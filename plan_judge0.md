# Code Emulator Implementation Plan
## Laravel + Livewire + Judge0 (Self-Hosted Docker)

---

## Table of Contents
1. [Architecture Overview](#1-architecture-overview)
2. [Prerequisites](#2-prerequisites)
3. [Step 1 — Set Up Judge0 via Docker](#3-step-1--set-up-judge0-via-docker)
4. [Step 2 — Configure Laravel](#4-step-2--configure-laravel)
5. [Step 3 — Build the Code Execution Service](#5-step-3--build-the-code-execution-service)
6. [Step 4 — Create the Livewire Component](#6-step-4--create-the-livewire-component)
7. [Step 5 — Build the Frontend (Monaco Editor)](#7-step-5--build-the-frontend-monaco-editor)
8. [Step 6 — Test Case Validation](#8-step-6--test-case-validation)
9. [Step 7 — Queue for Async Execution](#9-step-7--queue-for-async-execution)
10. [Folder Structure Summary](#10-folder-structure-summary)
11. [Common Issues & Fixes](#11-common-issues--fixes)

---

## 1. Architecture Overview

```
┌────────────────────────────────────────────────────────┐
│                      Browser                           │
│  Monaco Editor (code input) + Livewire (reactivity)    │
└─────────────────────┬──────────────────────────────────┘
                      │ wire:click → runCode()
                      ▼
┌────────────────────────────────────────────────────────┐
│                   Laravel Backend                       │
│  Livewire Component → CodeExecutionService             │
│  Laravel Queue (async job dispatch)                    │
└─────────────────────┬──────────────────────────────────┘
                      │ HTTP POST
                      ▼
┌────────────────────────────────────────────────────────┐
│              Judge0 (Docker Container)                 │
│  Sandboxed execution → Returns stdout/stderr/status    │
└────────────────────────────────────────────────────────┘
```

**Flow:**
1. User writes code in Monaco Editor
2. Livewire syncs code to backend on submit
3. Laravel dispatches a Job to the queue
4. Job calls Judge0 API (running locally on Docker)
5. Judge0 executes code in an isolated sandbox
6. Result is polled and returned to Livewire
7. Output is displayed to the user

---

## 2. Prerequisites

Make sure the following are installed on your server or local machine:

| Tool | Version | Check Command |
|------|---------|---------------|
| Docker | 20+ | `docker --version` |
| Docker Compose | 2+ | `docker compose version` |
| PHP | 8.1+ | `php --version` |
| Laravel | 10+ | `php artisan --version` |
| Livewire | 3+ | Check `composer.json` |
| Node.js | 18+ | `node --version` |

---

## 3. Step 1 — Set Up Judge0 via Docker

### 3.1 Create a Judge0 directory

```bash
mkdir judge0 && cd judge0
```

### 3.2 Download the official Judge0 docker-compose file

```bash
wget https://github.com/judge0/judge0/releases/download/v1.13.1/judge0-v1.13.1.zip
unzip judge0-v1.13.1.zip
cd judge0-v1.13.1
```

### 3.3 Set passwords in the config file

Open `judge0.conf` and set these two values:

```env
REDIS_PASSWORD=your_redis_password_here
POSTGRES_PASSWORD=your_postgres_password_here
```

> ⚠️ Use strong passwords even locally. These are required by Judge0.

### 3.4 Start Judge0 services

```bash
# Start the database and cache first
docker compose up -d db redis

# Wait ~10 seconds, then start the rest
sleep 10
docker compose up -d
```

### 3.5 Verify Judge0 is running

```bash
docker compose ps
```

You should see these containers running:
- `judge0-server`
- `judge0-workers`
- `judge0-db` (PostgreSQL)
- `judge0-redis`

### 3.6 Test the Judge0 API

```bash
curl -X POST http://localhost:2358/submissions \
  -H "Content-Type: application/json" \
  -d '{
    "source_code": "cHJpbnQoJ0hlbGxvIFdvcmxkJyk=",
    "language_id": 71,
    "stdin": ""
  }'
```

> `cHJpbnQoJ0hlbGxvIFdvcmxkJyk=` is `print('Hello World')` in base64.

You should receive a JSON response with a `token`. Use that token to check the result:

```bash
curl http://localhost:2358/submissions/{TOKEN}?base64_encoded=true
```

Judge0 is working if you see `"stdout": "SGVsbG8gV29ybGQ="` (Hello World in base64).

---

## 4. Step 2 — Configure Laravel

### 4.1 Add Judge0 config to `.env`

```env
JUDGE0_BASE_URL=http://localhost:2358
JUDGE0_AUTH_TOKEN=        # Leave empty if not using auth token
JUDGE0_MAX_POLL_ATTEMPTS=10
JUDGE0_POLL_INTERVAL=1    # seconds between each poll
```

### 4.2 Register config in `config/services.php`

```php
'judge0' => [
    'base_url'          => env('JUDGE0_BASE_URL', 'http://localhost:2358'),
    'auth_token'        => env('JUDGE0_AUTH_TOKEN', ''),
    'max_poll_attempts' => env('JUDGE0_MAX_POLL_ATTEMPTS', 10),
    'poll_interval'     => env('JUDGE0_POLL_INTERVAL', 1),
],
```

### 4.3 Set queue driver (use database for simplicity)

```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
```

---

## 5. Step 3 — Build the Code Execution Service

Create the service file:

```bash
mkdir -p app/Services
touch app/Services/CodeExecutionService.php
```

```php
<?php
// app/Services/CodeExecutionService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CodeExecutionService
{
    protected string $baseUrl;
    protected int $maxPollAttempts;
    protected int $pollInterval;

    // Judge0 language IDs — extend as needed
    public const LANGUAGES = [
        'python'     => 71,
        'javascript' => 63,
        'php'        => 68,
        'java'       => 62,
        'c'          => 50,
        'cpp'        => 54,
    ];

    public function __construct()
    {
        $this->baseUrl         = config('services.judge0.base_url');
        $this->maxPollAttempts = config('services.judge0.max_poll_attempts');
        $this->pollInterval    = config('services.judge0.poll_interval');
    }

    /**
     * Submit code and return the result.
     */
    public function run(string $code, string $language, string $stdin = ''): array
    {
        $languageId = self::LANGUAGES[$language] ?? 71;

        // Step 1: Submit the code
        $token = $this->submit($code, $languageId, $stdin);

        if (!$token) {
            return ['error' => 'Failed to submit code to Judge0.'];
        }

        // Step 2: Poll for result
        return $this->pollResult($token);
    }

    /**
     * Submit code to Judge0 and return the submission token.
     */
    protected function submit(string $code, int $languageId, string $stdin): ?string
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/submissions?base64_encoded=true&wait=false", [
                'source_code' => base64_encode($code),
                'language_id' => $languageId,
                'stdin'       => base64_encode($stdin),
            ]);

        if ($response->failed()) {
            Log::error('Judge0 submission failed', ['response' => $response->body()]);
            return null;
        }

        return $response->json('token');
    }

    /**
     * Poll Judge0 until the submission is complete.
     */
    protected function pollResult(string $token): array
    {
        for ($i = 0; $i < $this->maxPollAttempts; $i++) {
            sleep($this->pollInterval);

            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/submissions/{$token}?base64_encoded=true&fields=stdout,stderr,status,time,memory");

            if ($response->failed()) {
                continue;
            }

            $data       = $response->json();
            $statusId   = $data['status']['id'] ?? 0;

            // Status IDs 1 and 2 mean still processing
            if (in_array($statusId, [1, 2])) {
                continue;
            }

            return [
                'stdout' => $data['stdout'] ? base64_decode($data['stdout']) : '',
                'stderr' => $data['stderr'] ? base64_decode($data['stderr']) : '',
                'status' => $data['status']['description'] ?? 'Unknown',
                'time'   => $data['time'] ?? null,
                'memory' => $data['memory'] ?? null,
            ];
        }

        return ['error' => 'Execution timed out. Please try again.'];
    }

    protected function getHeaders(): array
    {
        $headers = ['Content-Type' => 'application/json'];

        if ($token = config('services.judge0.auth_token')) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        return $headers;
    }
}
```

---

## 6. Step 4 — Create the Livewire Component

```bash
php artisan make:livewire CodeEditor
```

```php
<?php
// app/Livewire/CodeEditor.php

namespace App\Livewire;

use App\Services\CodeExecutionService;
use Livewire\Component;

class CodeEditor extends Component
{
    public string $code        = '';
    public string $language    = 'python';
    public string $stdin       = '';
    public string $stdout      = '';
    public string $stderr      = '';
    public string $status      = '';
    public ?string $time       = null;
    public ?string $memory     = null;
    public bool $running       = false;
    public ?string $errorMsg   = null;

    // Starter code templates per language
    public array $starterCode = [
        'python'     => "# Write your Python code here\nprint('Hello, World!')",
        'javascript' => "// Write your JavaScript code here\nconsole.log('Hello, World!');",
        'php'        => "<?php\n// Write your PHP code here\necho 'Hello, World!';",
        'java'       => "public class Main {\n    public static void main(String[] args) {\n        System.out.println(\"Hello, World!\");\n    }\n}",
    ];

    public function mount(): void
    {
        $this->code = $this->starterCode[$this->language];
    }

    public function updatedLanguage(): void
    {
        $this->code   = $this->starterCode[$this->language] ?? '';
        $this->stdout = '';
        $this->stderr = '';
        $this->status = '';
    }

    public function runCode(): void
    {
        $this->running  = true;
        $this->stdout   = '';
        $this->stderr   = '';
        $this->errorMsg = null;

        $result = app(CodeExecutionService::class)->run(
            code:     $this->code,
            language: $this->language,
            stdin:    $this->stdin,
        );

        if (isset($result['error'])) {
            $this->errorMsg = $result['error'];
        } else {
            $this->stdout = $result['stdout'];
            $this->stderr = $result['stderr'];
            $this->status = $result['status'];
            $this->time   = $result['time'];
            $this->memory = $result['memory'];
        }

        $this->running = false;
    }

    public function render()
    {
        return view('livewire.code-editor');
    }
}
```

---

## 7. Step 5 — Build the Frontend (Monaco Editor)

```html
{{-- resources/views/livewire/code-editor.blade.php --}}

<div x-data="monacoEditor()" x-init="init()" class="w-full">

    {{-- Language Selector --}}
    <div class="flex items-center gap-4 mb-3">
        <label class="font-semibold text-sm">Language:</label>
        <select wire:model.live="language" class="border rounded px-3 py-1 text-sm">
            <option value="python">Python</option>
            <option value="javascript">JavaScript</option>
            <option value="php">PHP</option>
            <option value="java">Java</option>
            <option value="c">C</option>
            <option value="cpp">C++</option>
        </select>
    </div>

    {{-- Monaco Editor Container --}}
    <div id="monaco-editor" style="height: 400px; border: 1px solid #ccc; border-radius: 6px;"></div>

    {{-- Stdin Input --}}
    <div class="mt-3">
        <label class="block text-sm font-semibold mb-1">Standard Input (stdin):</label>
        <textarea
            wire:model="stdin"
            rows="2"
            placeholder="Optional input for your program..."
            class="w-full border rounded px-3 py-2 text-sm font-mono"
        ></textarea>
    </div>

    {{-- Run Button --}}
    <div class="mt-3">
        <button
            wire:click="runCode"
            wire:loading.attr="disabled"
            class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2 rounded disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="runCode">▶ Run Code</span>
            <span wire:loading wire:target="runCode">⏳ Running...</span>
        </button>
    </div>

    {{-- Error Message --}}
    @if($errorMsg)
        <div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
            ⚠️ {{ $errorMsg }}
        </div>
    @endif

    {{-- Output Panel --}}
    @if($status)
        <div class="mt-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-sm font-semibold">Status:</span>
                <span class="px-2 py-0.5 rounded text-xs font-bold
                    {{ $status === 'Accepted' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $status }}
                </span>
                @if($time)
                    <span class="text-xs text-gray-500">⏱ {{ $time }}s</span>
                @endif
                @if($memory)
                    <span class="text-xs text-gray-500">💾 {{ $memory }} KB</span>
                @endif
            </div>

            @if($stdout)
                <div>
                    <p class="text-sm font-semibold mb-1">Output:</p>
                    <pre class="bg-gray-900 text-green-400 rounded p-3 text-sm overflow-auto max-h-48">{{ $stdout }}</pre>
                </div>
            @endif

            @if($stderr)
                <div class="mt-2">
                    <p class="text-sm font-semibold mb-1 text-red-600">Errors:</p>
                    <pre class="bg-gray-900 text-red-400 rounded p-3 text-sm overflow-auto max-h-48">{{ $stderr }}</pre>
                </div>
            @endif
        </div>
    @endif
</div>

{{-- Monaco Editor Script --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>
<script>
function monacoEditor() {
    return {
        editor: null,

        init() {
            require.config({
                paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }
            });

            require(['vs/editor/editor.main'], () => {
                this.editor = monaco.editor.create(document.getElementById('monaco-editor'), {
                    value:     @js($code),
                    language:  @js($language),
                    theme:     'vs-dark',
                    fontSize:  14,
                    minimap:   { enabled: false },
                    scrollBeyondLastLine: false,
                    automaticLayout: true,
                });

                // Sync editor content to Livewire
                this.editor.onDidChangeModelContent(() => {
                    @this.set('code', this.editor.getValue());
                });
            });

            // Update editor language when Livewire language changes
            Livewire.on('language-changed', (lang) => {
                if (this.editor) {
                    const model = this.editor.getModel();
                    monaco.editor.setModelLanguage(model, lang);
                }
            });
        }
    }
}
</script>
@endpush
```

> Make sure your main layout includes `@stack('scripts')` before `</body>`.

---

## 8. Step 6 — Test Case Validation

For a quiz/exam app, you'll want to validate output against expected answers.

### 8.1 Database migration for test cases

```bash
php artisan make:migration create_test_cases_table
```

```php
// database/migrations/xxxx_create_test_cases_table.php
Schema::create('test_cases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('question_id')->constrained()->cascadeOnDelete();
    $table->text('stdin');
    $table->text('expected_output');
    $table->boolean('is_hidden')->default(false); // hidden = graded only
    $table->timestamps();
});
```

### 8.2 Run all test cases in CodeExecutionService

```php
// Add to CodeExecutionService.php
public function runTestCases(string $code, string $language, array $testCases): array
{
    $results = [];

    foreach ($testCases as $tc) {
        $result = $this->run($code, $language, $tc['stdin']);

        $passed = trim($result['stdout'] ?? '') === trim($tc['expected_output']);

        $results[] = [
            'stdin'           => $tc['stdin'],
            'expected'        => $tc['expected_output'],
            'actual'          => $result['stdout'] ?? '',
            'passed'          => $passed,
            'status'          => $result['status'] ?? 'Error',
        ];
    }

    return $results;
}
```

### 8.3 Show results in the Livewire component

```php
// In CodeEditor Livewire component
public array $testResults = [];

public function submitAnswer(): void
{
    $testCases      = $this->question->testCases->toArray();
    $this->testResults = app(CodeExecutionService::class)
        ->runTestCases($this->code, $this->language, $testCases);
}
```

---

## 9. Step 7 — Queue for Async Execution

For better UX, run code execution as a background job.

### 9.1 Create the job

```bash
php artisan make:job ExecuteCodeJob
```

```php
<?php
// app/Jobs/ExecuteCodeJob.php

namespace App\Jobs;

use App\Services\CodeExecutionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ExecuteCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly string $sessionKey,
        public readonly string $code,
        public readonly string $language,
        public readonly string $stdin = '',
    ) {}

    public function handle(CodeExecutionService $service): void
    {
        $result = $service->run($this->code, $this->language, $this->stdin);

        // Store result in cache so Livewire can poll it
        cache()->put($this->sessionKey, $result, now()->addMinutes(5));
    }
}
```

### 9.2 Update Livewire to dispatch job and poll

```php
// In CodeEditor.php Livewire component
use App\Jobs\ExecuteCodeJob;
use Illuminate\Support\Str;

public ?string $jobKey = null;

public function runCode(): void
{
    $this->running = true;
    $this->jobKey  = 'code_result_' . Str::uuid();

    ExecuteCodeJob::dispatch($this->jobKey, $this->code, $this->language, $this->stdin);
}

// Called by polling in the view
public function checkResult(): void
{
    if (!$this->jobKey) return;

    $result = cache()->get($this->jobKey);

    if ($result) {
        $this->stdout  = $result['stdout'] ?? '';
        $this->stderr  = $result['stderr'] ?? '';
        $this->status  = $result['status'] ?? '';
        $this->running = false;
        $this->jobKey  = null;
    }
}
```

```html
{{-- Add polling in Blade view --}}
<div wire:poll.1000ms="checkResult">
    ...
</div>
```

### 9.3 Start the queue worker

```bash
php artisan queue:work --tries=3 --timeout=30
```

> For production, use **Supervisor** to keep the worker running persistently.

---

## 10. Folder Structure Summary

```
app/
├── Jobs/
│   └── ExecuteCodeJob.php          ← Async job
├── Livewire/
│   └── CodeEditor.php              ← Livewire component
├── Services/
│   └── CodeExecutionService.php    ← Judge0 API wrapper
resources/
├── views/
│   └── livewire/
│       └── code-editor.blade.php   ← Editor UI + Monaco
judge0/
└── judge0-v1.13.1/
    ├── docker-compose.yml          ← Judge0 Docker setup
    └── judge0.conf                 ← Judge0 config
```

---

## 11. Common Issues & Fixes

| Problem | Cause | Fix |
|---------|-------|-----|
| `Connection refused` on port 2358 | Judge0 not running | Run `docker compose ps` and restart if needed |
| `base64 decode error` | Forgot to encode/decode | Always encode stdin/source_code, decode stdout/stderr |
| Execution always times out | Poll interval too short | Increase `JUDGE0_POLL_INTERVAL` and `JUDGE0_MAX_POLL_ATTEMPTS` |
| Monaco editor not syncing | Livewire overrides DOM | Use `x-data` Alpine.js wrapper as shown above |
| Queue jobs not running | Worker not started | Run `php artisan queue:work` in a separate terminal |
| Java/C++ submissions fail | Language ID wrong | Double-check Judge0 language IDs at `/languages` endpoint |

### Check available language IDs

```bash
curl http://localhost:2358/languages
```

---

*Document generated for Laravel + Livewire + Judge0 (Docker) integration.*

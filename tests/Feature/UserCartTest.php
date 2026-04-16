<?php

use App\Livewire\StudentCart;
use App\Models\Course;
use App\Models\User;
use App\Models\UserCart as UserCartModel;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('loads cart items on mount', function () {
    $course1 = Course::factory()->published()->create();
    $course2 = Course::factory()->published()->create();

    UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course1->id,
    ]);

    UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course2->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(StudentCart::class)
        ->assertSet('cartCount', 2)
        ->assertSet('cartItems', fn ($items) => count($items) === 2)
        ->assertSee($course1->title)
        ->assertSee($course2->title);
});

it('can remove first item from cart', function () {
    $course1 = Course::factory()->published()->create(['price' => 100000]);
    $course2 = Course::factory()->published()->create(['price' => 200000]);

    $item1 = UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course1->id,
    ]);

    $item2 = UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course2->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(StudentCart::class)
        ->assertSet('cartCount', 2)
        ->call('removeItem', $item1->id)
        ->assertNotified()
        ->assertSet('cartCount', 1)
        ->assertDontSee($course1->title)
        ->assertSee($course2->title);

    expect(UserCartModel::find($item1->id))->toBeNull();
});

it('can remove second item from cart after removing first', function () {
    $course1 = Course::factory()->published()->create(['price' => 100000]);
    $course2 = Course::factory()->published()->create(['price' => 200000]);
    $course3 = Course::factory()->published()->create(['price' => 150000]);

    $item1 = UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course1->id,
    ]);

    $item2 = UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course2->id,
    ]);

    $item3 = UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course3->id,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(StudentCart::class)
        ->assertSet('cartCount', 3);

    // Remove first item
    $component->call('removeItem', $item1->id)
        ->assertSet('cartCount', 2)
        ->assertDontSee($course1->title);

    // Remove second item - THIS IS WHERE THE BUG WAS
    $component->call('removeItem', $item2->id)
        ->assertSet('cartCount', 1)
        ->assertDontSee($course2->title)
        ->assertSee($course3->title);

    expect(UserCartModel::find($item1->id))->toBeNull();
    expect(UserCartModel::find($item2->id))->toBeNull();
    expect(UserCartModel::find($item3->id))->not->toBeNull();
});

it('can remove item from cart', function () {
    $course = Course::factory()->published()->create();

    $item = UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(StudentCart::class)
        ->call('removeItem', $item->id);

    $sessionCart = session('cart', []);
    expect(count($sessionCart))->toBe(0);
});

it('calculates correct total price', function () {
    $course1 = Course::factory()->published()->create(['price' => 100000]);
    $course2 = Course::factory()->published()->create(['price' => 200000]);

    UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course1->id,
    ]);

    UserCartModel::factory()->create([
        'user_id' => $this->user->id,
        'course_id' => $course2->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(StudentCart::class)
        ->assertSet('totalPrice', 300000);
});

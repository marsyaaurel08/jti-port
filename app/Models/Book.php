<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'year',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }
}

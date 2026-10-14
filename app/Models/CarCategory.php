<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarCategory extends Model
{
    protected $fillable = ['brand', 'name', 'slug', 'description', 'is_visible'];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
        ];
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'category_id');
    }
}

<?php

namespace App\Models;

use App\Enums\CarStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = ['category_id', 'code', 'slug', 'name', 'year', 'color', 'specs', 'price', 'status', 'is_visible'];

    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'price' => 'decimal:0',
            'year' => 'integer',
            'status' => CarStatus::class,
            'is_visible' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

<?php

namespace App\Models;

use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'type', 'description', 'allow_booking', 'is_visible'];

    protected function casts(): array
    {
        return [
            'type' => ServiceType::class,
            'allow_booking' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

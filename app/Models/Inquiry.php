<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    protected $fillable = ['customer_id', 'car_id', 'contact_name', 'contact_phone', 'contact_email', 'content', 'source', 'assigned_to', 'status', 'access_version', 'internal_note', 'close_result'];

    protected $hidden = ['internal_note'];

    protected function casts(): array
    {
        return [
            'status' => InquiryStatus::class,
            'access_version' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

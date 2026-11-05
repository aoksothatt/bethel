<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'shirt_number',
        'jersey_size',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'shirt_number' => 'integer',
    ];

    /**
     * Mutator to normalize payment_status values
     */
    public function setPaymentStatusAttribute($value): void
    {
        $normalized = strtolower(str_replace(' ', '_', trim((string)$value)));
        if (in_array($normalized, ['paid', 'unpaid', 'not_confirmed'])) {
            $this->attributes['payment_status'] = $normalized;
        } else {
            $this->attributes['payment_status'] = 'unpaid';
        }
    }
}

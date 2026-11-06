<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    /**
     * Supported jersey sizes.
     */
    public const SIZES = ['S', 'M', 'L', 'XL', 'XXL'];

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
     * Mutator to normalize jersey_size values to uppercase.
     */
    public function setJerseySizeAttribute($value): void
    {
        $this->attributes['jersey_size'] = strtoupper(trim((string)$value));
    }

    /**
     * Mutator to normalize payment_status values
     */
    public function setPaymentStatusAttribute($value): void
    {
        $raw = strtolower(trim((string)$value));
        if (in_array($raw, ['50% paid', '50%_paid', '50_paid', '50paid', '50_percent_paid', 'half_paid', 'half paid', 'halfpaid', '50%'])) {
            $this->attributes['payment_status'] = 'half_paid';
            return;
        }

        $normalized = str_replace(' ', '_', $raw);
        if (in_array($normalized, ['paid', 'half_paid', 'unpaid', 'not_confirmed'])) {
            $this->attributes['payment_status'] = $normalized;
        } else {
            $this->attributes['payment_status'] = 'unpaid';
        }
    }
}

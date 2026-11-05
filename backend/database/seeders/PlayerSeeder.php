<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid duplicate imports if records already exist
        if (Player::count() > 0) {
            return;
        }

        $initialPlayers = [
            ['name' => 'CHHAY', 'shirt_number' => 8, 'jersey_size' => 'L', 'payment_status' => 'paid', 'notes' => null],
            ['name' => 'HOUR', 'shirt_number' => 3, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'JUMING', 'shirt_number' => 7, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'MESSING', 'shirt_number' => 10, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'SUII NANN', 'shirt_number' => 1, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => 'GK'],
            ['name' => 'Sak', 'shirt_number' => 8, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'THEARA', 'shirt_number' => 6, 'jersey_size' => 'L', 'payment_status' => 'paid', 'notes' => null],
            ['name' => 'Xorng', 'shirt_number' => 9, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'PHA LEY', 'shirt_number' => 67, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'Pedri', 'shirt_number' => 21, 'jersey_size' => 'XL', 'payment_status' => 'paid', 'notes' => null],
            ['name' => 'NITH', 'shirt_number' => 19, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'Long', 'shirt_number' => 17, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'Panha', 'shirt_number' => 27, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'PrimeHeng', 'shirt_number' => 15, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'Sothatt', 'shirt_number' => 80, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'SOKKUN', 'shirt_number' => 18, 'jersey_size' => 'XL', 'payment_status' => 'paid', 'notes' => null],
            ['name' => 'Yimchamroeun', 'shirt_number' => 19, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'PiNy', 'shirt_number' => 10, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'SEY', 'shirt_number' => 19, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'CHONG', 'shirt_number' => 11, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'BANOO', 'shirt_number' => 29, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'HENG', 'shirt_number' => 12, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'JING ASSASIN', 'shirt_number' => 10, 'jersey_size' => 'XL', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'YTT', 'shirt_number' => 19, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'HOR', 'shirt_number' => 0, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'Meng Kruy', 'shirt_number' => 23, 'jersey_size' => 'L', 'payment_status' => 'unpaid', 'notes' => null],
            ['name' => 'Srey Nith', 'shirt_number' => 19, 'jersey_size' => 'M', 'payment_status' => 'unpaid', 'notes' => 'អាវគេចូលរួម'],
            ['name' => 'PHOUNG', 'shirt_number' => 23, 'jersey_size' => 'M', 'payment_status' => 'not_confirmed', 'notes' => null],
        ];

        foreach ($initialPlayers as $data) {
            Player::create($data);
        }
    }
}

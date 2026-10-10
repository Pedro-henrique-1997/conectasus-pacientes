<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HealthUnit;
use Illuminate\Support\Str;

class HealthUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unidade Central', 'city' => 'Rio de Janeiro'],
            ['name' => 'Unidade Norte', 'city' => 'Niterói'],
            ['name' => 'Unidade Sul', 'city' => 'São Gonçalo'],
        ];

        foreach ($units as $unit) {
            HealthUnit::create([
                'uuid' => (string) Str::uuid(),
                'name' => $unit['name'],
                'city' => $unit['city'],
            ]);
        }
    }
}

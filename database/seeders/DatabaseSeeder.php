<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ROLES
        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'operator'],
            ['name' => 'manajer'],
            ['name' => 'guest'],
        ]);

        // USERS
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role_id' => 1
        ]);

        User::factory()->create([
            'role_id' => 2
        ]);

        User::factory()->create([
            'role_id' => 3
        ]);

        // SUPPLIERS
        DB::table('suppliers')->insert([
            [
                'name' => 'UD Laut Segar',
                'phone_number' => '081234567890',
                'address' => 'Jl. Pelabuhan No. 12, Samarinda',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CV Samudra Nusantara',
                'phone_number' => '085612345678',
                'address' => 'Jl. Raya Pantai, Balikpapan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Ikan Jaya',
                'phone_number' => '082112345678',
                'address' => 'Jl. Bahari No. 5, Bontang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. FISH
        $fishList = [
            ['name' => 'Kerapu Macan'],
            ['name' => 'Kerapu Tikus'],
            ['name' => 'Kerapu Cantang'],
            ['name' => 'Kerapu Lumpur'],
            ['name' => 'Kerapu Sunu'],
            ['name' => 'Kerapu Bebek'],
            ['name' => 'Kerapu Baramundi'],
            ['name' => 'Kerapu Merah'],
        ];

        $fishData = [];
        foreach ($fishList as $item) {
            $fishData[] = [
                'name' => $item['name'],
                'description' => "Jenis {$item['name']} yang populer di pasar lokal.",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('fish')->insert($fishData);

        // 3. TRANSACTIONS
        $fishIds = DB::table('fish')->pluck('id');
        $supplierIds = DB::table('suppliers')->pluck('id');

        $transactions = [];
        foreach ($fishIds as $fishId) {
            $numTransactions = rand(1, 2); // 1-2 transaksi per ikan
            for ($i = 0; $i < $numTransactions; $i++) {
                $quantity = rand(5, 50);
                $price = rand(30000, 70000);
                $datetime = Carbon::now()->subDays(rand(0, 30))->addHours(rand(1, 12));

                $transactions[] = [
                    'user_id' => 2,
                    'supplier_id' => $supplierIds->random(),
                    'fish_id' => $fishId,
                    'origin' => 'Sungai Mahakam',
                    'quantity' => $quantity,
                    'price_per_unit' => $price,
                    'total_price' => $quantity * $price,
                    'entry_datetime' => $datetime,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('transactions')->insert($transactions);
    }
}

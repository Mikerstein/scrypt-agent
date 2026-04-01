<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'uchedivine65@gmail.com'],
            ['name' => 'Admin One', 'password' => bcrypt('Sniper6.5')]
        );

        User::firstOrCreate(
            ['email' => 'mikazacrypto@gmail.com'],
            ['name' => 'Admin Two', 'password' => bcrypt('Ogugua1998....')]
        );

        $this->call(ScryptDataSeeder::class);
    }
}
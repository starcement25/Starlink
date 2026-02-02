<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['id' => 1],
            [
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'phone' => '1000000001',
            'role' => 5,
            'status'=> 1,
            'email_verified_at' => null,
            'password' =>  Hash::make('12345'),
        ]);
    }
}

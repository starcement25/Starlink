<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::updateOrCreate(
            ['id' => 1],
            [
            'role_name' => 'Technical Engineer',
        ]);
        Role::updateOrCreate(
            ['id' => 2],
            [
            'role_name' => 'Dealer',
        ]);
        Role::updateOrCreate(
            ['id' => 3],
            [
            'role_name' => 'Mason',
        ]);
        Role::updateOrCreate(
            ['id' => 4],
            [
            'role_name' => 'RSSD',
        ]);
        Role::updateOrCreate(
            ['id' => 5],
            [
            'role_name' => 'Admin',
        ]);
    }
}

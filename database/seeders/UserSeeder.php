<?php

namespace Database\Seeders;

use App\Models\CompanyCode;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_role = Role::create(['name'=> 'admin']);
        $user_role = Role::create(['name'=> 'user']);

    }
}

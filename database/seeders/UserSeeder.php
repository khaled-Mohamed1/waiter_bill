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

//        $company_code = CompanyCode::create([
//            'company_code' => 'C_000000'
//        ]);
//
//
//        $admin = User::create([
//            'username' => 'admin',
//            'email' => 'admin@admin.com',
//            'phone_number' => '0599123123',
//            'password' => bcrypt('12341234'),
//            'un_password' => '12341234',
//            'address' => 'admin',
//            'company_id' => $company_code->id
//        ]);
//
//        $admin->assignRole($admin_role);
    }
}

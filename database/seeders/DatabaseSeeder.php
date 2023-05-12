<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::beginTransaction();
        try {
            Permission::create(['name' => 'manage users']);
            Permission::create(['name' => 'manage roles']);
            Permission::create(['name' => 'manage harvests']);
            Permission::create(['name' => 'manage categories']);
            Role::create(['name' => 'Admin'])->syncPermissions([1, 2, 3, 4]);
            Role::create(['name' => 'Petani'])->syncPermissions([3]);
            Role::create(['name' => 'Pengepul']);
            User::create([
                'name'      => 'Admin',
                'email'     => 'admin@admin.com',
                'password'  => bcrypt('12345678'),

            ])->assignRole('Admin');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}

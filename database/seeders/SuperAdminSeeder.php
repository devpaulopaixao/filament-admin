<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::withTrashed()->updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'admin@admin.com')],
            [
                'name'              => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'password'          => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
                'deleted_at'        => null,
            ]
        );

        $user->assignRole('super_admin');
    }
}

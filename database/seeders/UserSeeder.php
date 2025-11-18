<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'jbhasan@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create Vendors
        $vendors = [
            [
                'name' => 'TechStore Vendor',
                'email' => 'vendor1@ecom.com',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
            ],
            [
                'name' => 'FashionHub Vendor',
                'email' => 'vendor2@ecom.com',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
            ],
            [
                'name' => 'HomeDecor Vendor',
                'email' => 'vendor3@ecom.com',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
            ],
        ];

        foreach ($vendors as $vendor) {
            User::create($vendor);
        }

        // Create Customers
        $customers = [
            [
                'name' => 'Hasan Sayeed',
                'email' => 'jb_hasan@live.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        ];

        foreach ($customers as $customer) {
            User::create($customer);
        }

        // Create additional random customers
        User::factory()->count(8)->create(['role' => 'customer']);
    }
}

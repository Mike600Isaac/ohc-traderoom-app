<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // 1. Create Carrick Jones (Trader Path)
    $carrick = User::create([
        'kajabi_user_id' => '202588123',
        'first_name'     => 'Carrick',
        'last_name'      => 'Jones',
        'email'          => 'carrickjones.cj@gmail.com',
        'password'       => Hash::make('12345678'),
        'current_path'   => 'Trader',
        'status'         => 'Active',
    ]);

    // 2. Create Sarah Investor (Standalone Product User)
    $sarah = User::create([
        'kajabi_user_id' => '202588456',
        'first_name'     => 'Sarah',
        'last_name'      => 'Investor',
        'email'          => 'sarah@example.com',
        'password'       => Hash::make('12345678'),
        'current_path'   => 'Fixed Income', 
        'status'         => 'Active',
    ]);

    // 3. Add a specific entitlement for Sarah 
    // This matches the title in your CourseController catalog
    $sarah->entitlements()->create([
        'kajabi_offer_id' => 'offer_999',
        'offer_name'      => 'Standalone Fixed Income Offer',
        'product_name'    => 'Fixed Income Analysis', 
        'status'          => 'Active',
    ]);
}
}

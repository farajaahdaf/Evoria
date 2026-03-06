<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OrganizerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Organizer
        $organizer = User::create([
            'name' => 'Event Organizer One',
            'email' => 'organizer@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
        ]);

        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'company_name' => 'EO One Company',
            'description' => 'We organize the best tech events',
            'status' => 'verified',
        ]);

        // Attendee
        User::create([
            'name' => 'John Attendee',
            'email' => 'attendee@example.com',
            'password' => Hash::make('password'),
            'role' => 'attendee',
        ]);
    }
}


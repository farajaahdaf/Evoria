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
        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::updateOrCreate([
            'email' => 'attendee@example.com',
        ], [
            'name' => 'John Attendee',
            'password' => Hash::make('password'),
            'role' => 'attendee',
        ]);

        $organizers = [
            [
                'name' => 'Evoria Spotlight',
                'email' => 'organizer@example.com',
                'company_name' => 'Evoria Spotlight',
                'description' => 'Promotor utama Evoria untuk konser spesial, showcase artis, dan event skala arena.',
            ],
            [
                'name' => 'Nusantara Live',
                'email' => 'nusantara.live@example.com',
                'company_name' => 'Nusantara Live Entertainment',
                'description' => 'Mengelola konser musik, festival lintas genre, dan showcase artis nasional.',
            ],
            [
                'name' => 'Arena Sports ID',
                'email' => 'arena.sports@example.com',
                'company_name' => 'Arena Sports Indonesia',
                'description' => 'Berpengalaman mengelola event stadion, festival besar, dan activations berskala massal.',
            ],
            [
                'name' => 'Creative Hub Studio',
                'email' => 'creative.hub@example.com',
                'company_name' => 'Creative Hub Studio',
                'description' => 'Fokus pada pertunjukan kreatif, musical show, dan konser intimate experience.',
            ],
            [
                'name' => 'Edu Summit Asia',
                'email' => 'edu.summit@example.com',
                'company_name' => 'Edu Summit Asia',
                'description' => 'Unit event yang menangani konser kampus, K-pop showcase, dan special tour stop.',
            ],
            [
                'name' => 'Sound Rhythm Live',
                'email' => 'sound.rhythm@example.com',
                'company_name' => 'Sound Rhythm Live',
                'description' => 'Promotor konser internasional dengan fokus pada tour Asia dan venue indoor premium.',
            ],
            [
                'name' => 'Color Asia Live',
                'email' => 'colorasia.live@example.com',
                'company_name' => 'Color Asia Live',
                'description' => 'Promotor konser pop internasional, gala orchestra, dan nostalgia show.',
            ],
            [
                'name' => 'Harmony Stage',
                'email' => 'harmony.stage@example.com',
                'company_name' => 'Harmony Stage Indonesia',
                'description' => 'Mengelola festival musik, konser pop culture, dan event family entertainment.',
            ],
        ];

        foreach ($organizers as $organizerData) {
            $organizer = User::updateOrCreate([
                'email' => $organizerData['email'],
            ], [
                'name' => $organizerData['name'],
                'password' => Hash::make('password'),
                'role' => 'organizer',
            ]);

            OrganizerProfile::updateOrCreate([
                'user_id' => $organizer->id,
            ], [
                'company_name' => $organizerData['company_name'],
                'description' => $organizerData['description'],
                'status' => 'verified',
            ]);
        }
    }
}

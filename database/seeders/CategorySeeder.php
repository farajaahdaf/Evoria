<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Music Concert',
            'Tech Conference',
            'Workshop',
            'Sports',
            'Art Exhibition',
        ];

        foreach ($categories as $category) {
            EventCategory::updateOrCreate(
                ['slug' => Str::slug($category)],
                ['name' => $category]
            );
        }
    }
}

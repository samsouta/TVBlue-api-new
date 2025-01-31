<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            'Feet',
            
             
            
        ];

        foreach ($tags as $tagName) {
            $existingTag = Tag::where('name', $tagName)->first();
            
            if ($existingTag) {
                $this->command->error("Tag '$tagName' already exists in database!");
                continue;
            }

            Tag::create(['name' => $tagName]);
            $this->command->info("Tag '$tagName' created successfully!");
        }

        $this->command->info('Tag seeding process completed!');
    }
}

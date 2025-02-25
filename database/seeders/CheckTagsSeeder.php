<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class CheckTagsSeeder extends Seeder
{
    public function run()
    {
       
        
        $requiredTags = [
            '51 News',
            'Shenzhen Vanke Real Estate Company Jing Qili',
            'Creampie Real Estate Sales',
            'Contrast Property Sales',
            'Real estate sales sex video',
            'Office unspoken rules',
        ];


        $foundTagIds = [];
        $addedTags = [];

        foreach ($requiredTags as $tagName) {
            $tag = Tag::where('name', $tagName)->first();
            
            if ($tag) {
                $foundTagIds[] = $tag->id;
                $this->command->info("Found Existing Tag: '{$tagName}' with ID: {$tag->id}");
            } else {
                // Create missing tag
                $newTag = Tag::create(['name' => $tagName]);
                $foundTagIds[] = $newTag->id;
                $addedTags[] = $tagName;
                $this->command->warn("Created New Tag: '{$tagName}' with ID: {$newTag->id}");
            }
        }

        if (!empty($addedTags)) {
            $this->command->warn("\nNewly Added Tags:");
            foreach ($addedTags as $tagName) {
                $this->command->warn("- {$tagName}");
            }
        }

        $this->command->info("\nComplete Tag IDs Array:");
        $this->command->info('"tag_ids": [' . implode(',', $foundTagIds) . ']');
    }
}

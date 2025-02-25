<?php

namespace Database\Seeders;

use App\Models\Actress;
use Illuminate\Database\Seeder;

class ActressSeeder extends Seeder
{
    public function run()
    {
        $actresses = [
            [
                'name' => 'ComerZZ',
                'description' => 'null',
                'image_url' => 'https://ei.phncdn.com/videos/201904/16/218563081/original/(m=eGNdHgaaaa)(mh=iuDy2VrUAD87RNOU)14.jpg',
                'age' => null,
                'nationality' => 'usa',
                'birth_date' => '1999-1-1'
            ],
          
           
           
            // ... other actresses data
        ];

        $addedIds = [];
        $skippedNames = [];

        foreach ($actresses as $actress) {
            $exists = Actress::where('name', $actress['name'])->first();

            if ($exists) {
                $skippedNames[] = $actress['name'];
                $this->command->warn("Skipping: Actress '{$actress['name']}' already exists with ID: {$exists->id}");
                continue;
            }

            $newActress = Actress::create($actress);
            $addedIds[] = $newActress->id;
            $this->command->info("Created: Actress '{$actress['name']}' with ID: {$newActress->id}");
        }

        if (!empty($skippedNames)) {
            $this->command->warn("\nSkipped Actresses:");
            foreach ($skippedNames as $name) {
                $this->command->warn("- {$name}");
            }
        }

        $this->command->info("\nNewly Added Actress IDs:");
        $this->command->info('"actress_ids": [' . implode(',', $addedIds) . ']');
        
        $this->command->info('Actresses seeding completed!');
    }
}

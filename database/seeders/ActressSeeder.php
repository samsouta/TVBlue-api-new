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
                'name' => 'LinaMigurtt',
                'description' => 'null',
                'image_url' => 'https://img.telemetr.io/c/1VV0z0/5217445169769595143?ty=l',
                'age' => 25,
                'nationality' => 'American',
                'birth_date' => '1999-1-1'
            ],
            // ... other actresses data
        ];

        foreach ($actresses as $actress) {
            $exists = Actress::where('name', $actress['name'])->exists();

            if ($exists) {
                $this->command->warn("Skipping: Actress '{$actress['name']}' already exists");
                continue;
            }

            Actress::create($actress);
            $this->command->info("Created: Actress '{$actress['name']}' added successfully");
        }

        $this->command->info('Actresses seeding completed!');
    }
}

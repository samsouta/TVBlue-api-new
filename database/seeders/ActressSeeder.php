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
                'name' => 'noratheo',
                'description' => 'null',
                'image_url' => 'https://www.pornkut.com/get_file/0/133cf5eee5a3f7d96655057cf1f643d8fbdbf90f28/65000/65356/screenshots/1.jpg/',
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

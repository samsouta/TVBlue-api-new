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
                'name' => 'Sarina Momonaga',
                'description' => '88 H - 56 - 85',
                'image_url' => 'https://cdn.avfever.net/images/acctress/53844.jpg',
                'age' => null,
                'nationality' => 'japanese',
                'birth_date' => '1990-01-01'
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

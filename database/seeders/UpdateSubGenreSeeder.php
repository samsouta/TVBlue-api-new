<?php

namespace Database\Seeders;

use App\Models\SubGenre;
use Illuminate\Database\Seeder;

class UpdateSubGenreSeeder extends Seeder
{
    public function run()
    {
        $subGenreId = 40;
        $newName = 'Chinese AV'; // Replace with the desired new name

        $subGenre = SubGenre::find($subGenreId);
        
        if ($subGenre) {
            $oldName = $subGenre->name;
            $subGenre->update(['name' => $newName]);
            $this->command->info("Updated SubGenre ID {$subGenreId}: {$oldName} → {$newName}");
        } else {
            $this->command->error("SubGenre with ID {$subGenreId} not found!");
        }

        $this->command->info('SubGenre update completed!');
    }
}

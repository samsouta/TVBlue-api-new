<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genre::create([
            'name' => 'Watch Jav',
            'description' => 'Censored , Recent Update,New Release,Uncensored,Uncensored Leaked,VR,Hot actresses,Trending,Most viewed today,Most viewed by week,Most viewed by month',
        ]);

        Genre::create([
            'name' => 'List',
            'description' => 'Genres,Makers,Actresses,Series',
        ]);

        Genre::create([
            'name' => 'Amateur  ',
            'description' => 'SIRO,LUXU,200GANA,PRESTIGE PREMIUM,ORECO,S-CUTE,ARA,390JAC,328HMDN',
        ]);

        Genre::create([
            'name' => 'Uncensored',
            'description' => 'Uncensored Leaked,FC2,HEYZO,Tokyo-Hot,1pondo,Caribbeancom,Caribbeancompr,10musume,pacopacomama,Gachinco,XXX-AV,C0930,H0930,H4610',
        ]);
        Genre::create([
            'name' => 'Actress',
            'description' => 'jav , russia, asia, myanmar',
        ]);

        
    }
}

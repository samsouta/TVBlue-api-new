<?php

namespace Database\Seeders;

use App\Models\SubGenre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $subGenres = [
        //     10 => [
        //         'Censored',
        //         'Recent Update',
        //         'New Release',
        //         'Uncensored',
        //         'Uncensored Leaked',
        //         'Hot actresses',
        //         'Most viewed today',
        //         'Top Rated',
        //         'Popular',
        //     ],
        //     11 => [
        //         'Genres',
        //         'Makers',
        //         'Actresses',
        //         'Series',
        //         'Short',
        //     ],
        //     12 => [
        //         'SIRO', 'S-CUTE',
        //         'Creamy spot', 'Naomii', 
        //         'LinaMigurrt', 'HongKong Doll', 
        //         'Big Ass' , 'beautiful pussy'
        //     ],
        //     13 => [
        //         'Uncensored Leaked', 'FC2',
        //         'HEYZO ', 'Tokyo-Hot',
        //         '1pondo ', 'Caribbeancom',
        //         'Caribbeancompr', '10musume',
        //         'pacopacomama ', 'Gachinco',
        //         'XXX-AV ', 'C0930','H0930 ', 'H4610',

        // ],
        // ];

        // foreach ($subGenres as $genreId => $names) {
        //     foreach ($names as $name) {
        //         SubGenre::create([
        //             'genre_id' => $genreId,
        //             'name' => $name,
        //         ]);
        //     }
        // }

        SubGenre::create([
            'genre_id' => 12,
            'name' => 'chinese',
        ]);
    }
}

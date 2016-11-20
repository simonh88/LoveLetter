<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**Ajout des cartes, il faudrait après mettre les images**/
        DB::table('cartes')->insert([
            'nom' => 'Princess',
            'valeur' => 8
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Countess',
            'valeur' => 7
        ]);
        DB::table('cartes')->insert([
            'nom' => 'King',
            'valeur' => 6
        ]);
        //Deux princes
        DB::table('cartes')->insert([
            'nom' => 'Prince',
            'valeur' => 5
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Prince',
            'valeur' => 5
        ]);
        //2 Handmaid
        DB::table('cartes')->insert([
            'nom' => 'Handmaid',
            'valeur' => 4
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Handmaid',
            'valeur' => 4
        ]);
        //2 Barons
        DB::table('cartes')->insert([
            'nom' => 'Baron',
            'valeur' => 3
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Baron',
            'valeur' => 3
        ]);
        //2 Priest
        DB::table('cartes')->insert([
            'nom' => 'Priest',
            'valeur' => 2
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Priest',
            'valeur' => 2
        ]);
        //5 Guard
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1
        ]);

        \App\Salon::creationSalon(2);
        \App\Salon::creationSalon(3);
        \App\Salon::creationSalon(4);
        \App\Salon::creationSalon(4);
    }
}

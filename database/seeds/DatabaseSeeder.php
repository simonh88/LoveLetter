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
        // $this->call(UsersTableSeeder::class);

        /**Ajout des cartes, il faudrait après mettre les images**/
        DB::table('cartes')->insert([
            'nom' => 'Princess',
            'valeur' => 8,
            'image' => 'database/img/princess.jpg'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Countess',
            'valeur' => 7,
            'image' => 'database/img/countess.png'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'King',
            'valeur' => 6,
            'image' => 'database/img/king.jpg'
        ]);
        //Deux princes
        DB::table('cartes')->insert([
            'nom' => 'Prince',
            'valeur' => 5,
            'image' => 'database/img/prince.jpg'

        ]);
        DB::table('cartes')->insert([
            'nom' => 'Prince',
            'valeur' => 5,
            'image' => 'database/img/prince.jpg'
        ]);
        //2 Handmaid
        DB::table('cartes')->insert([
            'nom' => 'Handmaid',
            'valeur' => 4,
            'image' => 'database/img/handmaid.png'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Handmaid',
            'valeur' => 4,
            'image' => 'database/img/handmaid.png'
        ]);
        //2 Barons
        DB::table('cartes')->insert([
            'nom' => 'Baron',
            'valeur' => 3,
            'image' => 'database/img/baron.jpg'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Baron',
            'valeur' => 3,
            'image' => 'database/img/baron.jpg'
        ]);
        //2 Priest
        DB::table('cartes')->insert([
            'nom' => 'Priest',
            'valeur' => 2,
            'image' => 'database/img/priest.jpg'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Priest',
            'valeur' => 2,
            'image' => 'database/img/priest.jpg'
        ]);
        //5 Guard
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1,
            'image' => 'database/img/guard.jpg'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1,
            'image' => 'database/img/guard.jpg'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1,
            'image' => 'database/img/guard.jpg'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1,
            'image' => 'database/img/guard.jpg'
        ]);
        DB::table('cartes')->insert([
            'nom' => 'Guard',
            'valeur' => 1,
            'image' => 'database/img/guard.jpg'
        ]);
    }
}

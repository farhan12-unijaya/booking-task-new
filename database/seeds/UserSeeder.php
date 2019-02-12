<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
            'name' => 'Sistem Administrator',
            'username' => 'superadmin',
            'password' => bcrypt('password'),
            'email' => env('MAIL_FROM_ADDRESS'),
            'phone' => '',
            'user_type_id' => 1,
            'user_status_id' => 1
        ])->assignRole('admin');
<<<<<<< refs/remotes/origin/master
        
        App\User::create([
            'name' => 'Pemohon',
            'username' => 'pemohon1',
            'password' => bcrypt('password'),
            'email' => 'aiyub.unijaya@gmail.com',
            'phone' => '',
            'user_type_id' => 21,
            'user_status_id' => 21
        ])->assignRole('pemohon');
=======


        App\User::create([
            'name' => 'Pemohon Booking',
            'username' => 'pemohon',
            'password' => bcrypt('password'),
            'email' => env('MAIL_FROM_ADDRESS'),
            'phone' => '',
            'user_type_id' => 1,
            'user_status_id' => 1
        ])->assignRole('pemohon');


>>>>>>> Update Version 1.1
    }



    
    
}

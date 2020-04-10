<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addMedicalDefaultUser();
    }

    private function addMedicalDefaultUser(){
        $user = [
            'status'             => 'A', 
            'name'               => 'USER TEST', 
            'email'              => 'md.test@gmail.com',
            'cellphone'          => '+1 999 999 999',
            'password'           => hash('sha256', 'pplcert'),
            'description'        => 'Auto generate only for tests ... ',
            'verification_level' => '_TEST_',
            'verified'           => 1,
            'number_of_reviews'  => 0,
            'created_at'         => '2020-04-10 13:00:00',
            'updated_at'         => '2020-04-10 13:00:00',
        ];


        $newUser = new \App\Entities\User($user);
        $newUser->save();

        echo "- OK! - User Deafult created \n\n";
    }

}

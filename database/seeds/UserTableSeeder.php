<?php

use Illuminate\Database\Seeder;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $member = User::firstOrNew([
        		'email' => 'ariel@example.com'
        	]);

        $member->first_name = 'Ariel';
        $member->middle_name = 'Cacanog';
        $member->last_name = 'Magbanua';
        $member->address = '8th Avenue, Bangoy Street, Brgy. Tomas Monteverde, Agdao, Davao City';
        $member->birth_date = '1988-04-23';
        $member->account_type = 2;
        $member->password = bcrypt('password');
        $member->save();

        $admin = User::firstOrNew([
        		'email' => 'admin@example.com'
        	]);

        $admin->first_name = 'John';
        $admin->middle_name = 'Amazing';
        $admin->last_name = 'Doe';
        $admin->address = '8th Avenue, Bangoy Street, Brgy. Tomas Monteverde, Agdao, Davao City';
        $admin->birth_date = '1988-04-23';
        $admin->account_type = 1;
        $admin->password = bcrypt('password');
        $admin->save();

        //seeder for random members
        factory(User::class, 100)->create();
    }
}

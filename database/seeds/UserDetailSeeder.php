<?php
    
    use Illuminate\Database\Seeder;
    
    class UserDetailSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            \App\Core\Dao\SDB::table('users_detail')->insert([
                                                          array(
                                                              'user_id' => '1',
                                                              'gender'=>'1',
                                                          )
                                                      ]);
        }
    }

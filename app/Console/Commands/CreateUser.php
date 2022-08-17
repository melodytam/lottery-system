<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Command;

use App\Models\User;
use Carbon\Carbon;
use DB;

use App\NameGenerator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'mockup:createUser {username?}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command for generating user';

    /**
     * Mock up data.
     *
     * @var array
     */
    /**
     * Execute the console command.
     *
     * This command should create a User document in database.
     *
     * @see App\Models\User
     * @return mixed
     */
    public function handle()
    {   
        $this->info("User Generation");

        $username = $this->argument('username');
        while ($username === NULL || empty($username) || User::where('username', $username)->exists()) {
            $username = $this->ask('Enter the username (Required)');
            if (User::where('username', $username)->exists()) {
                $this->error("$username already exists");
                $username = NULL;
            }
        }

        $password = NULL;
        while ($password === NULL || empty($password)) {
            $password = $this->secret('Enter the password (Required)');
            if ($password === NULL || empty($password)) {
                $this->error("$password can not be empty");
                $password = NULL;
            }
        }

        $email = $this->ask('Enter the email address');
        
        // Initial User
        $users = [
            [
                'username' => $username,
                'password' => Hash::make($password),
                'email' => $email
            ]
        ];


        DB::beginTransaction();
        try
        {
            foreach($users as $user) {
                // Create User
                $count = User::where('username', $user['username'])->count();
                if($count === 0) {
                    $model = User::create($user);
                    $model_id = $model->id;
                    $this->info('User [' . $user['username'] . '] created');
                } else {
                    $model = User::where('username', $user['username'])->first();
                    $model_id = $model->id;
                    $this->info('User [' . $user['username'] . '] existed, skipped');
                    return;
                }
            }
            
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            echo $e->getMessage();
        }
    }
    
}

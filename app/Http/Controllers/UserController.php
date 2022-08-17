<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Ticket;

class UserController extends Controller {

    private $request;
    /**
    * Create a new user controller instance.
    *
    * @return void
    */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create() {
        if (!$this->request->has('username')) {
            return response()->json('Missing username', 400);
        }
        if (!$this->request->has('password')) {
            return response()->json('Missing password', 400);
        }
        if (!$this->request->has('email')) {
            return response()->json('Missing email', 400);
        }

        $username = $this->request->input('username');
        $password = $this->request->input('password');
        $email = $this->request->input('email');

        if ($username === NULL || empty($username)) {
            return response()->json('Username must not be null or empty', 400);
        } else {
            if (User::where('username', $username)->exists()) {
                return response()->json("$username already exists", 400);
            }
        }

        if ($password === NULL || empty($password)) {
            return response()->json('Password must not be null or empty', 400);
        } 

        if ($email === NULL || empty($email)) {
            return response()->json('Email must not be null or empty', 400);
        } else {
            if (User::where('email', $email)->exists()) {
                return response()->json("$email already exists", 400);
            }
        }
        
        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'email' => $email
            ]);
            DB::commit();

            return response()->json($user);
    
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    public function update($id) {
        // validation
        $ticket = User::find($id);
        if(empty($ticket)) {
            return response()->json([
                'message' => 'The user does not exist'
            ], 400);
        }

        // update staff
        DB::beginTransaction();
        try
        {
            // update username 
            if($this->request->has('username'))
            {
                $user->update([
                    'username' => $this->request->input('username'),
                ]);
            }
            // update password 
            if($this->request->has('password'))
            {
                $password = Hash::make($this->request->input('password'));
                $user->update([
                    'password' => $password,
                ]);
            }
            // update email 
            if($this->request->has('email'))
            {
                $email = $this->request->input('email');
                $user->update([
                    'email' => $email,
                ]);
            }

            // update completed
            DB::commit();

        } catch(\Exception $e) {
          DB::rollback();
          return response()->json([
            'error' => $e->getMessage(),
          ], 400);
        }

    }
}
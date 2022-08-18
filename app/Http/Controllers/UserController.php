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

    // create user
    // return json with user information
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

    // update user
    // return json with update information
    public function update($id) {
        // validation
        $user = User::find($id);
        if(empty($user)) {
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
                $username = $this->request->input('username');
                if (User::where('username', $username)->exists()) {
                    return response()->json("$username already exists", 400);
                }
                $user->update([
                    'username' => $username
                ]);
            }
            // update password 
            if($this->request->has('password'))
            {
                $password = Hash::make($this->request->input('password'));
                $user->update([
                    'password' => $password
                ]);
            }
            // update email 
            if($this->request->has('email'))
            {
                $email = $this->request->input('email');

                if (User::where('email', $email)->exists()) {
                    return response()->json("$email already exists", 400);
                }
                $user->update([
                    'email' => $email,
                ]);
            }

            // update completed
            DB::commit();

            return response()->json($user);

        } catch(\Exception $e) {
          DB::rollback();
          return response()->json([
            'error' => $e->getMessage(),
          ], 400);
        }

    }

    // retrieve user
    // return json with user information
    public function read($id) {

        // validation
        $user = User::find($id);
        if(empty($user))
        {
            return response()->json([
                'message' => 'The user does not exist'
            ], 400);
        }
        return response()->json($user);
    }

    // retrieve username by username
    // return json with user information
    public function readByUsername($username) {

        // validation
        $user = User::where('username', $username)->first();
        if(!$user)
        {
            return response()->json([
                'message' => 'The user does not exist'
            ], 400);
        }
        
        return response()->json($user);
    }

    // list users
    // return array of users and count of users
    public function list() {

        $query = new User();

        // order by particular column and direction
        if($this->request->has('order_by') && $this->request->has('order_direction')) {
            $query = $query->orderBy($this->request->input('order_by'), $this->request->input('order_direction'));
        } else {
            $query = $query->orderBy('id', 'asc');
        }

        // filter username
        if ($this->request->has('username')) {
            $query = $query->where('username', $this->request->input('username'));
        }
  
        // filter email
        if ($this->request->has('email')) {
            $query = $query->where('email', $this->request->input('email'));
        }

        // retrieve count from query
        $outputCount = $query->count();

        //if has paging parameter, do pagination
        //if not, list all items
        if ($this->request->has('page') && $this->request->has('items_per_page')){
            // set pagination
            $page = intval($this->request->input('page'));
            $itemsPerPage = intval($this->request->input('items_per_page'));
  
            // retrieve items from query
            $models = $query
                    ->skip(($page - 1)*$itemsPerPage)
                    ->take($itemsPerPage)
                    ->get();
        } else {
            $models = $query->get();
        }
  
        $result = $models->toArray();

        return response()->json([
            'items' => $result,
            'count' => $outputCount
        ]);
    }   
}
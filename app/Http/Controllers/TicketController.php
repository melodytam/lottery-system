<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Ticket;

class TicketController extends Controller {

    private $request;

        /**
    * Create a new ticket controller instance.
    *
    * @return void
    */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    // request body: user_id
    // return: ticket id and ticket No.
    public function create() {

        if (!$this->request->has('user_id')) {
            return response()->json('Missing user id', 400);
        }

        $userId = $this->request->input('user_id');

        // generate random string 
        $ticketNo = substr(str_shuffle(md5(time())),0, 10);

        DB::beginTransaction();
        try {
            $ticket = Ticket::create([
                'ticket_no' => $ticketNo,
                'is_drawn' => false,
                'draw_id' => null,
                'is_win' => null,
                'user_id' => $userId
            ]);
            DB::commit();

            return response()->json($ticket);
    
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
        // return ticket id and ticket No. as json
    }

    // retrieve ticket
    public function read($id) {

        // validation
        $ticket = Ticket::find($id);
        if(empty($ticket))
        {
          return response()->json([
            'message' => 'The ticket does not exist'
          ], 400);
        }
        
        return response()->json($ticket);
    }

    // retrieve ticket by ticket_no
    public function readByTicketNo($ticketNo) {

        // validation
        $ticket = Ticket::where('ticket_no', $ticketNo)->first();
        if(!$ticket)
        {
            return response()->json([
                'message' => 'The ticket does not exist'
            ], 400);
        }
        
        return response()->json($ticket);
    }

    // list all tickets
    public function list() {
        $query = new Ticket();

        // order by particular column and direction
        if($this->request->has('order_by') && $this->request->has('order_direction')) {
            $query = $query->orderBy($this->request->input('order_by'), $this->request->input('order_direction'));
        } else {
            $query = $query->orderBy('id', 'asc');
        }

        // filter is_drawn
        if ($this->request->has('is_drawn')) {
            $query = $query->where('is_drawn', $this->request->input('is_drawn'));
        }
  
        // filter draw_id
        if ($this->request->has('draw_id')) {
            $query = $query->where('draw_id', $this->request->input('draw_id'));
        }

        // filter is_win
        if ($this->request->has('is_win')) {
            $query = $query->where('is_win', $this->request->input('is_win'));
        }

        // filter user_id
        if ($this->request->has('user_id')) {
            $query = $query->where('user_id', $this->request->input('user_id'));
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
            'count' => $outputCount,
        ]);
    }   


}
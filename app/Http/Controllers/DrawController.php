<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Ticket;
use App\Models\Draw;
use App\Jobs\DrawTicketJob;

use Carbon\Carbon;

class DrawController extends Controller {

    private $request;

        /**
    * Create a new draw controller instance.
    *
    * @return void
    */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    // create a draw (draw ticket)
    // param: user_id
    // return: json with win ticket and draw information
    public function create() {

        // Get valid tickets (tickets not yet been drawn and created before this draw)
        $query = Ticket::where('is_drawn', false)->where('created_at', '<', Carbon::now());
        $validTicketIds = $query->pluck('id')->toArray();

        if (count($validTicketIds) > 0) {
            // draw a ticket from the valid tickets
            $selectedTicketId = array_rand($validTicketIds);
            $selectedTicket = Ticket::find($selectedTicketId);
            $draw = null;

            DB::beginTransaction();
            try {
                $draw = Draw::create();
                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                ], 400);
            }

            if ($draw) {

                DB::beginTransaction();
                try {
                    Ticket::whereIn('id', $validTicketIds)->where('id', '<>', $selectedTicketId)->update([
                        'is_drawn' => true,
                        'draw_id' => $draw->id,
                        'is_win' => false
                    ]);
                    Ticket::where('id', $selectedTicketId)->update([
                        'is_drawn' => true,
                        'draw_id' => $draw->id,
                        'is_win' => true
                    ]);
                    
                    DB::commit();
    
                    $winTicket = Ticket::find($selectedTicketId);
                    return response()->json([
                        "draw" => $draw,
                        "win_ticket" => $winTicket
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'error' => $e->getMessage(),
                    ], 400);
                }
            }
        }
        return response()->json([
            "draw" => null,
            "win_ticket" => null
        ]);
    }

    // retrieve draw
    // return: json with draw information
    public function read($id) {

        // validation
        $draw = Draw::find($id);
        if(empty($draw))
        {
          return response()->json([
            'message' => 'The draw does not exist'
          ], 400);
        }
        
        return response()->json($draw);
    }

    // list all draws
    // return: json with array of draws and count of draws 
    public function list() {
        $query = new Draw();

        // order by particular column and direction
        if($this->request->has('order_by') && $this->request->has('order_direction')) {
            $query = $query->orderBy($this->request->input('order_by'), $this->request->input('order_direction'));
        } else {
            $query = $query->orderBy('id', 'asc');
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
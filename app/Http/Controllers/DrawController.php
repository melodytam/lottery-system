<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Ticket;
use App\Models\Draw;
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
    // return: ticket id and ticket No.
    public function create() {

        // Get valid tickets (tickets not yet been drawn and created before this draw)
        $query = Ticket::where('is_drawn', false)->where('created_at', '<', Carbon::now());
        $validTicketIds = $query->pluck('id')->toArray();

        if (count($validTicketIds) > 0) {
            // draw a ticket from the valid tickets
            $selectedTicketId = array_rand($validTicketIds);
            $selectedTicket = Ticket::find($selectedTicketId);

            DB::beginTransaction();
            try {
                Ticket::whereIn('id', $validTicketIds)->update(['is_drawn' => true]);
                Ticket::where('id', $selectedTicketId)->update(['is_win' => true]);
                
                DB::commit();

                return response()->json([
                    'id' => $selectedTicketId,
                    'ticket_no' => $selectedTicket->ticket_no
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error' => $e->getMessage(),
                ], 400);
            }
            
        }
        return response()->json([
            'id' => null,
            'ticket_no' => ""
        ]);
        // return ticket id and ticket No. as json
    }
}
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DrawTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // draw ticket
        $query = Ticket::where('is_drawn', false)->where('created_at', '<', Carbon::now());
        $validTicketIds = $query->pluck('id')->toArray();

        if (count($validTicketIds) > 0) {
            // draw a ticket from the valid tickets
            $selectedTicketId = array_rand($validTicketIds);
            $selectedTicket = Ticket::find($selectedTicketId);
            $draw = null;

            \Log::info($selectedTicket);
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
                    Ticket::whereIn('id', $validTicketIds)->update([
                        'is_drawn' => true,
                        'draw_id' => $draw->id
                    ]);
                    Ticket::where('id', $selectedTicketId)->update(['is_win' => true]);
                    
                    DB::commit();
    
                    // return response()->json([
                    //     'id' => $selectedTicketId,
                    //     'ticket_no' => $selectedTicket->ticket_no
                    // ]);
                } catch (\Exception $e) {
                    DB::rollback();
                    // return response()->json([
                    //     'error' => $e->getMessage(),
                    // ], 400);
                }
            }
            
            
        }
        // return response()->json([
        //     'id' => null,
        //     'ticket_no' => ""
        // ]);
    }
}

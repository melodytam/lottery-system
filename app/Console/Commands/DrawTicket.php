<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DrawTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:draw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to draw a ticket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}

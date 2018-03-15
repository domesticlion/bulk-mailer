<?php

namespace App\Console\Commands;

use App;
use Illuminate\Console\Command;
use \App\ProcessedQueue;
use Mail;

class DeleteProcessedQueuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete_processed_queues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
     * @return mixed
     */
    public function handle()
    {
        ProcessedQueue::whereRaw("DATEDIFF(CURDATE(), `created_at`) > 30")->delete();
    }
}

<?php

namespace App\Console\Commands;

use App;
use Illuminate\Console\Command;
use \App\ProcessedQueue;
use Mail;

class SendBulkEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_bulk_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $sqs = App::make('aws')->createClient('sqs');
        for ($i = 1; $i <= 100; $i++) {
            $result = $sqs->receiveMessage([
                'QueueUrl' => env('AWS_SQS_QUEUE_BULK_MAIL'),
                'MaxNumberOfMessages' => 1
            ]);
            if ($messages = $result->getPath('Messages')) {
                foreach ($messages as $message) {
                    if (!$r = ProcessedQueue::where('queue_id', $message['MessageId'])->count()) {
                        $data = json_decode($message['Body']);
                        if (!empty($data->from) && !empty($data->to)) {
                            ProcessedQueue::create(['queue_id' => $message['MessageId']]);
                            Mail::send(['emails.html', 'emails.text'], ['data' => $data], function ($m) use ($data) {
                                $m->from($data->from, (!empty($data->from_name) ? $data->from_name : null))
                                    ->to($data->to, (!empty($data->to_name) ? $data->to_name : null))
                                    ->subject(!empty($data->subject) ? $data->subject : null);
                            });
                        }
                    }
                    $sqs->deleteMessage([
                        'QueueUrl' => env('AWS_SQS_QUEUE_BULK_MAIL'),
                        'ReceiptHandle' => $message['ReceiptHandle'],
                    ]);
                }
            }
        }
    }
}

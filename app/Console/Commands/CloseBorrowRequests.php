<?php

namespace App\Console\Commands;

use App\Models\BookRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseBorrowRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'close:requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close all pending borrow requests after specified time';

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
        $mytime = Carbon::now();
        $now = strtotime($mytime);
        $book_requests = BookRequest::where('is_pending', true)->where('is_accepted', null)->where('is_returned', null)->get();

        foreach ($book_requests as $book_request) {
            $create_time = strtotime($book_request->created_at);
            $time_spent = $now - $create_time;

            if ($time_spent > (intval(env('CLOSE_BORROW_REQUESTS')) * 60 * 60)) {
                $book_request->update([
                    'is_pending' => false,
                    'is_accepted' => false
                ]);
            }
        }

        $this->info("Run successful");
        return;
    }
}

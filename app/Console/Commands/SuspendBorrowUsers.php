<?php

namespace App\Console\Commands;

use App\Models\BookRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SuspendBorrowUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suspend:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspended users who have not returned borrowed books after specified time';

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
        $borrowed_books = BookRequest::where('is_accepted', true)->where('is_returned', null)->get();
        // return $this->info(json_encode($borrowed_books));
        foreach ($borrowed_books as $borrowed_book) {
            $updated_time = strtotime($borrowed_book->updated_at);
            $time_spent = $now - $updated_time;

            // $this->info("Time spent is : $time_spent");

            if ($time_spent > (intval(env('SUSPEND_BORROW_USERS')) * 60 * 60)) {
                $student = $borrowed_book->student;
                // return $this->info(json_encode($student));
                $suspend = $student->update([
                    'is_suspended' => true
                ]);

                $borrowed_book->is_returned = false;
                $borrowed_book->save();
            }
        }
        $this->info("Run successful");
        return;
    }
}

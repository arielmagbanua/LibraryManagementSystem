<?php

namespace App\Console\Commands;

use App\BorrowedBook;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

class BookFineWatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:book-fine-watcher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will query all active borrows and compute fine if they are more than 2 weeks old';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Initiating fine watcher');

        //query all borrowed books that are more than 2 weeks
        $borrowedBooks = BorrowedBook::whereRaw(DB::raw('ADDDATE(borrow_start_date, + 14) < NOW()'))
                                      ->where('status',1)
                                      ->with('book')->get();

        foreach($borrowedBooks as $borrowedBook)
        {
            //calculate the fine
            $this->info('Calculating fines for book_id = '.$borrowedBook->book->id);

            $dateNow = Carbon::now();
            $borrowStartDate = Carbon::parse($borrowedBook->borrow_start_date);
            $diffInDays =  $borrowStartDate->diffInDays($dateNow,true);

            $borrowedBook->fine = doubleval($diffInDays) * doubleval($borrowedBook->book->overdue_fine);
            $borrowedBook->save();
        }

        $this->info('Watcher finished!');
    }
}

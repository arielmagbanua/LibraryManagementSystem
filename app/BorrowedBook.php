<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BorrowedBook
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $book_id
 * @property integer $status
 * @property float $fine
 * @property string $borrow_start_date
 * @property string $date_returned
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereBookId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereFine($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereBorrowStartDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereDateReturned($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BorrowedBook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BorrowedBook extends Model
{
    protected $table = 'borrowed_books';

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'fine',
        'borrow_start_date',
        'date_returned',
    ];
}

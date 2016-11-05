<?php

namespace App;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Book
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Book searchBooksWithLimit($inputs)
 * @method static \Illuminate\Database\Query\Builder|\App\Book searchBooksWithoutLimit($inputs)
 * @mixin \Eloquent
 * @property integer $id
 * @property string $title
 * @property integer $author_id
 * @property string $isbn
 * @property integer $quantity
 * @property float $overdue_fine
 * @property string $shelf_location
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereAuthorId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereIsbn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereOverdueFine($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereShelfLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereUpdatedAt($value)
 * @property \Carbon\Carbon $deleted_at
 * @property-read \App\Author $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $borrowers
 * @method static \Illuminate\Database\Query\Builder|\App\Book whereDeletedAt($value)
 */
class Book extends Model
{
    use SoftDeletes;

    protected $table = 'books';

    protected $fillable = [
        'title',
        'author_id',
        'isbn',
        'quantities',
        'shelf_location'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Scope query for the server side processing for books datatable. This is with limit.
     *
     * @param $query
     * @param $inputs
     * @return mixed
     */
    public function scopeSearchBooksWithLimit($query, $inputs)
    {
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            //datatable column index  => database column name
            0 => 'books.title',
            1 => 'author_name',
            2 => 'books.isbn',
            3 => 'books.quantity',
            4 => 'books.overdue_fine',
            5 => 'books.shelf_location',
            6 => 'books.created_at'
        ];

        $authorConcat = "authors.first_name,' ',authors.middle_name,' ',authors.last_name";

        $query->select('books.*',DB::raw("CONCAT($authorConcat) AS author_name"));
        $query->join('authors', 'authors.id', '=', 'books.author_id');

        if(!empty($param) || $param!='')
        {
            $query->where('books.title','LIKE',"%$param%")
                  ->orWhere('books.isbn','LIKE',"%$param%")
                  ->orWhere('books.shelf_location','LIKE',"%$param%");

            if(is_double($param))
            {
                $paramDouble = doubleval($param);

                $query->orWhere('books.quantity','=',$paramDouble)
                      ->orWhere('books.overdue_fine','=',$paramDouble);
            }

            //for created_at
            if($this->validateDate($param))
            {
                $query->orWhere(DB::raw('DATE(books.created_at)'),'=',DB::raw("DATE('$param')"));
            }

            //for author_id
            $query->orWhere('authors.first_name','LIKE',"%$param%")
                  ->orWhere('authors.middle_name','LIKE',"%$param%")
                  ->orWhere('authors.last_name','LIKE',"%$param%");
        }

        $query->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);

        if($length>1)
        {
            $query->take($length)->skip($start);
        }

        return $query;

    }

    /**
     * Scope query for the server side processing for books datatable. This is without limit.
     *
     * @param $query
     * @param $inputs
     * @return mixed
     */
    public function scopeSearchBooksWithoutLimit($query, $inputs)
    {
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            //datatable column index  => database column name
            0 => 'books.title',
            1 => 'author_name',
            2 => 'books.isbn',
            3 => 'books.quantity',
            4 => 'books.overdue_fine',
            5 => 'books.shelf_location',
            6 => 'books.created_at'
        ];

        $authorConcat = "authors.first_name,' ',authors.middle_name,' ',authors.last_name";

        $query->select('books.*',DB::raw("CONCAT($authorConcat) AS author_name"));
        $query->join('authors', 'authors.id', '=', 'books.author_id');

        if(!empty($param) || $param!='')
        {
            $query->where('books.title','LIKE',"%$param%")
                ->orWhere('books.isbn','LIKE',"%$param%")
                ->orWhere('books.shelf_location','LIKE',"%$param%");

            if(is_double($param))
            {
                $paramDouble = doubleval($param);

                $query->orWhere('books.quantity','=',$paramDouble)
                    ->orWhere('books.overdue_fine','=',$paramDouble);
            }

            //for created_at
            if($this->validateDate($param))
            {
                $query->orWhere(DB::raw('DATE(books.created_at)'),'=',DB::raw("DATE('$param')"));
            }

            //for author_id
            $query->orWhere('authors.first_name','LIKE',"%$param%")
                  ->orWhere('authors.middle_name','LIKE',"%$param%")
                  ->orWhere('authors.last_name','LIKE',"%$param%");
        }

        $query->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);

        return $query;
    }

    /**
     * The author for this particular book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * All borrowers for this particular book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function borrowers()
    {
        return $this->belongsToMany(User::class,'borrowed_books','book_id','user_id');
    }

    /**
     * Date validation
     *
     * @param $date
     * @return bool
     */
    protected function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }
}
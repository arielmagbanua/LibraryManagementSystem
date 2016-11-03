<?php

namespace App;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use DB;

class Book extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'title',
        'author_id',
        'isbn',
        'quantities',
        'shelf_location'
    ];

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

        $query->select('books.*',"CONCAT($authorConcat) AS author_name");
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

        $query->select('books.*',"CONCAT($authorConcat) AS author_name");
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
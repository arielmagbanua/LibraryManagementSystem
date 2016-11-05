<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTime;

/**
 * App\Author
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $description
 * @property string $birth_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereMiddleName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereBirthDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Author searchAuthorsWithLimit($inputs)
 * @method static \Illuminate\Database\Query\Builder|\App\Author searchAuthorsWithoutLimit($inputs)
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
 * @method static \Illuminate\Database\Query\Builder|\App\Author whereDeletedAt($value)
 */
class Author extends Model
{
    use SoftDeletes;

    protected $table = 'authors';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'description',
        'birth_date'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Scope query for the server side processing for authors datatable. This is with limit.
     *
     * @param $query
     * @param $inputs
     * @return mixed
     */
    public function scopeSearchAuthorsWithLimit($query, $inputs)
    {
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            // datatable column index  => database column name
            0 => 'id',
            1 => 'first_name',
            2 => 'middle_name',
            3 => 'last_name',
            4 => 'description',
            5 => 'birth_date'
        ];

        if(!empty($param) || $param!='')
        {
            $query->where('first_name','LIKE',"%$param%")
                  ->orWhere('middle_name','LIKE',"%$param%")
                  ->orWhere('last_name','LIKE',"%$param%")
                  ->orWhere('description','LIKE',"%$param%");

            //for birth_date
            if($this->validateDate($param))
            {
                $query->orWhere(DB::raw('DATE(birth_date)'),'=',DB::raw("DATE('$param')"));
            }

            if(is_numeric($param))
            {
                $paramInt = intval($param);
                $query->orWhere('id','=',$paramInt);
            }
        }

        $query->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);

        if($length>1)
        {
            $query->take($length)->skip($start);
        }

        return $query;
    }

    /**
     * Scope query for the server side processing for authors datatable. This is without limit.
     *
     * @param $query
     * @param $inputs
     * @return mixed
     */
    public function scopeSearchAuthorsWithoutLimit($query, $inputs)
    {
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            // datatable column index  => database column name
            0 => 'id',
            1 => 'first_name',
            2 => 'middle_name',
            3 => 'last_name',
            4 => 'description',
            5 => 'birth_date'
        ];

        if(!empty($param) || $param!='')
        {
            $query->where('first_name','LIKE',"%$param%")
                ->orWhere('middle_name','LIKE',"%$param%")
                ->orWhere('last_name','LIKE',"%$param%")
                ->orWhere('description','LIKE',"%$param%");

            //for birth_date
            if($this->validateDate($param))
            {
                $query->orWhere(DB::raw('DATE(birth_date)'),'=',DB::raw("DATE('$param')"));
            }

            if(is_numeric($param))
            {
                $paramInt = intval($param);
                $query->orWhere('id','=',$paramInt);
            }
        }

        $query->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);

        return $query;
    }

    /**
     * All books authored by this particular author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books()
    {
        return $this->hasMany(Book::class);
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

<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DateTime;
use DB;

/**
 * App\User
 *
 * @method static \Illuminate\Database\Query\Builder|\App\User allMembers()
 * @method static \Illuminate\Database\Query\Builder|\App\User searchMembersWithLimit($inputs)
 * @method static \Illuminate\Database\Query\Builder|\App\User searchMembersWithoutLimit($inputs)
 * @mixin \Eloquent
 * @property integer $id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $address
 * @property string $email
 * @property string $birth_date
 * @property integer $account_type
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereMiddleName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereBirthDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAccountType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 */
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 
        'middle_name',
        'last_name',
        'address',   
        'email', 
        'birth_date',
        'account_type',
        'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function scopeAllMembers($query)
    {
        return $query->where('account_type','=',2);
    }

    /**
     * Scope query for the server side processing for members datatable. This is with limit.
     *
     * @param $query
     * @param $inputs
     * @return mixed
     */
    public function scopeSearchMembersWithLimit($query, $inputs)
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
            4 => 'address',
            5 => 'email',
            6 => 'birth_date'
        ];

        $query->where('account_type','=',2);

        if(!empty($param) || $param!='')
        {
            $rawWhere = "(first_name LIKE '%$param%' OR middle_name LIKE '%$param%' OR last_name LIKE '%$param%' OR address LIKE '%$param%' OR email LIKE '%$param%' BIRTH_DATE_CRITERIA ID_CRITERIA)";

            /*
            $query->where(function($query,$inputs) use ($inputs)
            {
                $param = $inputs['search']['value'];

                $query->where('first_name','LIKE',"%$param%")
                      ->orWhere('middle_name','LIKE',"%$param%")
                      ->orWhere('last_name','LIKE',"%$param%")
                      ->orWhere('address','LIKE',"%$param%")
                      ->orWhere('email','LIKE',"%$param%");

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
            });
            */

            //for birth_date
            if($this->validateDate($param))
            {
                //$query->orWhere(DB::raw('DATE(birth_date)'),'=',DB::raw("DATE('$param')"));
                $criteria = " OR DATE(birth_date) = DATE('$param')";
                $rawWhere = str_replace('BIRTH_DATE_CRITERIA',$criteria,$rawWhere);
            }
            else
            {
                $rawWhere = str_replace('BIRTH_DATE_CRITERIA','',$rawWhere);
            }

            if(is_numeric($param))
            {
                $paramInt = intval($param);
                //$query->orWhere('id','=',$paramInt);
                $criteria = " OR id = $paramInt";
                $rawWhere = str_replace('ID_CRITERIA',$criteria,$rawWhere);
            }
            else
            {
                $rawWhere = str_replace('ID_CRITERIA','',$rawWhere);
            }

            $query->whereRaw(DB::raw($rawWhere));
        }

        $query->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);

        if($length>1)
        {
            $query->take($length)->skip($start);
        }

        return $query;
    }

    /**
     * Scope query for the server side processing for members datatable. This is without limit.
     *
     * @param $query
     * @param $inputs
     * @return mixed
     */
    public function scopeSearchMembersWithoutLimit($query, $inputs)
    {
        $param = $inputs['search']['value'];

        $columns = [
            // datatable column index  => database column name
            0 => 'id',
            1 => 'first_name',
            2 => 'middle_name',
            3 => 'last_name',
            4 => 'address',
            5 => 'email',
            6 => 'birth_date'
        ];

        $query->where('account_type','=',2);

        if(!empty($param) || $param!='')
        {
            $rawWhere = "(first_name LIKE '%$param%' OR middle_name LIKE '%$param%' OR last_name LIKE '%$param%' OR address LIKE '%$param%' OR email LIKE '%$param%' BIRTH_DATE_CRITERIA ID_CRITERIA)";

            /*
            $query->where(function($query,$param)
            {
                $query->where('first_name','LIKE',"%$param%")
                    ->orWhere('middle_name','LIKE',"%$param%")
                    ->orWhere('last_name','LIKE',"%$param%")
                    ->orWhere('address','LIKE',"%$param%")
                    ->orWhere('email','LIKE',"%$param%");

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
            });
            */

            //for birth_date
            if($this->validateDate($param))
            {
                //$query->orWhere(DB::raw('DATE(birth_date)'),'=',DB::raw("DATE('$param')"));
                $criteria = " OR DATE(birth_date) = DATE('$param')";
                $rawWhere = str_replace('BIRTH_DATE_CRITERIA',$criteria,$rawWhere);
            }
            else
            {
                $rawWhere = str_replace('BIRTH_DATE_CRITERIA','',$rawWhere);
            }

            if(is_numeric($param))
            {
                $paramInt = intval($param);
                //$query->orWhere('id','=',$paramInt);
                $criteria = " OR id = $paramInt";
                $rawWhere = str_replace('ID_CRITERIA',$criteria,$rawWhere);
            }
            else
            {
                $rawWhere = str_replace('ID_CRITERIA','',$rawWhere);
            }

            $query->whereRaw(DB::raw($rawWhere));
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

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class RedirectIfAuthenticated
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {

            //get the authenticated user's account type
            $userAccountType = $request->user()->account_type;

            switch($userAccountType)
            {
                case 1:
                    return redirect('admin');
                    break;

                case 2:
                    //commented since it after authentication system will redirect to the member index
                    //return redirect('member');
                    break;

                default:
                    return response('Unauthorized.', 401);
            }

        }

        return $next($request);
    }
}
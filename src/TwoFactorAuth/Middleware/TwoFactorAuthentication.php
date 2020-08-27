<?php

namespace Freedom\TwoFactorAuth\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;


class TwoFactorAuthentication extends Authenticate
{

    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if($request->route()->uri  != 'logout'){
            $this->authenticate($request, $guards);
        }

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            $auth_guard = $this->auth->guard($guard);
            if($auth_guard->check()){
                if(
                    method_exists($auth_guard,'hasVerifiedAuth') &&
                    !$auth_guard->hasVerifiedAuth()
                 ){
                    throw new AuthenticationException(
                        'Unverified.', $guards, $this->redirectToCodeVerification($request)
                    );
                }
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException( 'Unauthenticated.', $guards, $this->redirectTo($request));
    }

    protected function redirectToCodeVerification($request)
    {
        return \Config::get('two-factor-auth.verification_route');
    }


}

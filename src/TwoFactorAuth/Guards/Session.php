<?php

namespace Freedom\TwoFactorAuth\Guards;
use Illuminate\Auth\SessionGuard;
use Freedom\TwoFactorAuth\Contracts\HasTwoFactorAuthentication;
use Carbon\Carbon;

class Session extends SessionGuard
{

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        //return ! $this->check();
        return ! $this->check() || ! $this->hasVerifiedAuth();
    }


    /*
    * Determine if the current user has already verified with Auth Code
    * return @boolean
    */
    public function hasVerifiedAuth()
    {
        $user = $this->user();

        // Check if user model implements two factor interface and is Auth Code restricted
        if($user instanceof HasTwoFactorAuthentication && $user->is_code_restricted ){
            //count if user has authentication code to be verified via notification
            return $user->authenticationCode()->where('expire_on','>=',Carbon::now())->count() <= 0;
        }

        return ! is_null($user);
    }

    public function verifyCode($code,$reference_id)
    {
        $user = $this->user();
        $auth_code = $user->authenticationCodes()->where('reference_id',$reference_id)->first();
        if($auth_code){
            $match = $code == $auth_code->code;
            if($match){
                $user->authenticationCodes()->update(['disabled' => 1]);
            }
            return $match;
        }
        return false;
    }

    public function sendCode()
    {
        $user = $this->user();
        if($user->is_code_restricted){
            $auth_code = $user->createAuthenticationCode();
            $number = preg_replace('/[^0-9]/','',$user->phone_number);
            \Sms::send(substr($number,2),substr($number,0,2), $auth_code->code,$auth_code->reference_id);
            return $auth_code;
        }
        return null;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $is_authenticated = parent::attempt($credentials,$remember);
        if($is_authenticated){
            $this->sendCode();
        }
        return $is_authenticated;
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        parent::logout();
    }

}

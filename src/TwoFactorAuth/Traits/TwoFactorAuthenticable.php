<?php

namespace Freedom\TwoFactorAuth\Traits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

trait TwoFactorAuthenticable
{
    public function authenticationCode()
    {
        return $this->hasMany(Config::get('two-factor-auth.model'),'user_id','id')
        ->where('user_type',get_called_class())
        ->where('disabled',0)
        ->orderBy('created_at','desc');
    }

    public function authenticationCodes()
    {
        return $this->hasMany(Config::get('two-factor-auth.model'),'user_id','id')
        ->where('user_type',get_called_class())
        ->where('disabled',0)
        ->orderBy('created_at','desc');
    }

    public function getIsCodeRestrictedAttribute()
    {
        return false;
    }

    public function hasVerifiedAuth()
    {
        if($this->is_code_restricted){
            return $this->authenticationCode()->where('expire_on','>=',Carbon::now())->count() <= 0;
        }
        return true;
    }

}

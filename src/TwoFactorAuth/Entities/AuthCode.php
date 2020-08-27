<?php

namespace Freedom\TwoFactorAuth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Auth;
use Config;

/**
 * Class AuthCode.
 *
 * @package namespace App\Entities;
 */
class AuthCode extends Model
{
    protected $table = "oauth_auth_code";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type',
        'code',
        'user_id',
        'reference_id',
        'expire_on',
        'disabled',
    ];

    protected function performInsert(Builder $query)
    {
        $user = Auth::user();

        $this->code = rand(11111,99999);
        $this->reference_id = rand(11111,99999);
        $this->expire_on = Carbon::now()->addMinutes(Config::get('two-factor-auth.lifetime'));
        $this->user_id = $user->id;
        $this->user_type = get_class($user);

        parent::performInsert($query);
    }


    protected function getIsEnabledAttribute()
    {
        return $this->expire_on->greaterThan(Carbon::now()) && !$this->disabled;
    }

    public function getExpireOnAttribute($value)
    {
        return Carbon::create($value);

    }

    public function isMatch($code)
    {
        return $this->code == $code;
    }

    public function use($code){
        if($this->isMatch($code)){
            $this->update(['disabled' => true]);
            return true;
        }
        return false;
    }
}

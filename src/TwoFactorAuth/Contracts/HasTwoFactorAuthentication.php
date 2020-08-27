<?php

namespace Freedom\TwoFactorAuth\Contracts;

interface HasTwoFactorAuthentication
{
    /*
    * Get the Flag to identify Authenticatable is Code Restricted
    * return @boolean
    */
    public function getIsCodeRestrictedAttribute() : bool;
    /*
    * Create an authentication code o
    * return Illuminate\Database\Eloquent\Model;
    */
    public function createAuthenticationCode();

    /*
    * Get the Eloquent Model containing the latest authentication code
    * return Illuminate\Database\Eloquent\Model;
    */
    public function authenticationCode();

    /*
    * Get all the Eloquent Model containing the authenication codes
    * return @Collection;
    */
    public function authenticationCodes();

}

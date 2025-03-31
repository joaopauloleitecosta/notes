<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class Operations
{
    public static function decryptId($value)
    {
        //check this $value is encrypted
        try {
            $value = Crypt::decrypt($value);
        } catch (DecryptException $e) {
            //return redirect()->route('home');
            return null;
        }

        return $value;
    }
}
<?php

namespace App\Actions\Auths;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginAction
{
   public function excecute(string $email, string $otp): bool
   {
      $normalizedEmail = strtolower(trim($email));
      $customer = Customer::where('email', $normalizedEmail)->first();

      if (! $customer) {
         return false;
      }

      $cacheKey = $this->cacheKey($normalizedEmail);
      $cachedOtp = (string) Cache::get($cacheKey, '');

      if ($cachedOtp === '' || ! hash_equals($cachedOtp, trim($otp))) {
         return false;
      }

      Auth::guard('customer')->login($customer);
      request()->session()->regenerate();
      Cache::forget($cacheKey);

      return true;
   }

   private function cacheKey(string $email): string
   {
      return "auth:otp:{$email}";
   }
}
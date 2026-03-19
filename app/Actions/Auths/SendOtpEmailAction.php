<?php

namespace App\Actions\Auths;

use App\Mail\OTPMail;
use App\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendOtpEmailAction
{
   public function excecute(string $email): bool
   {
      $normalizedEmail = strtolower(trim($email));
      $customer = Customer::where('email', $normalizedEmail)->first();

      if (! $customer) {
         return false;
      }

      $cacheKey = $this->cacheKey($normalizedEmail);
      $otp = Cache::get($cacheKey);

      if (! $otp) {
         $otp = (string) random_int(100000, 999999);
         Cache::put($cacheKey, $otp, now()->addMinutes(5));
      }

      Mail::to($customer->email)->queue(new OTPMail($otp));

      return true;
   }

   private function cacheKey(string $email): string
   {
      return "auth:otp:{$email}";
   }
}
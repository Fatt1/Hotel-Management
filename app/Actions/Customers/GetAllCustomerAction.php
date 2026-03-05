<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllCustomerAction
{
    public function handle(
      int $page_size = 10, 
      int $page = 1, 
      ?string $search = null,
      ?string $country = null
    ): LengthAwarePaginator {
      $query = Customer::query();
      if($search)
         {
        $query->where(function ($q) use ($search) {
          $q->where('first_name', 'like','%'. $search. '%')
          ->orWhere('last_name', 'like','%'. $search. '%')
          ->orWhere('email', 'like','%'. $search. '%')
          ->orWhere('phone_number', 'like','%'. $search. '%');
        });
      }
      if($country)
         {
        $query->where('country', $country); 
      }
        
    return $query-> orderBy('id', 'asc')
    ->paginate($page_size, ['*'], 'page', $page);
    
    }
}
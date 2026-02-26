<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;

class GetAllRoleAction
{
    public function handle($page_size = 10, $page_number = 1, $search = null){
      $query = Role::query();
      if($search)
         {
        $query->where('name', 'like','%'. $search. '%'); 
      }  
    return $query->paginate($page_size, ['*'], 'page', $page_number);
    }
}

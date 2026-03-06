<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;

class GetAllRoleAction
{
    public function handle($page_size = 10, $page = 1, $search = null){
      $query = Role::query();
      if($search)
         {
        $query->where('name', 'like','%'. $search. '%'); 
      }
      $query = $query->orderBy('name', 'asc');  
    return $query->paginate($page_size, ['*'], 'page', $page);
    }
}

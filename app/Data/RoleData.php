<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class RoleData extends Data
{
   public function __construct(
        #[Required]
        #[Unique('roles', 'name', ignore: 'id')]
       public string $name,
       public ?int $id = null,
   ) {
   }

   public static function messages(...$args): array
   {
       return [
           'name.required' => 'Vui lòng nhập tên vai trò',
           'name.unique' => 'Tên vai trò đã tồn tại',
       ];
   }

  
}

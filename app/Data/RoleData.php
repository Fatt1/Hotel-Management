<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class RoleData extends Data
{
   public function __construct(
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

   // Bắt buộc phải dùng hàm này để xử lý ID động
   public static function rules(ValidationContext|null $context = null ): array
   {
       // Lấy ID từ cục data gửi lên (nếu đang là Update thì sẽ có ID, Create thì null)
       $roleId = $context->payload['id'] ?? null;

       return [
           'name' => [
               'required',
               Rule::unique('roles', 'name')->ignore($roleId),
               
           ],
       ];
   }
}

<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class LoginAdminData extends Data
{
    public function __construct(
        #[Required]
        #[Email]
        public string $email,
        
        #[Required]
        #[Min(4)]
        public string $password,
    ) {
    }

    public static function messages(...$args): array
    {
        return [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 4 ký tự',
        ];
    }
}

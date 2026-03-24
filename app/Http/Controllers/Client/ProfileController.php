<?php

namespace App\Http\Controllers\Client;

use App\Actions\Customers\UpdateCustomerAction;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the profile page for the authenticated customer.
     */
    public function show()
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return view('client.profile.index', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the authenticated customer's profile.
     */
    public function update(Request $request, UpdateCustomerAction $action)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'regex:/^\+?[0-9]{9,15}$/'],
            'country'      => 'required|string|max:255',
            'email'        => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('customers', 'email')->ignore($customer->id),
            ],
        ], [
            'first_name.required'   => 'Vui lòng nhập tên.',
            'last_name.required'    => 'Vui lòng nhập họ.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'phone_number.regex'    => 'Số điện thoại phải có 10 hoặc 11 chữ số (có thể bắt đầu bằng +).',
            'country.required'      => 'Vui lòng chọn quốc gia.',
            'email.required'        => 'Vui lòng nhập email.',
            'email.email'           => 'Email không hợp lệ.',
            'email.unique'          => 'Email này đã được sử dụng bởi tài khoản khác.',
        ]);

        $customer->first_name   = $validated['first_name'];
        $customer->last_name    = $validated['last_name'];
        $customer->phone_number = $validated['phone_number'];
        $customer->country      = $validated['country'];
        $customer->email        = $validated['email'];
        $customer->save();

        return redirect()
            ->route('client.profile')
            ->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    }
}

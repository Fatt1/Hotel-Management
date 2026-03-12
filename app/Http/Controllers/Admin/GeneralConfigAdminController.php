<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GeneralConfig\UpdateSurchargePoliciesAction;
use App\Actions\GeneralConfig\UpdateSystemSettingsAction;
use App\Enums\PolicyType;
use App\Http\Controllers\Controller;
use App\Models\SurchargePolicy;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\Request;

class GeneralConfigAdminController extends Controller
{
    public function index()
    {
        $section = request()->query('section', 'general');
        $settings = SystemSetting::whereIn('setting_key', ['checkin_time', 'checkout_time', 'rounding_time'])->pluck('setting_value', 'setting_key');
        $checkinPolicies = SurchargePolicy::where('policy_type', PolicyType::CHECKIN_EARLY->value)->orderBy('hour_mark')->get();
        $checkoutPolicies = SurchargePolicy::where('policy_type', PolicyType::CHECKOUT_LATE->value)->orderBy('hour_mark')->get();
        return view('admin.general-config.index', compact('section', 'settings', 'checkinPolicies', 'checkoutPolicies'));
    }

    public function updateGeneral(Request $request, UpdateSystemSettingsAction $action)
    {
        $request->validate([
            'checkin_time' => ['required', 'date_format:H:i'],
            'checkout_time' => ['required', 'date_format:H:i'],
            'rounding_time' => ['required', 'integer', 'min:1','max:59'],
        ],[
            'checkin_time.required' => 'Vui lòng nhập thời gian check-in',
            'checkin_time.date_format' => 'Thời gian check-in không hợp lệ phải có định dạng HH:mm',
            'checkout_time.required' => 'Vui lòng nhập thời gian check-out',
            'checkout_time.date_format' => 'Thời gian check-out không hợp lệ phải có định dạng HH:mm',
            'rounding_time.required' => 'Vui lòng nhập số phút làm tròn',
            'rounding_time.integer' => 'Số phút làm tròn phải là số nguyên',
            'rounding_time.min' => 'Số phút làm tròn tối thiểu là 1',
            'rounding_time.max' => 'Số phút làm tròn tối đa là 59',
        ]);
        try{
            $action->handle(
                $request->input('checkin_time'),
                $request->input('checkout_time'),
                (int) $request->input('rounding_time')
            );
            return redirect()
                ->route('admin.general-config.index', ['section' => 'general'])
                ->with('success', 'Cấu hình vận hành chung đã được cập nhật thành công');
        }catch (Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function updateSurcharge(Request $request, UpdateSurchargePoliciesAction $action)
    {
        $request->validate([
            'checkin_early' => ['nullable','array'],
            'checkin_early.*.hour_mark' => ['required', 'integer', 'min:1', 'max:24'],
            'checkin_early.*.price' => ['required', 'numeric', 'min:0'],
            'checkout_late' => ['nullable','array'],
            'checkout_late.*.hour_mark' => ['required', 'integer', 'min:1', 'max:24'],
            'checkout_late.*.price' => ['required', 'numeric', 'min:0'],
        ],[
            'checkin_early.*.hour_mark.required' => 'Vui lòng nhập số giờ check-in sớm',
            'checkin_early.*.hour_mark.integer' => 'Số giờ check-in sớm phải là số nguyên',
            'checkin_early.*.hour_mark.min' => 'Số giờ check-in sớm tối thiểu là 1',
            'checkin_early.*.hour_mark.max' => 'Số giờ check-in sớm tối đa là 24',
            'checkin_early.*.price.required' => 'Vui lòng nhập mức phí check-in sớm',
            'checkin_early.*.price.numeric' => 'Mức phí check-in sớm phải là số',
            'checkin_early.*.price.min' => 'Mức phí check-in sớm không được âm',
            'checkout_late.*.hour_mark.required' => 'Vui lòng nhập số giờ check-out muộn',
            'checkout_late.*.hour_mark.integer' => 'Số giờ check-out muộn phải là số nguyên',
            'checkout_late.*.hour_mark.min' => 'Số giờ check-out muộn tối thiểu là 1',
            'checkout_late.*.hour_mark.max' => 'Số giờ check-out muộn tối đa là 24',
            'checkout_late.*.price.required' => 'Vui lòng nhập mức phí check-out muộn',
            'checkout_late.*.price.numeric' => 'Mức phí check-out muộn phải là số',
            'checkout_late.*.price.min' => 'Mức phí check-out muộn không được âm',
        ]);
        try{
            $action->handle(PolicyType::CHECKIN_EARLY->value, $request->input('checkin_early',[]));
            $action->handle(PolicyType::CHECKOUT_LATE->value, $request->input('checkout_late',[]));
            return redirect()
                ->route('admin.general-config.index', ['section' => 'surcharge'])
                ->with('success', 'Quy định phụ phí đã được cập nhật thành công');
        }catch (Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}

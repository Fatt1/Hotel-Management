<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GeneralConfig\UpdateSurchargePoliciesAction;
use App\Actions\GeneralConfig\UpdateSystemSettingsAction;
use App\Data\SurchargePoliciesData;
use App\Data\SystemSettingsData;
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

    public function updateGeneral(SystemSettingsData $data, UpdateSystemSettingsAction $action)
    {
        try{
            $action->handle(
                $data->checkin_time,
                $data->checkout_time,
                $data->rounding_time
            );
            return redirect()
                ->route('admin.general-config.index', ['section' => 'general'])
                ->with('success', 'Cấu hình vận hành chung đã được cập nhật thành công');
        }catch (Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function updateSurcharge(SurchargePoliciesData $data, UpdateSurchargePoliciesAction $action)
    {
        try{
            $action->handle(PolicyType::CHECKIN_EARLY->value, $data->checkin_early ?? []);
            $action->handle(PolicyType::CHECKOUT_LATE->value, $data->checkout_late ?? []);
            return redirect()
                ->route('admin.general-config.index', ['section' => 'surcharge'])
                ->with('success', 'Quy định phụ phí đã được cập nhật thành công');
        }catch (Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}

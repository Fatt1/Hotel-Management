<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Actions\Customers\GetAllCustomerAction;
use App\Actions\Customers\GetCustomerByIdAction;
use App\Actions\Customers\AddCustomerAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Actions\Customers\DeleteCustomerAction;
use App\Data\CustomerData;
use App\ViewModels\CustomerViewModel;
use Exception;
use Illuminate\Http\Request;

class CustomerAdminController extends Controller
{
    public function index(Request $request, GetAllCustomerAction $getAllCustomerAction)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page', 1);
        $search = $request->input('search', null);
        $country = $request->input('country', null);
        $customer = $getAllCustomerAction->handle($page_size, $page_number, $search, $country);
        return view('admin.customers.index', [
            'customers' => $customer,
        ]);
    }

    public function show(int $id, GetCustomerByIdAction $getCustomerByIdAction)
    {
        try{
            $customer = $getCustomerByIdAction->handle($id);
            $viewModel = new CustomerViewModel($customer);
            return view('admin.customers.show', [
                'viewModel' => $viewModel,
            ]);
        } catch (Exception $e) {
            return redirect()->route('admin.customer.index')->with('error', $e->getMessage());
        }
    }

    public function create(){
        $viewModel = new CustomerViewModel();
        return view('admin.customers.create', [
            'viewModel' => $viewModel,
        ]);
    }

    public function store(CustomerData $customerData, AddCustomerAction $addCustomerAction){
        $addCustomerAction->handle($customerData);
        return redirect()->route("admin.customers.index")->with("success","Khách hàng đã được thêm vào thành công.");
    }

    public function edit(int $id, GetCustomerByIdAction $getCustomerByIdAction){
        try {
            $customer = $getCustomerByIdAction->handle($id);
            $viewModel = new CustomerViewModel($customer);
            return view('admin.customers.edit', [
                'viewModel' => $viewModel,
            ]);
        } catch (Exception $e) {
            return redirect()->route('admin.customers.index')->with('error', $e->getMessage());
        }
    }

    public function update(int $id, CustomerData $customerData, UpdateCustomerAction $updateCustomerAction){
        try{
            $updateCustomerAction->handle($id, $customerData);
            return redirect()->route('admin.customers.index')->with('success', 'Cập nhật khách hàng thành công.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id, DeleteCustomerAction $deleteCustomerAction){
        try {
            $deleteCustomerAction->handle($id);
            return response()->json(['message' => 'Xóa khách hàng thành công'], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e->getMessage()], 400);
        }
    }
}

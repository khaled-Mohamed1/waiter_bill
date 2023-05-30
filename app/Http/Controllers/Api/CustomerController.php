<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $customers = Customer::with(['receipts','receipts.purchases', 'receipts.purchases.ProductPurchase'])->where('company_id', $user->company_id)->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع جميع العملاء بنجاح',
                'data' => [
                    'customers' => CustomerResource::collection($customers),
                ],
            ], 200);

        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                    'customer_name' => 'required',
                    'customer_email' => 'required|email|unique:customers,customer_email',
                    'customer_phone_number' => 'required|unique:customers,customer_phone_number',
                    'customer_address' => 'required',
                ],[
                    'customer_name.required' => 'يجب إدخال اسم العميل!',
                    'customer_email.required' => 'يجب إدخال البريد الإلكتروني',
                    'customer_email.email' => 'يجب أن يكون البريد الإلكتروني صالحاً ويحتوي على @',
                    'customer_email.unique' => 'البريد الإلكتروني مسجل مسبقا',
                    'customer_phone_number.required' => 'يجب إدخال رقم الهاتف',
                    'customer_address.required' => 'يجب إدخال مكان إقامة العميل!',
                    'customer_phone_number.unique' => 'رقم الهاتف مسجل مسبقا',

                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(isset($request->customer_image)){
                $imageName = Str::random(32) . "." . $request->customer_image->getClientOriginalExtension();
                $path = 'https://waiterbail.com/storage/app/public/customers/' . $imageName;
                Storage::disk('public')->put('customers/' . $imageName, file_get_contents($request->customer_image));
            }

            $user = auth()->user();

            $customer = Customer::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone_number' => $request->customer_phone_number,
                'customer_image' => $path ?? null,
                'customer_address' => $request->customer_address,
                'customer_notes' => $request->customer_notes,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء العميل بنجاح',
                'data' => [
                    'customer' => new CustomerResource($customer),
                ],
            ], 200);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            $user = auth()->user();
            $customer = Customer::with(['receipts','receipts.purchases', 'receipts.purchases.ProductPurchase'])->where('company_id', $user->company_id)->where('id', $id)->first();

            if(!$customer){
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على هذا العميل',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع العميل المختار بنجاح',
                'data' => [
                    'customer' => new CustomerResource($customer),
                ],
            ], 200);

        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا العميل',
                    'data' => null,
                ], 404);
            }

            $validateUser = Validator::make(
                $request->all(),
                [
                    'customer_name' => 'required',
                    'customer_email' => 'required|email|unique:customers,customer_email' . $id,
                    'customer_phone_number' => 'required|unique:customers,customer_phone_number' . $id,
                    'customer_address' => 'required',
                ],[
                    'customer_name.required' => 'يجب إدخال اسم العميل!',
                    'customer_email.required' => 'يجب إدخال البريد الإلكتروني',
                    'customer_email.email' => 'يجب أن يكون البريد الإلكتروني صالحاً ويحتوي على @',
                    'customer_email.unique' => 'البريد الإلكتروني مسجل مسبقا',
                    'customer_phone_number.required' => 'يجب إدخال رقم الهاتف',
                    'customer_phone_number.unique' => 'رقم الهاتف مسجل مسبقا',
                    'customer_address.required' => 'يجب إدخال مكان إقامة العميل!',

                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $storage = Storage::disk('public');
            if(isset($request->customer_image)){
                if ($storage->exists('customers/' . basename($customer->customer_image)))
                    $storage->delete('customers/' . basename($customer->customer_image));
                $imageName = Str::random(32) . "." . $request->customer_image->getClientOriginalExtension();
                $path = 'https://waiterbail.com/storage/app/public/customers/' . $imageName;
                Storage::disk('public')->put('customers/' . $imageName, file_get_contents($request->customer_image));
            }

            $customer->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone_number' => $request->customer_phone_number,
                'customer_image' => $path ?? null,
                'customer_address' => $request->customer_address,
                'customer_notes' => $request->customer_notes,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل العميل بنجاح',
                'data' => [
                    'customer' => new CustomerResource($customer),
                ],
            ], 200);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            // Detail
            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا العميل',
                    'data' => null,
                ], 404);
            }

            // Public storage
            $storage = Storage::disk('public');
            if ($storage->exists('customers/' . basename($customer->customer_image)))
                $storage->delete('customers/' . basename($customer->customer_image));

            $customer->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف العميل بنجاح',
            ], 200);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function updateStatus($id)
    {
        DB::beginTransaction();
        try {
            // Detail
            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا العميل',
                    'data' => null,
                ], 404);
            }

            $customer->update([
                'customer_status' => !$customer->customer_status
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل حالة العميل بنجاح',
            ], 200);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\ReceiptResource;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class ProfileController extends Controller
{

    public function index()
    {
        try {
            $user = auth()->user();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع بيانات الموظف بنجاح',
                'data' => [
                    'user' => new ProfileResource($user),
                ],
            ], 200);

        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function showReceipts()
    {
        try {
            $user = auth()->user();

            $receipts = Receipt::where('company_id', $user->company_id)->where('user_id', $user->id)->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع طلبات الموظف بنجاح',
                'data' => [
                    'receipts' => ReceiptResource::collection($receipts),
                ],
            ], 200);

        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function updatePassword(Request $request)
    {
        DB::beginTransaction();
        try {

            $user = auth()->user();

            $validateUser = Validator::make(
                $request->all(),
                [
                    'current_password' => 'required',
                    'new_password' => 'required|string|min:6',
                    'confirm_password' => 'required|same:new_password',
                ],[
                    'current_password.required' => 'يجب إدخال كلمة المرور الحالية',
                    'new_password.required' => 'يجب إدخال كلمة المرور الجديدة',
                    'new_password.min' => 'يجب أن تتكون كلمة المرور من 6 أحرف على الأقل',
                    'confirm_password.required' => 'يجب إدخال تأكيد كلمة المرور الجديدة',
                    'confirm_password.same' => 'يجب إدخال تأكيد كلمة المرور مطابقة لكمة المرور الجديدة',
                ]
            );

            // Check if the current password matches the one provided
            if ($request->current_password != $user->un_password) {
                return response()->json([
                    'success' => false,
                    'message' => 'كلمة السر الحالية غير صحيحة.',
                ], 401);
            }

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

//            $receiverNumber = "+970567494761";
//            $message = "This is testing from CodeSolutionStuff.com";
//
//            $account_sid = getenv("TWILIO_SID");
//            $auth_token = getenv("TWILIO_TOKEN");
//            $twilio_number = getenv("TWILIO_FROM");
//
//            $client = new Client($account_sid, $auth_token);
//            $client->messages->create($receiverNumber, [
//                'from' => $twilio_number,
//                'body' => $message]);

            // Update the user's password
            $user->password = bcrypt($request->new_password);
            $user->un_password = $request->new_password;
            $user->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل كلمة السر بنجاح',
            ], 200);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function confirmPin(Request $request){
        try {

            $user = auth()->user();

            if($user->pin_code != $request->pin_code){
                return response()->json([
                    'success' => true,
                    'message' => 'رقم التأكد غير صحيح',
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم التأكيد',
            ], 200);

        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'status' => true,
            'message' => 'تم تسجيل الخروج'
        ];
    }

}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class PasswordReset extends Controller
{
    public function forgetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا المستخدم',
                ], 404);
            }

            $token = Str::random(40);
            $domain = URL::to('/');
            $url = $domain . '/reset-password?token' . $token;

            $details = [
                'title' => 'Password Reset',
                'body' => [
                    'email' => $user->email,
                    'url' => $url,
                    'body' => 'Please click on below link to reset your Password.',]
            ];

            Mail::to($request->email)->send(new ForgetPasswordMail($details));

            $dataTime = Carbon::now()->format('Y-m-d H:i:s');
            $reset_password = \App\Models\PasswordReset::updateOrCreate(
               ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => $dataTime
                ]
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Please check your mail to reset your password.',
                'data' => [
                    'user' => $reset_password
                ]
            ], 200);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function resetPassword(Request $request){
        DB::beginTransaction();
        try {

            $resetData = \App\Models\PasswordReset::where('token', $request->token)->first();

            if(isset($request->token) && $resetData){

                $user = User::where('email', $request->email)->first();

                $validator = Validator::make($request->all(), [
                    'password' => 'required|string|min:6',
                    'confirm_password' => 'required|same:password',
                ], [
                    'password.required' => 'يجب إدخال كلمة المرور الجديدة',
                    'password.min' => 'يجب أن تتكون كلمة المرور من 6 أحرف على الأقل',
                    'confirm_password.required' => 'يجب إدخال تأكيد كلمة المرور الجديدة',
                    'confirm_password.same' => 'يجب إدخال تأكيد كلمة المرور مطابقة لكلمة المرور الجديدة',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'خطأ في التحقق من البيانات',
                        'errors' => $validator->errors()
                    ], 401);
                }

                $user->password = bcrypt($request->password);
                $user->un_password = $request->password;
                $user->save();

            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل كلمة المرور',
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

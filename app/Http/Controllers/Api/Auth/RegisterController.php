<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\CompanyCode;
use App\Models\User;
use App\Notifications\VerifyPhone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request){

        DB::beginTransaction();
        try {

            $company = CompanyCode::create([
                'company_code' => Helper::IDGenerator(new CompanyCode(), 'company_code', 6,'C_'),
            ]);

            if($company->company_code == 'C_000000'){
                $company->company_code = 'C_000001';
                $company->save();
            }

            $companyId = $company->id;

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'mobile_verify_code' => random_int(111111,999999),
                'address' => $request->address,
                'company_id' => $companyId,
                'password' => bcrypt($request->password),
                'un_password' => $request->password,
                'role_id' => 1
            ]);

            $user_role = Role::where(['name'=> 'admin'])->first();
            if($user_role){
                $user->assignRole($user_role);
            }

//            $user->notify(new SendVerifySMS());
//            $user->notify(new VerifyPhone());

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Register successful',
                'data' => [
                    'user' => new UserResource($user),
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
}

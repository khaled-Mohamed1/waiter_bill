<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();
            $shifts = Shift::where('company_id', $user->company_id);

            if ($user->role_id != 1) {
                $shifts->where('user_id', $user->id);
            }

            $shifts = $shifts->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع جميع المناوبات بنجاح',
                'data' => [
                    'shifts' => ShiftResource::collection($shifts),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'beginning_cash' => 'required|numeric',

            ], [
                'beginning_cash.required' => 'يجب إدخال المبلغ النقدي!',
                'beginning_cash.numeric' => 'يجب إدخال قيمة رقمية للمبلغ النقدي!',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في التحقق من الصحة',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = auth()->user();

            $shift = Shift::create([
                'shift_date' => now()->format('Y-m-d'), // Set the current date
                'shift_time_start' => now()->format('H:i:s'), // Set the current time
                'beginning_cash' => $request->beginning_cash,
                'company_id' => $user->company_id,
                'user_id' => $user->id,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المناوبة بنجاح',
                'data' => [
                    'shift' => new ShiftResource($shift),
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();
            $shift = Shift::where('company_id', $user->company_id)->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع المناوبة بنجاح',
                'data' => [
                    'shift' => new ShiftResource($shift),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على هذه المناوبة',
                'data' => null,
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $shift = Shift::where('company_id', $user->company_id)->find($id);

            if (!$shift) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذه المناوبة',
                    'data' => null,
                ], 404);
            }

            $shift->update([
                'status' => 'closed',
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إغلاق المناوبة بنجاح',
            ], 200);
        } catch (\Exception $e) {
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
    public function destroy(Shift $shift)
    {
        //
    }
}

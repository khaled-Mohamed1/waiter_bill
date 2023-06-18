<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashManagementResource;
use App\Models\CashManagement;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'value' => 'required|numeric',
                    'type' => 'required'
                ],
                [
                    'value.required' => 'يجب إدخال اسم التصنيف!',
                    'value.numeric' => 'يجب إدخال قيمة رقمية للتصنيف!',
                    'type.required' => 'يجب إدخال نوع المبلغ هل هو مدفوع او مسترد'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = auth()->user();
            $shift = Shift::where('company_id', $user->company_id)->find($id);

            $cash = CashManagement::create([
                'value' => $request->value,
                'note' => $request->note,
                'type' => $request->type,
                'shift_id' => $id
            ]);

            if ($request->type === 'المبالغ المدفوعة') {
                $shift->increment('payments', $request->value);
            } elseif ($request->type === 'المبالغ المسحوبة') {
                $shift->increment('withdrawal_amounts', $request->value);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الدفعة بنجاح',
                'data' => [
                    'cash' => new CashManagementResource($cash),
                ],
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
     * Display the specified resource.
     */
    public function show(CashManagement $cashManagement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashManagement $cashManagement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashManagement $cashManagement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashManagement $cashManagement)
    {
        //
    }
}

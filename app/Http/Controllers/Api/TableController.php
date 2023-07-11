<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TableResource;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $tables = Table::with('tickets')->where('company_id', $user->company_id)->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع جميع الطاولات بنجاح',
                'data' => [
                    'tables' => TableResource::collection($tables),
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
                    'table_name' => 'required',
                ],[
                    'table_name.required' => 'يجب إدخال اسم الطاولة!',
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

            $table = Table::create([
                'table_name' => $request->table_name,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الطاولة بنجاح',
                'data' => [
                    'table' => new TableResource($table),
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
            $table = Table::with('tickets')->where('company_id', $user->company_id)->where('id', $id)->first();

            if(!$table){
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على هذه الطاولة',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع الطاولة المختار بنجاح',
                'data' => [
                    'table' => new TableResource($table),
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
    public function edit(Table $table)
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
            $table = Table::find($id);

            if (!$table) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا التصنيف',
                    'data' => null,
                ], 404);
            }

            $validateUser = Validator::make(
                $request->all(),
                [
                    'table_name' => 'required',
                ],[
                    'table_name.required' => 'يجب إدخال اسم الطاولة!',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $table->update([
                'table_name' => $request->table_name,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل الطاولة بنجاح',
                'data' => [
                    'table' => new TableResource($table),
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
            $table = Table::find($id);
            if (!$table) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذه الطاولة',
                    'data' => null,
                ], 404);
            }

            // Delete table
            $table->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الطاولة بنجاح',
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

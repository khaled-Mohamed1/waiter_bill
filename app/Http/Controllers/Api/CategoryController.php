<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\OneCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $categories = Category::with('products')->where('company_id', $user->company_id)->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع جميع التصنيفات بنجاح',
                'data' => [
                    'categories' => CategoryResource::collection($categories),
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
                    'category_name' => 'required',
                ],[
                    'category_name.required' => 'يجب إدخال اسم التصنيف!',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(isset($request->category_image)){
                $imageName = Str::random(32) . "." . $request->category_image->getClientOriginalExtension();
                $path = 'https://testing.pal-lady.com/storage/app/public/categories/' . $imageName;
                Storage::disk('public')->put('categories/' . $imageName, file_get_contents($request->category_image));
            }

            $user = auth()->user();

            $category = Category::create([
                'category_name' => $request->category_name,
                'category_color' => $request->category_color,
                'category_image' => $path ?? null,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء التصنيف بنجاح',
                'data' => [
                    'category' => new CategoryResource($category),
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
            $category = Category::with('products')->where('company_id', $user->company_id)->where('id', $id)->first();

            if(!$category){
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على هذا التصنيف',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع التصنيف المختار بنجاح',
                'data' => [
                    'category' => new CategoryResource($category),
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
    public function edit(Category $category)
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
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا التصنيف',
                    'data' => null,
                ], 404);
            }

            $validateUser = Validator::make(
                $request->all(),
                [
                    'category_name' => 'required',
                ],[
                    'category_name.required' => 'يجب إدخال اسم التصنيف!',
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
            if(isset($request->category_image)){
                if ($storage->exists('categories/' . basename($category->category_image)))
                    $storage->delete('categories/' . basename($category->category_image));
                $imageName = Str::random(32) . "." . $request->category_image->getClientOriginalExtension();
                $path = 'https://testing.pal-lady.com/storage/app/public/categories/' . $imageName;
                Storage::disk('public')->put('categories/' . $imageName, file_get_contents($request->category_image));
            }

            $category->update([
                'category_name' => $request->category_name,
                'category_color' => $request->category_color,
                'category_image' => $path ?? $category->category_image,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل التصنيف بنجاح',
                'data' => [
                    'category' => new CategoryResource($category),
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
            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا التصنيف',
                    'data' => null,
                ], 404);
            }
            // Public storage
            $storage = Storage::disk('public');
            if ($storage->exists('categories/' . basename($category->category_image)))
                $storage->delete('categories/' . basename($category->category_image));

            // Delete Category
            $category->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف التصنيف بنجاح',
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

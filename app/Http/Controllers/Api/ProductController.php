<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $products = Product::where('company_id', $user->company_id)->paginate();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع جميع المنتجات بنجاح',
                'data' => [
                    'products' => ProductResource::collection($products),
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
                    'product_name' => 'required',
                    'sold_by' => 'required',
                    'product_price' => 'required|numeric',
                    'product_cost' => 'required|numeric',
                ],[
                    'product_name.required' => 'يجب إدخال اسم المنتج!',
                    'sold_by.required' => 'يجب إدخال نوع المنتج من حيث الوزن او الوحدة!',
                    'product_price.required' => 'يجب إدخال سعر المنتج!',
                    'product_price.numeric' => 'يجب إدخال سعر المنتج بالأرقام!',
                    'product_cost.required' => 'يجب إدخال تكلفة المنتج!',
                    'product_cost.numeric' => 'يجب إدخال تكلفة المنتج بالأرقام!',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(isset($request->product_image)){
                $imageName = Str::random(32) . "." . $request->product_image->getClientOriginalExtension();
                $path = 'https://testing.pal-lady.com/storage/app/public/products/' . $imageName;
                Storage::disk('public')->put('products/' . $imageName, file_get_contents($request->product_image));
            }

            $user = auth()->user();

            $product = Product::create([
                'product_name' => $request->product_name,
                'product_color' => $request->product_color,
                'product_image' => $path ?? null,
                'product_price' => $request->product_price,
                'product_cost' => $request->product_cost,
                'category_id' => $request->category_id,
                'sku' => $request->sku,
                'bar_code' => $request->bar_code,
                'sold_by' => $request->sold_by,
                'expiration_date' => $request->expiration_date,
                'product_location' => $request->product_location,
                'stock' => $request->stock,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء التصنيف بنجاح',
                'data' => [
                    'product' => new ProductResource($product),
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
            $product = Product::where('company_id', $user->company_id)->where('id', $id)->first();

            if(!$product){
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على هذا المنتج',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع المنتج المختار بنجاح',
                'data' => [
                    'product' => new ProductResource($product),
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
    public function edit(Product $product)
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
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا المنتج',
                    'data' => null,
                ], 404);
            }

            $validateUser = Validator::make(
                $request->all(),
                [
                    'product_name' => 'required',
                    'sold_by' => 'required',
                    'product_price' => 'required|numeric',
                    'product_cost' => 'required|numeric',
                ],[
                    'product_name.required' => 'يجب إدخال اسم المنتج!',
                    'sold_by.required' => 'يجب إدخال نوع المنتج من حيث الوزن او الوحدة!',
                    'product_price.required' => 'يجب إدخال سعر المنتج!',
                    'product_price.numeric' => 'يجب إدخال سعر المنتج بالأرقام!',
                    'product_cost.required' => 'يجب إدخال تكلفة المنتج!',
                    'product_cost.numeric' => 'يجب إدخال تكلفة المنتج بالأرقام!',
                ]
            );

            $storage = Storage::disk('public');
            if(isset($request->product_image)){
                if ($storage->exists('products/' . basename($product->product_image)))
                    $storage->delete('products/' . basename($product->product_image));
                $imageName = Str::random(32) . "." . $request->product_image->getClientOriginalExtension();
                $path = 'https://testing.pal-lady.com/storage/app/public/products/' . $imageName;
                Storage::disk('public')->put('products/' . $imageName, file_get_contents($request->product_image));
            }

            $product->update([
                'product_name' => $request->product_name,
                'product_color' => $request->product_color,
                'product_image' => $path ?? $product->product_image,
                'product_price' => $request->product_price,
                'product_cost' => $request->product_cost,
                'category_id' => $request->category_id,
                'sku' => $request->sku,
                'bar_code' => $request->bar_code,
                'sold_by' => $request->sold_by,
                'expiration_date' => $request->expiration_date,
                'product_location' => $request->product_location,
                'stock' => $request->stock,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل المنتج بنجاح',
                'data' => [
                    'product' => new ProductResource($product),
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
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذا المنتج',
                    'data' => null,
                ], 404);
            }
            // Public storage
            $storage = Storage::disk('public');
            if ($storage->exists('products/' . basename($product->product_image)))
                $storage->delete('products/' . basename($product->product_image));

            // Delete Category
            $product->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج بنجاح',
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\OneReceiptResource;
use App\Http\Resources\ReceiptResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchases;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $receipts = Receipt::with('purchases')->where('company_id', $user->company_id)->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع جميع الإيصالات بنجاح',
                'data' => [
                    'receipts' => new ReceiptResource($receipts),
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

            $user = auth()->user();

            $total = 0;
            $total_discount = 0;

            $purchasesReceiptIds = [];

            foreach ($request->product_id as $key => $product){

                $old_product = Product::find($request->product_id[$key]);

                if(!$old_product){
                    return response()->json([
                        'success' => false,
                        'message' => 'لم يتم العثور على هذا المنتج',
                        'data' => null,
                    ], 404);
                }

                $old_product->decrement('stock', $request->product_quantity[$key]);
                $old_product->save();

                $total += $old_product->product_price;
                $total_discount += $request->product_discount[$key];

                $purchases = Purchases::create([
                    'product_id' => $request->product_id[$key],
                    'receipt_id' ,
                    'quantity' => $request->product_quantity[$key],
                    'discount' => $request->product_discount[$key]
                ]);

                $purchasesReceiptIds[] = $purchases->id;

            }

            $total_summation = round($total - ($total * ($total_discount / 100)));

            $receipt = Receipt::create([
                'receipt_number' => Helper::IDGenerator(new Receipt(), 'receipt_number', 6,'R_'),
                'total' => $total,
                'total_discount' => $total_discount,
                'total_summation' => $total_summation,
                'status_bill' => $request->status_bill,
                'status_pay' => $request->status_pay,
                'amount_paid' => $request->amount_paid,
                'rest'  => $request->rest,
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
            ]);

            if($receipt->receipt_number == 'R_000000'){
                $receipt->receipt_number = 'R_000001';
                $receipt->save();
            }

            // Update the receipt_id for each purchase
            Purchases::whereIn('id', $purchasesReceiptIds)->update([
                'receipt_id' => $receipt->id,
            ]);

            if(isset($request->customer_id)){
                $customer = Customer::find($request->customer_id);

                if($request->status_bill == 'إضافة الباقي للمحفظة'){
                    $customer->increment('wallet', $request->rest);
                }elseif ($request->status_bill == 'فاتورة دائنة'){
                    $customer->decrement('wallet', $request->rest);
                }

                if($request->status_pay == 'الدفع عن طريق المحفظة'){
                    $customer->decrement('wallet', $request->total_summation);
                }

            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الوصل بنجاح',
                'data' => [
                    'receipt' => new ReceiptResource($receipt),
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
            $receipt = Receipt::with('purchases')->where('company_id', $user->company_id)->where('id', $id)->first();

            if(!$receipt){
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على هذا الإيصال',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع الإيصال المختار بنجاح',
                'data' => [
                    'receipt' => new OneReceiptResource($receipt),
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
    public function edit(Receipt $receipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receipt $receipt)
    {
        //
    }
}

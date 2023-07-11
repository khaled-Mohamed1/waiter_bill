<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Receipt;

class OverviewController extends Controller
{
    public function statistics()
    {
        try {
            $user = auth()->user();

            $productCount = Product::where('company_id', $user->company_id)->count();
            $categoryCount = Category::where('company_id', $user->company_id)->count();
            $customerCount = Customer::where('company_id', $user->company_id)->count();
            $receiptCount = Receipt::where('company_id', $user->company_id)->count();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع الإحصائيات بنجاح',
                'data' => [
                    'products' => $productCount,
                    'categories' => $categoryCount,
                    'customers' => $customerCount,
                    'receipts' => $receiptCount,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرجاع الإحصائيات.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

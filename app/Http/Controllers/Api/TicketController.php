<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Product;
use App\Models\Purchases;
use App\Models\Table;
use App\Models\Ticket;
use App\Models\TicketPurchases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $tickets = Ticket::with('ticketPurchases')->where('company_id', $user->company_id)->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع جميع التذاكر بنجاح',
                'data' => [
                    'tickets' => TicketResource::collection($tickets),
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

            $purchasesTicketIds = [];

            foreach ($request->product_id as $key => $product) {
                $old_product = Product::find($request->product_id[$key]);

                if (!$old_product) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'لم يتم العثور على هذا المنتج',
                        'data' => null,
                    ], 404);
                }

                $ticket_purchases = TicketPurchases::create([
                    'product_id' => $request->product_id[$key],
                    'ticket_id' => null,
                    'quantity' => $request->product_quantity[$key],
                    'price' => $request->product_price[$key],
                    'discount' => $request->product_discount[$key]
                ]);

                $purchasesTicketIds[] = $ticket_purchases->id;
            }

            if($request->ticket_type == 0){
                $table = Table::find($request->table_id);
            }else{
                $ticket_name = $request->ticket_name;

            }

            $ticket = Ticket::create([
                'ticket_name' => $ticket_name ?? $table->table_name,
                'ticket_total' => $request->ticket_total,
                'ticket_type' => $request->ticket_type,
                'table_id' => $table->id ?? null,
                'ticket_total_discount' => $request->tickticket_total_discountet_type,
                'ticket_total_summation' => $request->ticket_total_summation,
                'ticket_paid' => $request->ticket_paid,
                'ticket_rest' => $request->ticket_rest,
                'ticket_payment' => $request->ticket_payment,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            // Update the receipt_id for each purchase
            TicketPurchases::whereIn('id', $purchasesTicketIds)->update([
                'ticket_id' => $ticket->id,
            ]);

            DB::commit();
            $ticket = Ticket::with('ticketPurchases')->find($ticket->id);
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء التذكرة بنجاح',
                'data' => [
                    'tickets' => new TicketResource($ticket),
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
            $ticket = Ticket::with('ticketPurchases')->where('company_id', $user->company_id)->where('id', $id)->first();

            if(!$ticket){
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على هذه التذكرة',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع التذكرة المختار بنجاح',
                'data' => [
                    'ticket' => new TicketResource($ticket),
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
    public function edit(Ticket $ticket)
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
            $ticket = Ticket::find($id);

            $ticket->update([
                'ticket_name' => $request->ticket_name,
                'ticket_total' => $request->ticket_total,
                'ticket_type' => $request->ticket_type,
                'company_id' => $user->company_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل الطاولة بنجاح',
                'data' => [
                    'ticket' => new TicketResource($ticket),
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
            $tickets = Table::find($id);
            if (!$tickets) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذه التذكرة',
                    'data' => null,
                ], 404);
            }

            // Delete tickets
            $tickets->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف التذكرة بنجاح',
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

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

            if(isset($request->ticket_paid)){
                $ticket_status = 'منتهية';
            }


            $ticket = Ticket::create([
                'ticket_name' => $ticket_name ?? $table->table_name,
                'ticket_total' => $request->ticket_total,
                'ticket_type' => $request->ticket_type,
                'table_id' => $table->id ?? null,
                'ticket_total_discount' => $request->ticket_total_discount,
                'ticket_total_summation' => $request->ticket_total_summation,
                'ticket_paid' => $request->ticket_paid,
                'ticket_rest' => $request->ticket_rest,
                'ticket_payment' => $request->ticket_payment,
                'ticket_status' => $ticket_status ?? 'مستمرة',
                'ticket_note' => $request->ticket_note,
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

    public function paid(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $user = auth()->user();
            // Detail
            $ticket = Table::find($id);
            if (!$ticket) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذه التذكرة',
                    'data' => null,
                ], 404);
            }

            $ticket->update([
                'ticket_paid' => $request->ticket_paid,
                'ticket_rest' => $request->ticket_rest,
                'ticket_payment' => $request->ticket_payment,
                'ticket_status' => 'منتهبة',
            ]);

            DB::commit();
            $ticket = Ticket::with('ticketPurchases')->find($ticket->id);
            return response()->json([
                'success' => true,
                'message' => 'تم دفع التذكرة بنجاح',
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
    public function update(Request $request, $ticketId)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $ticket = Ticket::find($ticketId);

            if (!$ticket) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على هذه التذكرة',
                    'data' => null,
                ], 404);
            }

            $ticket->ticket_name = $request->ticket_name ?? $ticket->ticket_name;
            $ticket->ticket_total = $request->ticket_total ?? $ticket->ticket_total;
            $ticket->ticket_type = $request->ticket_type ?? $ticket->ticket_type;

            if ($request->ticket_type == 0) {
                $ticket->table_id = $request->table_id ?? $ticket->table_id;
            } else {
                $ticket->ticket_name = $request->ticket_name ?? $ticket->ticket_name;
            }

            $ticket->ticket_total_discount = $request->ticket_total_discount ?? $ticket->ticket_total_discount;
            $ticket->ticket_total_summation = $request->ticket_total_summation ?? $ticket->ticket_total_summation;
            $ticket->ticket_paid = $request->ticket_paid ?? $ticket->ticket_paid;
            $ticket->ticket_rest = $request->ticket_rest ?? $ticket->ticket_rest;
            $ticket->ticket_payment = $request->ticket_payment ?? $ticket->ticket_payment;

            if (isset($request->ticket_paid)) {
                $ticket->ticket_status = 'منتهية';
            } else {
                $ticket->ticket_status = $ticket->ticket_status ?? 'مستمرة';
            }

            $ticket->ticket_note = $request->ticket_note ?? $ticket->ticket_note;

            $ticket->save();

            $purchasesTicketIds = [];

            // Delete existing ticket purchases
            TicketPurchases::where('ticket_id', $ticket->id)->delete();

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

                $ticket_purchase = TicketPurchases::create([
                    'product_id' => $request->product_id[$key],
                    'ticket_id' => $ticket->id,
                    'quantity' => $request->product_quantity[$key],
                    'price' => $request->product_price[$key],
                    'discount' => $request->product_discount[$key]
                ]);

                $purchasesTicketIds[] = $ticket_purchase->id;
            }

            DB::commit();

            // Delete any ticket purchases that were not included in the request
            TicketPurchases::where('ticket_id', $ticket->id)->whereNotIn('id', $purchasesTicketIds)->delete();

            DB::commit();

            $ticket = Ticket::with('ticketPurchases')->find($ticket->id);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التذكرة بنجاح',
                'data' => [
                    'tickets' => new TicketResource($ticket),
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
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
            $ticket = Table::find($id);
            if (!$ticket) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على هذه التذكرة',
                    'data' => null,
                ], 404);
            }

            // Delete tickets
            $ticket->delete();

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

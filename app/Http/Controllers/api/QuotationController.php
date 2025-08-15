<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Quotation;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Services\QuotationService;

class QuotationController extends Controller
{




    public function store(StoreQuotationRequest $request, QuotationService $service)
    {
        $quotation = $service->store($request->validated());
        return response()->json($quotation, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation, QuotationService $service)
    {
        $quotation = $service->update($quotation, $request->validated());
        return response()->json($quotation, 200);
    }

    public function destroy(Quotation $quotation, QuotationService $service)
        {
            $result = $service->deleteQuotation($quotation);

            if ($result['status']) {
                return response()->json(['message' => $result['message']], 200);
            }

            return response()->json([
                'error' => $result['message'],
                'details' => $result['error']
            ], 500);
        }


public function byCustomer($customerId)
{
    $customer = Customer::with('quotations.items')->findOrFail($customerId);

    $mappedQuotations = $customer->quotations->map(function ($quotation) {
          $totalItems = $quotation->items->sum('quantity');
        return [
            'id' => $quotation->id,
            'customer_id' => $quotation->customer_id,
            'quotation_date' => $quotation->quotation_date,
            'grand_total' => $quotation->grand_total,
            'total_items' => $totalItems,
            'created_at' => $quotation->created_at,
            'updated_at' => $quotation->updated_at,
            'items' => $quotation->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quotation_id' => $item->quotation_id,
                    'product_name' => $item->product_name,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'subtotal' => $item->subtotal,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            }),
        ];
    });

    return response()->json($mappedQuotations);
}


    public function sendEmail(Request $request, Quotation $quotation, QuotationService $service)
        {
            $result = $service->sendQuotationEmail($quotation);

            if ($result['status']) {
                return response()->json([
                    'message' => $result['message'],
                    'brevo_response' => $result['brevo_response']
                ]);
            }

            return response()->json([
                'error' => $result['error'],
                'status_code' => $result['status_code'],
                'brevo_response' => $result['brevo_response']
            ], 500);
        }


}

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
        return response()->json($customer->quotations);
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

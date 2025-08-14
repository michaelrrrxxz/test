<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;


use App\Models\Quotation;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


use App\Libraries\Brevo;

use App\Services\QuotationService;

class QuotationController extends Controller
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

    public function store(StoreQuotationRequest $request, QuotationService $service)
    {
        $quotation = $service->store($request->validated());
        return response()->json($quotation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation, QuotationService $service)
    {
        $quotation = $service->update($quotation, $request->validated());
        return response()->json($quotation, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        // Start a DB transaction
        \DB::beginTransaction();

        try {
            // Delete the quotation and its items
            $quotation->items()->delete();
            $quotation->delete();

            \DB::commit();

            return response()->json(['message' => 'Quotation deleted successfully'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => 'Failed to delete quotation', 'message' => $e->getMessage()], 500);
        }
    }

    public function byCustomer($customerId)
    {
        $customer = Customer::with('quotations.items')->findOrFail($customerId);
        return response()->json($customer->quotations);
    }

    public function sendEmail(Request $request, Quotation $quotation)
    {
        $customer = $quotation->customer;
        $htmlContent = view('emails.quotation', compact('quotation', 'customer'))->render();

        $brevo = new Brevo();
        $result = $brevo->sendQuotationEmail(
            $customer->email,
            $customer->name,
            'Your Quotation',
            $htmlContent
        );

        if ($result['success']) {
            return response()->json([
                'message' => 'Quotation sent to customer.',
                'brevo_response' => $result['response']
            ]);
        }

        return response()->json([
            'error' => 'Brevo API request failed',
            'status_code' => $result['status_code'],
            'brevo_response' => $result['response']
        ], 500);
    }



}

<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;


use App\Models\Quotation;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;




use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
public function store(StoreQuotationRequest $request)
{
    $data = $request->validated();

    // Start a DB transaction in case something fails
    \DB::beginTransaction();

    try {
        // Create the quotation without totals first
        $quotation = \App\Models\Quotation::create([
            'customer_id' => $data['customer_id'],
            'quotation_date' => $data['quotation_date'],
            // We'll calculate these after creating items
            'grand_total' => 0,
            'total_items' => 0,
        ]);

        $grandTotal = 0;
        $totalItems = 0;

        // Loop through each item and create them linked to this quotation
        foreach ($data['items'] as $item) {
            $quantity = $item['quantity'];
            $price = $item['price'];

            $quotation->items()->create([
                'product_name' => $item['product_name'],
                'quantity' => $quantity,
                 'unit_cost' => $price,
            ]);

            $grandTotal += $quantity * $price;
            $totalItems += $quantity;
        }

        // Update the quotation with the totals
        $quotation->update([
            'grand_total' => $grandTotal,
            'total_items' => $totalItems,
        ]);

        \DB::commit();

        return response()->json($quotation->load('items'), 201);

    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json(['error' => 'Failed to save quotation', 'message' => $e->getMessage()], 500);
    }
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
public function update(UpdateQuotationRequest $request, Quotation $quotation)
{
    $data = $request->validated();

    \DB::beginTransaction();

    try {
        // Update basic quotation details first
        $quotation->update([
            // 'customer_id' => $data['customer_id'],
            'quotation_date' => $data['quotation_date'],
        ]);

        // Remove old items (simplest approach; can be replaced with smarter diffing)
        $quotation->items()->delete();

        $grandTotal = 0;
        $totalItems = 0;

        // Add the updated items
        foreach ($data['items'] as $item) {
            $quantity = $item['quantity'];
            $price = $item['price'];

            $quotation->items()->create([
                'product_name' => $item['product_name'],
                'quantity' => $quantity,
                'unit_cost' => $price,
            ]);

            $grandTotal += $quantity * $price;
            $totalItems += $quantity;
        }

        // Update totals
        $quotation->update([
            'grand_total' => $grandTotal,
            'total_items' => $totalItems,
        ]);

        \DB::commit();

        return response()->json($quotation->load('items'), 200);

    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json([
            'error' => 'Failed to update quotation',
            'message' => $e->getMessage()
        ], 500);
    }
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

    $client = new Client();

    try {
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => trim(env('BREVO_API_KEY')),
                'content-type' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name'  => env('BREVO_SENDER_NAME'),
                    'email' => env('BREVO_SENDER_EMAIL'),
                ],
                'to' => [
                    ['email' => $customer->email, 'name' => $customer->name]
                ],
                'subject' => 'Your Quotation',
                'htmlContent' => view('emails.quotation', compact('quotation', 'customer'))->render()
            ],
        ]);

        return response()->json([
            'message' => 'Quotation sent to customer.',
            'brevo_response' => json_decode($response->getBody(), true)
        ]);

    } catch (ClientException $e) {
        // Show full Brevo error for debugging
        return response()->json([
            'error' => 'Brevo API request failed',
            'status_code' => $e->getResponse()->getStatusCode(),
            'brevo_response' => json_decode($e->getResponse()->getBody(), true)
        ], 500);
    }
}


}

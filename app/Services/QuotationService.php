<?php

namespace App\Services;

use App\Models\Quotation;
use App\Libraries\Brevo;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    protected $brevo;

    public function __construct(Brevo $brevo)
    {
        $this->brevo = $brevo;
    }

    public function store(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $quotation = Quotation::create([
                'customer_id'   => $data['customer_id'],
                'quotation_date'=> $data['quotation_date'],
                'grand_total'   => 0,
                'total_items'   => 0,
            ]);

            [$grandTotal, $totalItems] = $this->Items($quotation, $data['items']);

            $quotation->update([
                'grand_total' => $grandTotal,
                'total_items' => $totalItems,
            ]);

            return $quotation->load('items');
        });
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            $quotation->update([
                'quotation_date'=> $data['quotation_date'],
            ]);

            $quotation->items()->delete();

            [$grandTotal, $totalItems] = $this->Items($quotation, $data['items']);

            $quotation->update([
                'grand_total' => $grandTotal,
                'total_items' => $totalItems,
            ]);

            return $quotation->load('items');
        });
    }

     public function deleteQuotation(Quotation $quotation): array
    {
        DB::beginTransaction();

        try {
            $quotation->items()->delete();
            $quotation->delete();

            DB::commit();
            return [
                'status' => true,
                'message' => 'Quotation deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Failed to delete quotation',
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendQuotationEmail(Quotation $quotation): array
        {
            $customer = $quotation->customer;
            $htmlContent = view('emails.quotation', compact('quotation', 'customer'))->render();

            $result = $this->brevo->sendQuotationEmail(
                $customer->email,
                $customer->name,
                'Your Quotation',
                $htmlContent
            );

            if ($result['success']) {
                return [
                    'status' => true,
                    'message' => 'Quotation sent to customer.',
                    'brevo_response' => $result['response']
                ];
            }

            return [
                'status' => false,
                'error' => 'Brevo API request failed',
                'status_code' => $result['status_code'],
                'brevo_response' => $result['response']
            ];
        }

    protected function Items(Quotation $quotation, array $items): array
    {
        $grandTotal = 0;
        $totalItems = 0;

        foreach ($items as $item) {
            $quantity = $item['quantity'];
            $price    = $item['price'];

            $quotation->items()->create([
                'product_name'     => $item['product_name'],
                'item_description' => $item['item_description'],
                'quantity'         => $quantity,
                'unit_cost'        => $price,
            ]);

            $grandTotal += $quantity * $price;
            $totalItems += $quantity;
        }

        return [$grandTotal, $totalItems];
    }
}

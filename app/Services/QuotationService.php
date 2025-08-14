<?php

namespace App\Services;

use App\Models\Quotation;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function store(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $quotation = Quotation::create([
                'customer_id'   => $data['customer_id'],
                'quotation_date'=> $data['quotation_date'],
                'grand_total'   => 0,
                'total_items'   => 0,
            ]);

            [$grandTotal, $totalItems] = $this->syncItems($quotation, $data['items']);

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

            [$grandTotal, $totalItems] = $this->syncItems($quotation, $data['items']);

            $quotation->update([
                'grand_total' => $grandTotal,
                'total_items' => $totalItems,
            ]);

            return $quotation->load('items');
        });
    }

    protected function syncItems(Quotation $quotation, array $items): array
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

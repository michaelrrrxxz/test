<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->id }}</title>
</head>
<body style="margin:0; padding:0; font-family: Inter, Arial, sans-serif; background-color: #f9fafb; color: #111827;">

    <!-- Container -->
    <div style="max-width: 640px; margin: 0 auto; padding: 24px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">

        <!-- Header -->
        <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 20px;">
            <h1 style="font-size: 20px; font-weight: 600; margin: 0;">Quotation #{{ $quotation->id }}</h1>
            <p style="margin: 4px 0 0; font-size: 14px; color: #6b7280;">
                Date: {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F j, Y') }}
            </p>
        </div>

        <!-- Customer Info -->
        <div style="margin-bottom: 20px;">
            <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">Customer Information</h2>
            <div style="padding: 12px; background-color: #f3f4f6; border-radius: 8px;">
                <p style="margin: 0;"><strong>Name:</strong> {{ $customer->name ?? 'N/A' }}</p>
                <p style="margin: 4px 0;"><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
                <p style="margin: 0;"><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Quotation Details -->
        <div>
            <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">Quotation Details</h2>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: left; padding: 8px;">Product Name</th>
                        <th style="text-align: left; padding: 8px;">Description</th>
                        <th style="text-align: center; padding: 8px;">Qty</th>
                        <th style="text-align: right; padding: 8px;">Unit Cost</th>
                        <th style="text-align: right; padding: 8px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotation->items as $item)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 8px;">{{ $item->product_name }}</td>
                            <td style="padding: 8px;">{{ $item->item_description ?? '-' }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $item->quantity }}</td>
                            <td style="text-align: right; padding: 8px;">${{ number_format($item->unit_cost, 2) }}</td>
                            <td style="text-align: right; padding: 8px;">${{ number_format($item->quantity * $item->unit_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; padding: 8px; font-weight: 600; background-color: #f9fafb;">Grand Total:</td>
                        <td style="text-align: right; padding: 8px; font-weight: 600; background-color: #f9fafb;">
                            ${{ number_format($quotation->grand_total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer -->
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            Thank you for your business! If you have any questions, feel free to reply to this email.
        </p>
    </div>

</body>
</html>

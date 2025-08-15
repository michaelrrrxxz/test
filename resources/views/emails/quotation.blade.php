<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quotation #{{ $quotation->id }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f9fafb;
            color: #111827;
            line-height: 1.5;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            padding: 24px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        header {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        header p {
            margin: 4px 0 0;
            font-size: 0.875rem;
            color: #6b7280;
        }

        h2 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .info-box {
            padding: 12px;
            background-color: #f3f4f6;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .info-box p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        thead th {
            text-align: left;
            padding: 8px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        thead th:nth-child(2) {
            text-align: center;
        }
        thead th:nth-child(3),
        thead th:nth-child(4) {
            text-align: right;
        }

        tbody td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody td:nth-child(2) {
            text-align: center;
        }
        tbody td:nth-child(3),
        tbody td:nth-child(4) {
            text-align: right;
        }

        tfoot td {
            padding: 8px;
            font-weight: 600;
            background-color: #f9fafb;
        }
        tfoot td:first-child {
            text-align: right;
        }
        tfoot td:last-child {
            text-align: right;
        }

        footer {
            margin-top: 20px;
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Mobile responsiveness */
        @media (max-width: 600px) {
            .container {
                padding: 16px;
                border-radius: 0;
            }
            table, thead, tbody, tfoot, tr, td, th {
                display: block;
                width: 100%;
            }
            thead {
                display: none;
            }
            tbody tr {
                margin-bottom: 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 8px;
                background: #fff;
            }
            tbody td {
                border: none;
                padding: 6px 0;
                text-align: left !important;
            }
            tfoot td {
                display: block;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <h1>Quotation #{{ $quotation->id }}</h1>
            <p>Date: {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F j, Y') }}</p>
        </header>

        <!-- Customer Info -->
        <section>
            <h2>Customer Information</h2>
            <div class="info-box">
                <p><strong>Name:</strong> {{ $customer->name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
            </div>
        </section>

        <!-- Quotation Details -->
        <section style="margin-top: 20px;">
            <h2>Quotation Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit Cost</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotation->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                            <td>₱{{ number_format($item->quantity * $item->unit_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Grand Total:</td>
                        <td>₱{{ number_format($quotation->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <!-- Footer -->
        <footer>
            Thank you for your business! If you have any questions, feel free to reply to this email.
        </footer>
    </div>
</body>
</html>

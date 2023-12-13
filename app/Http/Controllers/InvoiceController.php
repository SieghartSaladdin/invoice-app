<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Counter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function get_all_invoice()
    {
        $invoices = Invoice::with('customer')->orderBy('id', 'DESC')->get();
        return response()->json([
            'invoice' => $invoices
        ],200);
    }

    public function search_invoice(Request $request)
    {
        $search = $request->get('s');
        if ($search != null) {
            $invoices = Invoice::with('customer')
            ->where('id', 'LIKE', "%$search%")
            ->get();

            return response()->json([
                'invoices' => $invoices
            ]);
        } else {
            return response()->json([
                'invoices' => Invoice::with('customer')->orderBy('id', 'DESC')->get()
            ]);
        }
    }

    public function create_invoice()
    {
        $counter = Counter::where('key', 'invoice')->first();

        $invoice = Invoice::orderBy('id', 'DESC')->first();
        if($invoice){
            $invoice = $invoice->id+1;
            $counters = $counter->value + $invoice;
        }else {
            $counters = $counter->value;
        }

        $formData = [
            'number' => $counter->prefix.$counters,
            'customer_id' => null,
            'customer' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'terms_and_condition' => 'Default Terms And Codintions',
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'quantity' => 0,
                ],
            ]
        ];

        return response()->json($formData);
    }

    public function add_invoice(Request $request)
    {
        $invoiceData = $request->validate([
            'sub_total' => 'required|numeric',
            'total' => 'required|numeric',
            'customer_id' => 'required|integer',
            'number' => 'required|string',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'discount' => 'required|numeric',
            'reference' => 'nullable|string',
            'terms_and_condition' => 'required|string',
            'invoice_item' => 'required|json',
        ]);

        $invoiceItemData = json_decode($invoiceData['invoice_item'], true);

        $invoice = Invoice::create($invoiceData);

        foreach ($invoiceItemData as $item) {
            $itemData = [
                'product_id' => $item['product_id'],
                'invoice_id' => $invoice->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ];

            InvoiceItem::create($itemData);
        }

        return response()->json(['message' => 'Invoice created successfully'], 201);
    }

    public function show_invoice($id) 
    {
        $invoice = Invoice::with(['customer', 'invoice_items.product'])->find($id);

        return response()->json([
            'invoice' => $invoice
        ]);
    }

    public function delete_invoice_items($id)
    {
        $invoiceItems = InvoiceItem::find($id);
        $invoiceItems->delete();

        return response()->json('Item Sudah Berhasil Di Delete');
    }

    public function update_invoice(Request $request, $invoiceId)
    {
        $invoiceData = $request->validate([
            'sub_total' => 'required|numeric',
            'total' => 'required|numeric',
            'customer_id' => 'required|integer',
            'number' => 'required|string',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'discount' => 'required|numeric',
            'reference' => 'nullable|string',
            'terms_and_condition' => 'required|string',
            'invoice_item' => 'required|json',
        ]);

        $invoiceItemData = json_decode($invoiceData['invoice_item'], true);

        // Find the existing invoice
        $invoice = Invoice::findOrFail($invoiceId);

        // Delete existing invoice items
        $invoice->invoice_items()->delete();

        // Update invoice details
        $invoice->update($invoiceData);

        // Create new invoice items
        foreach ($invoiceItemData as $item) {
            $itemData = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ];

            $invoice->invoice_items()->create($itemData);
        }

        return response()->json(['message' => 'Invoice updated successfully'], 200);
    }

    public function delete_invoice($id)
    {
        $invoice = Invoice::find($id);

        $invoice->invoice_items()->delete();
        $invoice->delete();
    }

}

<?php

namespace App\Helpers\Transformers;

use App\Models\Sales\Invoice;
use App\Models\Sales\InvoiceItem;
use App\Models\Sales\SaleStatus;

final class InvoiceTransformer
{
    public static function saleStatus(SaleStatus $saleStatus)
    {
        return [
            'id' => $saleStatus->id,
            'name' => $saleStatus->name,
        ];
    }

    public static function invoice(Invoice $invoice)
    {
        return [
            'id' => $invoice->id,
            'company' => CompanyTransformer::company($invoice->company),
            'branch' => CompanyTransformer::branch($invoice->branch),
            'created_by' => EmployeeTransformer::employee($invoice->employee),
            'description' => $invoice->description,
            'invoice_number' => $invoice->invoice_number,
            'total_amount' => $invoice->total_amount,
            'discount_amount' => $invoice->discount_amount,
            'shipping_amount' => $invoice->shipping_amount,
            'tax_amount' => $invoice->tax_amount,
            'grand_total' => $invoice->grand_total,
            'sale_status' => self::saleStatus($invoice->saleStatus),
            'customer' => CustomerReferenceTransformer::invoiceCustomer($invoice->invoiceCustomer),
            'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
            'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
            'notes' => $invoice->notes,
        ];
    }

    public static function invoiceItem(InvoiceItem $invoiceItem)
    {
        return [
            'id' => $invoiceItem->id,
            'product_id' => $invoiceItem->product_id,
            'name' => $invoiceItem->name,
            'type' => $invoiceItem->type,
            'sku' => $invoiceItem->sku,
            'description' => $invoiceItem->description,
            'price' => $invoiceItem->price,
            'discount' => $invoiceItem->discount,
            'tax_percentage' => $invoiceItem->tax_percentage,
            'tax_amount' => $invoiceItem->tax_amount,
            'quantity' => $invoiceItem->quantity,
            'total' => $invoiceItem->total,
        ];
    }

    public static function invoiceWithItems(Invoice $invoice)
    {
        return [
            'id' => $invoice->id,
            'company' => CompanyTransformer::company($invoice->company),
            'branch' => CompanyTransformer::branch($invoice->branch),
            'created_by' => EmployeeTransformer::employee($invoice->employee),
            'company_bank' => CompanyTransformer::bank($invoice->companyBank),
            'description' => $invoice->description,
            'invoice_number' => $invoice->invoice_number,
            'total_amount' => $invoice->total_amount,
            'discount_amount' => $invoice->discount_amount,
            'shipping_amount' => $invoice->shipping_amount,
            'tax_amount' => $invoice->tax_amount,
            'grand_total' => $invoice->grand_total,
            'sale_status' => self::saleStatus($invoice->saleStatus),
            'customer' => CustomerReferenceTransformer::invoiceCustomer($invoice->invoiceCustomer),
            'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
            'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
            'notes' => $invoice->notes,
            'items' => $invoice->items->transform(fn($item) => self::invoiceItem($item)),
        ];
    }
}

<?php

namespace App\Helpers\Transformers;

use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationItem;
use App\Models\Sales\SaleStatus;

final class QuotationTransformer
{
    public static function saleStatus(SaleStatus $saleStatus)
    {
        return [
            'id' => $saleStatus->id,
            'name' => $saleStatus->name,
        ];
    }

    public static function quotation(Quotation $quotation)
    {
        return [
            'id' => $quotation->id,
            'company' => CompanyTransformer::company($quotation->company),
            'branch' => CompanyTransformer::branch($quotation->branch),
            'created_by' => EmployeeTransformer::employee($quotation->employee),
            'description' => $quotation->description,
            'quotation_number' => $quotation->quotation_number,
            'total_amount' => $quotation->total_amount,
            'discount_amount' => $quotation->discount_amount,
            'shipping_amount' => $quotation->shipping_amount,
            'grand_total' => $quotation->grand_total,
            'sale_status' => self::saleStatus($quotation->saleStatus),
            'customer' => CustomerReferenceTransformer::customerReference($quotation->customerReferences),
            'quotation_date' => $quotation->quotation_date->format('Y-m-d'),
            'notes' => $quotation->notes,
        ];
    }

    public static function quotationItem(QuotationItem $quotationItem)
    {
        return [
            'id' => $quotationItem->id,
            'name' => $quotationItem->name,
            'type' => $quotationItem->type,
            'sku' => $quotationItem->sku,
            'description' => $quotationItem->description,
            'price' => $quotationItem->price,
            'discount' => $quotationItem->discount,
            'tax_percentage' => $quotationItem->tax_percentage,
            'tax_amount' => $quotationItem->tax_amount,
            'quantity' => $quotationItem->quantity,
            'total' => $quotationItem->total,
        ];
    }

    public static function quotationWithItems(Quotation $quotation)
    {
        return [
            'id' => $quotation->id,
            'company' => CompanyTransformer::company($quotation->company),
            'branch' => CompanyTransformer::branch($quotation->branch),
            'created_by' => EmployeeTransformer::employee($quotation->employee),
            'description' => $quotation->description,
            'quotation_number' => $quotation->quotation_number,
            'total_amount' => $quotation->total_amount,
            'discount_amount' => $quotation->discount_amount,
            'shipping_amount' => $quotation->shipping_amount,
            'grand_total' => $quotation->grand_total,
            'sale_status' => self::saleStatus($quotation->saleStatus),
            'customer' => CustomerReferenceTransformer::customerReference($quotation->customerReferences),
            'quotation_date' => $quotation->quotation_date->format('Y-m-d'),
            'notes' => $quotation->notes,
            'items' => $quotation->items->transform(fn($item) => self::quotationItem($item)),
        ];
    }
}

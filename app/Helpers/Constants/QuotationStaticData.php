<?php

namespace App\Helpers\Constants;

final class QuotationStaticData
{
    public static function statuses(): array
    {
        return [
            ['code' => 'draft', 'value' => 'Draft'],
            ['code' => 'sent', 'value' => 'Sent'],
            ['code' => 'approved', 'value' => 'Approved'],
            ['code' => 'rejected', 'value' => 'Rejected'],
            ['code' => 'completed', 'value' => 'Completed'],
        ];
    }
}

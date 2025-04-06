<?php

namespace App\Enums;

enum PdfValues: string
{
    case DATE = 'Date';
    case INVOICE_NUMBER = 'Invoice Number';

    case INVOICE_TO = 'Invoice To';

    case SHIP_TO = 'Ship To';

    case SUB_TOTAL = 'Sub Total';

    case GST_TOTAL = 'GST Total';

    case TOTAL = 'Total';

    case QUANTITY = 'Quantity';

    case DESCRIPTION = 'Description';

    /**
     * Find ENUM by name or value
     *
     *
     * @param  mixed  $needle
     *
     * @return PdfValues|null
     */
    public static function find(mixed $needle): ?self
    {
        if (in_array($needle, self::names())) {
            return constant("self::{$needle}");
        }
        if (in_array($needle, self::values())) {
            return self::tryFrom($needle);
        }

        return null;
    }

    /**
     * Get all ENUM names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all ENUM values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all ENUM name => value
     */
    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}

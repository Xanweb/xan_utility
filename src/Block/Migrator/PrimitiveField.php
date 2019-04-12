<?php
namespace XanUtility\Block\Migrator;

use Doctrine\DBAL\Types\Type;

class PrimitiveField
{
    public static function sanitizeFieldValue($type, $value)
    {
        switch ($type) {
            case Type::INTEGER:
            case Type::SMALLINT:
                $sanitizedVal = (int) $value;
                break;
            case Type::STRING:
                $sanitizedVal = trim((string) $value);
                break;
            case Type::BOOLEAN:
                $sanitizedVal = $value ? 1 : 0;
                break;
            default:
                $sanitizedVal = $value;
                break;
        }

        return $sanitizedVal;
    }
}

<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package   jtl\Connector\Shopware\Utilities
 */

namespace JtlWooCommerceConnector\Utilities;

use Jtl\Connector\Core\Definition\IdentityType;

class Id
{
    const SEPARATOR = '_';
    const PRODUCT_PREFIX = 'p';
    const CATEGORY_PREFIX = 'c';
    const GUEST_PREFIX = 'g';
    const MANUFACTURER_PREFIX = 'm';
    
    public static function link(array $endpointIds)
    {
        return implode(self::SEPARATOR, $endpointIds);
    }
    
    public static function unlink($endpointId)
    {
        return explode(self::SEPARATOR, $endpointId);
    }
    
    public static function linkProductImage($imageId, $productId)
    {
        return self::link([self::PRODUCT_PREFIX, $imageId, $productId]);
    }

    public static function unlinkImage($endpointId)
    {
        list($typePrefix, $parts) = explode(self::SEPARATOR, $endpointId, 2);

        if ($typePrefix === self::CATEGORY_PREFIX) {
            return [$parts, IdentityType::CATEGORY_IMAGE];
        }

        if ($typePrefix === self::PRODUCT_PREFIX) {
            return [$parts, IdentityType::PRODUCT_IMAGE];
        }

        if ($typePrefix === self::MANUFACTURER_PREFIX) {
            return [$parts, IdentityType::MANUFACTURER_IMAGE];
        }

        return null;
    }

    public static function linkCategoryImage($attachmentId)
    {
        return self::link([self::CATEGORY_PREFIX, $attachmentId]);
    }
    
    public static function unlinkCategoryImage($endpoint)
    {
        if (strstr($endpoint, self::CATEGORY_PREFIX . self::SEPARATOR)) {
            return self::unlink($endpoint)[1];
        }
        
        return '';
    }
    
    public static function linkManufacturerImage($attachmentId)
    {
        return self::link([self::MANUFACTURER_PREFIX, $attachmentId]);
    }
    
    public static function unlinkManufacturerImage($endpoint)
    {
        if (strstr($endpoint, self::MANUFACTURER_PREFIX . self::SEPARATOR)) {
            return self::unlink($endpoint)[1];
        }
        
        return '';
    }

    public static function unlinkCustomer($endpointId)
    {
        return [$endpointId, (int)(strpos($endpointId, self::SEPARATOR) !== false)];
    }
}

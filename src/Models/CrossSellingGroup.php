<?php

namespace JtlWooCommerceConnector\Models;

use jtl\Connector\Model\CrossSellingGroupI18n;
use jtl\Connector\Model\Identity;
use JtlWooCommerceConnector\Utilities\Util;

/**
 * Class CrossSelling
 * @package JtlWooCommerceConnector\Models
 */
class CrossSellingGroup
{
    public const TYPE_CROSS_SELL = "1";
    public const TYPE_UP_SELL    = "2";

    /**
     * @var array
     */
    protected static array $groups = [
        [
            'endpointId' => self::TYPE_CROSS_SELL,
            'name' => 'WooCommerce-CrossSelling',
            'woo_commerce_name' => '_crosssell_ids',
        ],
        [
            'endpointId' => self::TYPE_UP_SELL,
            'name' => 'WooCommerce-UpSell',
            'woo_commerce_name' => '_upsell_ids',
        ]
    ];

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function all(): array
    {
        $groups = [];
        foreach (self::$groups as $group) {
            $groups[] = self::createFromArray($group);
        }
        return $groups;
    }

    /**
     * @param $name
     * @throws \InvalidArgumentException
     */
    public static function getByWooCommerceName($name)
    {
        $key = self::findKeyByColumn('woo_commerce_name', $name);

        if ($key === false) {
            return false;
        }

        $group = self::$groups[$key];

        return self::createFromArray($group);
    }

    /**
     * @param array $groupData
     * @return \jtl\Connector\Model\CrossSellingGroup
     * @throws \InvalidArgumentException
     */
    protected static function createFromArray(array $groupData): \jtl\Connector\Model\CrossSellingGroup
    {
        $crossSellingGroup = new \jtl\Connector\Model\CrossSellingGroup();
        $crossSellingGroup->setId(new Identity($groupData['endpointId']));

        $i18n = new CrossSellingGroupI18n();
        $i18n->setLanguageISO(Util::getInstance()->getWooCommerceLanguage());
        $i18n->setName($groupData['name']);

        $crossSellingGroup->addI18n($i18n);

        return $crossSellingGroup;
    }

    /**
     * @param $columnName
     * @param $value
     */
    protected static function findKeyByColumn($columnName, $value)
    {
        return \array_search($value, \array_column(self::$groups, $columnName));
    }
}

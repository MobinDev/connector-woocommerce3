<?php

/**
 * @author    Jan Weskamp <jan.weskamp@jtl-software.com>
 * @copyright 2010-2013 JTL-Software GmbH
 */

namespace JtlWooCommerceConnector\Utilities;

use jtl\Connector\Model\Category as CategoryModel;

class Category
{
    public const TERM_TAXONOMY               = 'product_cat';
    public const LEVEL_TABLE                 = 'jtl_connector_category_level';
    public const OPTION_CATEGORY_HAS_CHANGED = 'jtlconnector_category_has_changes';

    public static function fillCategoryLevelTable(array $parentIds = null, $level = 0): void
    {
        global $wpdb;
        $where = ' AND tt.parent = 0';

        if ($parentIds === null) {
            Db::getInstance()->query(\sprintf(
                'TRUNCATE TABLE `%s%s`',
                $wpdb->prefix,
                self::LEVEL_TABLE
            ));
        } else {
            $where = 'AND tt.parent IN (' . \implode(',', $parentIds) . ')';
        }

        $parentIds = [];

        $categories = Db::getInstance()->query(SqlHelper::categoryTreeGet($where));

        if (! empty($categories)) {
            $sort    = 0;
            $parents = [];

            foreach ($categories as $category) {
                $categoryId       = (int) $category['term_id'];
                $parentCategoryId = (int) $category['parent'];

                if (! \in_array($parentCategoryId, $parents)) {
                    $sort      = 0;
                    $parents[] = $parentCategoryId;
                }

                $parentIds[] = $categoryId;

                Db::getInstance()->query(SqlHelper::categoryTreeAddIgnore($categoryId, $level, $sort++));
            }

            self::fillCategoryLevelTable($parentIds, $level + 1);
        }
    }

    /**
     * @param array|null $parent
     * @param $count
     * @return void
     */
    public static function saveCategoryLevelsAsPreOrder(array $parent = null, &$count = 0): void
    {
        if ($count === 0) {
            $categories = Db::getInstance()->query(SqlHelper::categoryTreePreOrderRoot());
        } else {
            $query      = SqlHelper::categoryTreePreOrder($parent['category_id'], $parent['level'] + 1);
            $categories = Db::getInstance()->query($query);
        }
        foreach ((array) $categories as $category) {
            \wc_set_term_order($category['category_id'], ++$count, 'product_cat');
            self::saveCategoryLevelsAsPreOrder($category, $count);
        }
    }

    /**
     * @param CategoryModel $category
     * @param $isNew
     * @return void
     */
    public static function updateCategoryTree(CategoryModel $category, $isNew): void
    {
        $id = $category->getId()->getEndpoint();

        if ($isNew) {
            $categoryTreeQuery = SqlHelper::categoryTreeAdd($id, $category->getLevel(), $category->getSort());
        } else {
            $categoryTreeQuery = SqlHelper::categoryTreeUpdate($id, $category->getLevel(), $category->getSort());
        }

        Db::getInstance()->query($categoryTreeQuery);
    }

    /**
     * @return array|string[]
     */
    public static function getTermMetaData(): array
    {
        global $wpdb;

        if (\version_compare(\WC()->version, '2.6', '>=')) {
            return [ $wpdb->termmeta, 'term_id' ];
        } else {
            return [ "{$wpdb->prefix}woocommerce_termmeta", 'woocommerce_term_id' ];
        }
    }
}

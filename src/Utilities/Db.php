<?php

/**
 * @author    Jan Weskamp <jan.weskamp@jtl-software.com>
 * @copyright 2010-2013 JTL-Software GmbH
 */

namespace JtlWooCommerceConnector\Utilities;

use jtl\Connector\Core\Utilities\Singleton;
use JtlWooCommerceConnector\Logger\DatabaseLogger;

class Db extends Singleton
{
    /**
     * Run a plain SQL query on the database.
     *
     * @param string $query SQL query.
     * @param bool $shouldLog Query should be written to log files.
     *
     * @return array|null Database query results
     */
    public function query(string $query, bool $shouldLog = true): ?array
    {
        global $wpdb;

        if ($shouldLog) {
            DatabaseLogger::getInstance()->writeLog($query);
        }

        return $wpdb->get_results($query, \ARRAY_A);
    }

    /**
     * Run a SQL query which should only return one value.
     *
     * @param string $query SQL query.
     * @param bool $shouldLog Query should be written to log files.
     *
     * @return null|string Found value or null.
     */
    public function queryOne(string $query, bool $shouldLog = true): ?string
    {
        global $wpdb;

        if ($shouldLog) {
            DatabaseLogger::getInstance()->writeLog($query);
        }

        return $wpdb->get_var($query);
    }

    /**
     * Run a SQL query which should return a list of single values.
     *
     * @param string $query SQL query.
     * @param bool $shouldLog Query should be written to log files.
     *
     * @return array The array of values
     */
    public function queryList(string $query, bool $shouldLog = true): array
    {
        global $wpdb;

        $return = [];

        if ($shouldLog) {
            DatabaseLogger::getInstance()->writeLog($query);
        }

        $result = $wpdb->get_results($query, \ARRAY_N);

        if (!empty($result)) {
            foreach ($result as $row) {
                $return[] = $row[0];
            }
        } else {
            return [];
        }

        return $return;
    }

    /**
     * @param $table
     * @param $constraint
     * @return bool
     */
    public static function checkIfFKExists($table, $constraint): bool
    {
        $sql  = "
               SELECT COUNT(*)
                  FROM information_schema.TABLE_CONSTRAINTS
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = '{$table}'
                    AND CONSTRAINT_NAME = '{$constraint}';";
        $test = Db::getInstance()->queryOne($sql);

        return (bool)$test;
    }

    /**
     * @return Singleton
     */
    public static function getInstance(): Singleton
    {
        return parent::getInstance();
    }
}

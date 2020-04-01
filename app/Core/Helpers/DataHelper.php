<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 10/23/2018
 * Time: 10:52 AM
 */

namespace App\Core\Helpers;

use App\Core\Dao\SDB;

class DataHelper
{
    public static function getTableColumns($table)
    {
        return SDB::getSchemaBuilder()->getColumnListing($table);
    }

    public static function getCreateSchemaSqlite($tableName)
    {
        $tableDetail = SDB::table('INFORMATION_SCHEMA.COLUMNS')
            ->where('table_name', '=', $tableName)
            ->where('TABLE_SCHEMA', '=', env('DB_DATABASE'))
            ->selectRaw("column_name,REPLACE(column_type, 'unsigned', '') as column_type")
            ->get();
        $schema = 'CREATE TABLE IF NOT EXISTS ' . $tableName . ' (';
        foreach ($tableDetail as $columnDetail) {
            $columnArr[] = $columnDetail->column_name . ' ' . $columnDetail->column_type;
        }
        $schema = $schema . implode(',', $columnArr) . ')';
        return $schema;
    }

}

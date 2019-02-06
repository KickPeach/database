<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2019/2/3
 * Time: 17:26
 */

namespace KickPeach\DataBase;

use PDO;
use KickPeach\DataBase\Query\Builder;
use KickPeach\DataBase\Query\Gramars\MysqlGrammar;

class MysqlConnection extends Connection
{
    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1, $value,
                is_int($value) || is_float($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }

    public function table($table)
    {
        return (new Builder($this,new MysqlGrammar()))->table($table);
    }
}
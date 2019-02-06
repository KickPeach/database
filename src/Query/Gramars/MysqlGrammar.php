<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2019/2/3
 * Time: 17:15
 */

namespace KickPeach\DataBase\Query\Gramars;


class MysqlGrammar extends Grammar
{
    protected function wrap($value)
    {
        if ($value !=='*') {
            return "'$value'";
        }

        return $value;
    }
}
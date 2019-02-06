<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2019/2/3
 * Time: 17:48
 */

namespace KickPeach\DataBase;

use kickPeach\DataBase\Query\Builder;
use KickPeach\DataBase\Query\Gramars\PostpostgresqlGrammar;

class PostpostgresqlConnection extends Connection
{
    public function insertGetId($query,$bindings = [],$primaryKey = 'id')
    {
        return $this->select($query.' returning '.$primaryKey,$bindings)[0][$primaryKey];
    }

    public function table($table)
    {
        return (new Builder($this,new PostpostgresqlGrammar()))->table($table);
    }
}
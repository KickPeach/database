<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2019/2/3
 * Time: 16:27
 */

namespace KickPeach\DataBase\Query;

use KickPeach\DataBase\Connection;
use KickPeach\DataBase\Query\Gramars\Grammar;

class Builder
{
    protected $conn;

    protected $gramar;

    public $table;

    public function __construct(Connection $conn,Grammar $gramar)
    {
        $this->conn = $conn;
        $this->gramar = $gramar;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;

    }

    public function insert(array $values)
    {
        if (!is_array(reset($values))) {
            $values = [$values];
        }

        list($query,$bindings) = $this->gramar->compileInsert($this,$values);

        return $this->conn->insert($query,$bindings);
    }

    public function insertGetId(array $values,$primaryKey = 'id')
    {
        list($query,$bindings) = $this->gramar->compileInsertGetId($this,$values);

        $id = $this->conn->insertGetId($query,$bindings,$primaryKey);

        return is_numeric($id)? (int) $id : $id;
    }

}
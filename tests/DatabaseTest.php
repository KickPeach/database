<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2019/2/3
 * Time: 20:31
 */

namespace Tests\KickPeach\DataBase;

use PHPUnit\Framework\TestCase;
use KickPeach\DataBase\MysqlConnection;


class DatabaseTest extends TestCase
{
    /**
     * @var MySqlConnection
     */
    private $conn;

    public function setUp()
    {
        parent::setUp();

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', '192.168.10.10', 3306, 'geermall_psbildr', 'utf8mb4');
        $this->conn = new MysqlConnection($dsn, 'homestead', 'secret');
    }

    private function getLast()
    {
        return $this->conn->select('select * from users order by id desc limit 1');
    }

    private function insertGetId()
    {
        return $this->conn->table('users')->insertGetId([
            'name'      => 'seven',
            'email'    => 'shisiying@xhzyxed.cn',
            'password'      =>'$10$wGP.Jj.2CK2xaplIem31geUDdIHaCn1CKNuUvJ4IZdcZcxbJdJrrO',
            'phone'              =>'15626832124',
            'profile_color'              =>'#7c5cc4',
            'role_id'              =>1,
            'is_active'              =>1,
            'is_deleted'              =>0,
            'expire_date'              =>'2018-11-19',
            'language'              =>'zh-CN',
        ]);
    }

    public function testInsertGetId()
    {
        $lastRecord = $this->getLast();
        $lastId = empty($lastRecord) ? 0 : $lastRecord[0]['id'];

        $id = $this->insertGetId();
        $this->assertEquals($lastId + 1, (int) $id);
    }

    public function testInsert()
    {
        $affectNum = $this->conn->table('users')->insert([
            [
                'name'      => 'seven1',
                'email'    => 'shisiying@xhzyxed.cn',
                'password'      =>'$10$wGP.Jj.2CK2xaplIem31geUDdIHaCn1CKNuUvJ4IZdcZcxbJdJrrO',
                'phone'              =>'15626832124',
                'profile_color'              =>'#7c5cc4',
                'role_id'              =>1,
                'is_active'              =>1,
                'is_deleted'              =>0,
                'expire_date'              =>'2018-11-19',
                'language'              =>'zh-CN',
            ],
            [
                'name'      => 'seven2',
                'email'    => 'shisiying@xhzyxed.cn',
                'password'      =>'$10$wGP.Jj.2CK2xaplIem31geUDdIHaCn1CKNuUvJ4IZdcZcxbJdJrrO',
                'phone'              =>'15626832124',
                'profile_color'              =>'#7c5cc4',
                'role_id'              =>1,
                'is_active'              =>1,
                'is_deleted'              =>0,
                'expire_date'              =>'2018-11-19',
                'language'              =>'zh-CN',
            ],
        ]);

        $this->assertEquals(2, $affectNum);
    }

    public function testSelect()
    {
        $id = $this->insertGetId();

        $lastRecord = $this->getLast();

        $record = $this->conn->select('select * from users where id = :id', [
            ':id'   => $id
        ]);
        $this->assertEquals($lastRecord[0]['name'], $record[0]['name']);
    }

    public function testUpdate()
    {
        $id = $this->insertGetId();

        $affectNum = $this->conn->update('update users set name = :name  where id = :id', [
            ':name'     => 'sevenupdate',
            ':id'       => $id,
        ]);

        $this->assertEquals(1, $affectNum);
    }
    public function testDelete()
    {
        $id = $this->insertGetId();

        $affectNum = $this->conn->delete('delete from users where id = :id', [
            ':id'       => $id,
        ]);

        $this->assertEquals(1, $affectNum);
    }
}
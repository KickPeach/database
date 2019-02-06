<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2019/2/3
 * Time: 14:55
 */

namespace KickPeach\DataBase;

use PDO;
use Exception;
use Throwable;
use Closure;
use KickPeach\DataBase\Query\Gramars\Grammar;
/**
 * Class Connection
 * @package KickPeach\DataBase
 */

class Connection
{
    protected $pdo;

    protected $timeout = 3;

    protected $pretending = false;

    protected $queryLog = [];

    public function __construct($dsn,$user = null,$password = null,array $options = [])
    {
        $options = $this->getDefaultOptions() + $options +[PDO::ATTR_TIMEOUT=>$this->timeout];
        $this->pdo = new PDO($dsn,$user,$password,$options);
    }

    protected function getDefaultOptions()
    {
        return [
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES=>false,
        ];
    }

    public function pretend(Closure $callback)
    {
        $this->pretending = true;

        $callback($this);

        $this->pretending = false;

        return $this->queryLog;

    }

    public function getQueryLog()
    {
        return $this->queryLog;
    }

    public function transaction(Closure $callback)
    {
        $this->pdo->beginTransaction();

        try{
            $result = $callback($this);

            $this->pdo->commit();

        }
        catch (Exception $e){
            $this->pdo->rollBack();
            throw $e;
        }catch (Throwable $e){
            $this->pdo->rollBack();
            throw $e;
        }

        return $result;
    }

    protected function run($query,$bindings,Closure $callback)
    {
        $start = microtime(true);
        $result = $callback($query,$bindings);
        $time = round((microtime(true) - $start) * 1000, 2);
        $this->queryLog[] = compact('query','bindings','time');

        return $result;

    }

    public function select($query,$bindings=[])
    {
        return $this->run($query,$bindings,function ($query,$bindings){
            if ($this->pretending) {
                return [];
            }

            $stmt = $this->pdo->prepare($query);
            $this->bindValues($stmt,$bindings);

            $stmt->execute();
            return $stmt->fetchAll();
        });
    }

    public function bindValues($statement,$bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key)?$key:$key+1,$value,
                is_int($value)?PDO::PARAM_INT:PDO::PARAM_STR
            );
        }
    }

    protected function affectingStatement($query,$bindings = [])
    {
        return $this->run($query,$bindings,function ($query,$bindings) {
            if ($this->pretending) {
                return 0;
            }
            $stmt = $this->pdo->prepare($query);
            $this->bindValues($stmt,$bindings);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public function insertGetId($query,$bindings = [],$primaryKey = 'id')
    {
        $query = str_replace("'",'',$query);
        $this->insert($query,$bindings);

        return $this->pdo->lastInsertId($primaryKey);
    }

    public function insert($query,$bindings = [])
    {
        $query = str_replace("'",'',$query);
        return $this->affectingStatement($query,$bindings);

    }

    public function update($query,$bindings)
    {
        $query = str_replace("'",'',$query);
        return $this->affectingStatement($query,$bindings);
    }

    public function delete($query,$bindings)
    {
        return $this->affectingStatement($query,$bindings);
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function table($table)
    {
        return (new Builder($this, new Grammar()))->table($table);
    }

    public function info()
    {
        $output = [
            'server'=>'SERVER_INFO',
            'driver'=>'DRIVER_NAME',
            'client'=>'CLIENT_VERSION',
            'version'=>'SERVER_VERSION',
            'connection'=>'CONNECTION_STATUS'
        ];

        foreach ($output as $key=> $value){
            $output[$key] = $this->pdo->getAttribute(constant('PDO::ATTR_'.$value));
        }
        return $output;
    }
}
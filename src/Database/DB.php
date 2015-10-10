<?php

namespace Betteryourweb\Database;

use PDO;
use PDOException;
use Betteryourweb\Helpers\Utils;

/**
 * @property  user
 */
class DB implements \JsonSerializable
{
    private $dbname;
    private $handlerost;
    private $dbuser;
    private $dbpass;
    private $dsn;

    private $dbdriver;

    private $handler;
    private $statement;
    private $error;
    private $options;
    private $queryString = null;

    public $table;
    private $bindings;
    private $results;


    /**
     * Set DB variables
     * connect to database
     */
    public function __construct()
    {
        $this->dbdriver = 'mysql';
        $this->handlerost = DB_HOST;
        $this->dbuser = DB_USER;
        $this->dbname = DB_DATABASE;
        $this->dbpass = DB_PASS;

        $this->connect();

    }


    /**
     * Connect to the database
     */
    public function connect()
    {

        $this->setOptions([
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

        ]);
        try {
            $this->handler = new PDO($this->setDSN(), $this->dbuser, $this->dbpass, $this->getOptions());

        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
        dump([
            $this->setDSN(),
            $this->dbuser,
            $this->dbpass,
            $this->getOptions(),
            $this->handler,
            $this->error,
        ]);
    }

    /**
     * @param $user
     * @param $pass
     * @param $host
     * @param $database
     */
    public function init($handlerost, $database, $dbuser = null, $dbpass = null)
    {
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->handlerost = $handlerost;
        $this->database = $database;

        return $this;
    }

    public function from($table)
    {
        $this->table;
        return $this;
    }

    public function execute()
    {
        return $this->statement->execute();
    }

    public function resultset()
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single()
    {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    public function lastInsertId()
    {
        return $this->handler->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->handler->beginTransaction();
    }

    /**
     * @return mixed
     */
    public function cancelTransaction()
    {
        return $this->handler->rollBack();
    }

    public function endTransaction()
    {
        return $this->handler->commit();
    }

    /**
     * @return mixed
     */
    public function debugDumpParams()
    {
        return $this->statement->debugDumpParams();
    }

    /**
     * @param $column
     * @param $value
     * @param null $value2
     * @return $this
     */
    public function where($column, $value, $value2 = null)
    {

        $operand = "=";
        $pValue = "'$value'";

        //return dump([Utils::get_var_name($value),Utils::get_var_name($value2)]);

        if ($value2 !== null) {
            $operand = $value;
            $pValue = " '$value2'";
        }

        $this->setBindings(":$column", $pValue);

        $this->setQueryString(" WHERE $column $operand :$column ");

        return $this;

    }

    public function setBindings($param, $value, $type = null)
    {
        $this->bindings[$param] = [$value, $type];
        return $this;
    }

    public function query($query)
    {

        $this->queryString = $query;
        return $this;
    }


    public function buildQuery()
    {
        $this->statement = $this->handler->prepare($this->queryString);
        foreach ($this->bindings as $key => $value) {

            $this->bind($key, $value[0], $value[1]);
        }

        return $this;

    }

    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    public function get()
    {
        $this->buildQuery();
        $this->execute();
        $this->results = $this->resultset();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @param mixed $statement
     */
    public function setStatement($statement)
    {
        $this->statement = $statement;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param mixed $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return mixed
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * @param mixed $queryString
     */
    public function setQueryString($queryString)
    {
        $this->queryString .= " $queryString ";
    }

    /**
     * Set DSN to use to connect to the databse
     * @return string
     */
    public function setDSN()
    {
        $this->dsn = 'mysql:host=' . $this->handlerost . ';dbname=' . $this->dbname;
        return $this->dsn;
    }

    /**
     * Set PDO options
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options = [])
    {

        $this->options = $options;

        return $this;

    }

    /**
     * Get PDO options
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * List of useable database driviers to use
     */
    public function getDriverList()
    {
        $drivers = [
            'sqlite',
            'mysql',

        ];
    }

    public function jsonSerialize(){
        return $this->results;
    }
}
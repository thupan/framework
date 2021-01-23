<?php

namespace Database\Driver;

use \PDOException;
use \Service\Debug\Debug;

class Mysql implements \Database\Interfaces\PersistenceDatabase
{
    protected static $config = [];
    protected static $error = [];
    protected $connection = [];

    public function __construct($connection, $database, $host, $port, $username, $password)
    {
        $this->connect($connection, $database, $host, $port, $username, $password);
    }

    public static function getError()
    {
        return self::$error;
    }

    public function connect($connection, $database, $host, $port, $username, $password)
    {
        self::$config = autoload_config();

        // rename
        $current = $connection;

        try {
            $this->connection[$current] = new \PDO("mysql:host=$host;dbname=$database;charset=UTF8", $username, $password);
            $this->connection[$current]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            Debug::collectorPDO($this->connection[$current]);
        } catch (PDOException $e) {
            self::$error[] = $e->getMessage();
            Debug::getInstance('exceptions')->addException($e);
        }

        return $this;
    }

    public function query($sql, $type = null)
    {
        foreach ($this->connection as $connection);
        try {
            $sth = $connection->prepare($sql);
            $sth->execute();

            switch ($type) {
                case 'json':
                    return json_encode($sth->fetchAll(self::$config['database']['DB_FETCH']));
                break;

                default:
                    return $sth->fetchAll(self::$config['database']['DB_FETCH']);
            }
        } catch (PDOException $e) {
            self::$error[] = $e->getMessage();
            Debug::getInstance('exceptions')->addException($e);
        }
    }

    public function find($table, $where = null)
    {
        foreach ($this->connection as $connection);
        try {
            if (!empty($table)) {
                $table = rtrim($table);
            }
            if (!is_null($where)) {
                $where = rtrim("WHERE $where");
            }

            $sql = "SELECT * FROM $table $where";
            $sth = $connection->prepare($sql);
            $sth->execute();

            return $sth->fetchAll(self::$config['database']['DB_FETCH']);
        } catch (PDOException $e) {
            self::$error[] = $e->getMessage();
            Debug::getInstance('exceptions')->addException($e);
        }
    }

    public function insert($table, $data)
    {
        foreach ($this->connection as $connection);
        try {
            $fieldNames = implode(',', array_keys($data));

            foreach ($data as $key => $value) {
                $fieldValues .= ":$key,";
            }

            $fieldValues = rtrim($fieldValues, ',');

            $sql = "INSERT INTO $table ($fieldNames) VALUES ($fieldValues)";

            $sth = $connection->prepare($sql);

            foreach ($data as $key => $value) {
                $sth->bindValue(":$key", $value);
            }

            $execute = $sth->execute();

            return $connection->lastInsertId() ? $connection->lastInsertId() : $execute;
        } catch (PDOException $e) {
            self::$error[] = $e->getMessage();
            Debug::getInstance('exceptions')->addException($e);
        }
    }

    public function update($table, $data, $where)
    {
        foreach ($this->connection as $connection);
        try {
            ksort($data);

            $fieldDetails = null;

            foreach ($data as $key => $value) {
                $fieldDetails .= "$key=:$key,";
            }

            $fieldDetails = rtrim($fieldDetails, ',');

            $sql = "UPDATE $table SET $fieldDetails WHERE $where";

            $sth = $connection->prepare($sql);

            foreach ($data as $key => $value) {
                $sth->bindValue(":$key", $value);
            }

            return $sth->execute();
        } catch (PDOException $e) {
            self::$error[] = $e->getMessage();
            Debug::getInstance('exceptions')->addException($e);
        }
    }

    public function delete($table, $where)
    {
        foreach ($this->connection as $connection);
        try {
            $sql = "DELETE FROM $table WHERE $where";

            return $connection->exec($sql);
        } catch (PDOException $e) {
            self::$error[] = $e->getMessage();
            Debug::getInstance('exceptions')->addException($e);
        }

        if (self::$error) {
            return self::$error;
        } else {
            return false;
        }
    }

    public function execute($sql)
    {
        foreach ($this->connection as $connection);
        try {
            return $connection->exec($sql);
        } catch (PDOException $e) {
            self::$error[] = $e->getMessage();
            Debug::getInstance('exceptions')->addException($e);
        }
    }
}

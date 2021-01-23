<?php

namespace Database\Interfaces;

interface PersistenceDatabase
{
    public function connect($connection, $database, $host, $port, $username, $password);
    public function query($sql, $type);
    public function insert($table, $data);
    public function update($table, $data, $where);
    public function delete($table, $where);
    public function find($table, $where);
    public function execute($sql);
}

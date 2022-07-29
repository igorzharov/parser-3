<?php

namespace App\DB;

use Medoo\Medoo;
use PDO;

class DBParser
{

    public Medoo $db;

    public function __construct()
    {
        $pdo = new PDO('mysql:dbname=rent;host=db', 'dbuser', 'root');

        $this->db = new Medoo(['pdo' => $pdo, 'type' => 'mysql']);
    }

    public function insert(string $table, array $data)
    {
        $this->db->insert($table, $data);
    }

    public function update(string $table, array $data, array $where): ?\PDOStatement
    {
        return $this->db->update($table, $data, $where);
    }

    public function select(string $table, mixed $columns, array $where, callable $callback = null): array
    {
        return $this->db->select($table, $columns, $where, $callback);
    }

    public function clear(string $table, $parserClassName)
    {
        $this->db->delete($table, ['parser_class[=]' => $parserClassName]);

        $this->db->query('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1');
    }

}


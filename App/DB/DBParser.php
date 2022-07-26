<?php

namespace App\DB;

use Medoo\Medoo;
use PDO;

class DBParser {

    public $db;

    public function __construct()
    {
        $pdo = new PDO('mysql:dbname=rent;host=db', 'dbuser', 'root');

        $this->db = new Medoo(['pdo' => $pdo, 'type' => 'mysql']);
    }

    public function insert(string $table, array $data)
    {
        $this->db->insert($table, $data);
    }

    public function selectAll(string $table, string $parserClassName) : array
    {
        if ($parserClassName == '') {

            return $this->db->select($table, '*', ['status[=]' => 1]);
        }

        return $this->db->select($table, '*', ['parser_class[=]' => $parserClassName, 'status[=]' => 1]);
    }

    public function clear(string $table, $parserClassName)
    {
        $this->db->delete($table, ['parser_class[=]' => $parserClassName]);

//        $this->db->query('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1');
    }

    public function updateProduct(string $table, array $data, int $productId)
    {
        $this->db->update($table, $data, ['product_id[=]' => $productId]);
    }

}


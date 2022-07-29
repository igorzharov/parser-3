<?php

namespace App\DB;

use Medoo\Medoo;
use PDO;

class DBParser
{

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

    public function update(string $table, array $data, array $where) : ?\PDOStatement
    {
        return $this->db->update($table, $data, $where);
    }

    public function delete(string $table, array $where)
    {
        $this->db->update($table, $where);
    }

    public function selectAll(string $table, string $parserClassName = ''): array
    {
        if ($parserClassName == '') {
            return $this->db->select($table, '*', ['status[=]' => 1]);
        }

        return $this->db->select($table, '*', ['parser_class[=]' => $parserClassName, 'status[=]' => 1]);
    }

    public function select(string $table, mixed $columns, array $where, callable $callback = null): array
    {
        return $this->db->select($table, $columns, $where, $callback);
    }

    public function createTempTable(string $table)
    {
        $this->db->query("CREATE TABLE `" . $table . "` (`product_id` int NOT NULL,`category_id` int NOT NULL,`category_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,`product_url` text NOT NULL,`parser_class` varchar(255) NOT NULL,`status` tinyint(1) NOT NULL DEFAULT '1') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;");

        $this->db->query("ALTER TABLE `" . $table . "` ADD PRIMARY KEY (`product_id`);");

        $this->db->query("ALTER TABLE `" . $table . "` MODIFY `product_id` int NOT NULL AUTO_INCREMENT;");
    }

    public function clear(string $table, $parserClassName)
    {
        $this->db->delete($table, ['parser_class[=]' => $parserClassName]);

        $this->db->query('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1');
    }

    public function updateProduct(string $table, array $data, int $productId)
    {
        $this->db->update($table, $data, ['product_id[=]' => $productId]);
    }

}


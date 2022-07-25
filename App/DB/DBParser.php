<?php

namespace App\DB;

use Medoo\Medoo;
use PDO;

trait DBParser {

    private $db;

    public function __construct()
    {
        $pdo = new PDO('mysql:dbname=parser;host=127.0.0.1', 'root', 'root');

        $this->db = new Medoo(['pdo' => $pdo, 'type' => 'mysql']);
    }

    public function insert(string $table, array $data) {
        $this->db->insert($table, $data);
    }

    public function selectAll(string $table) : array {
        return $this->db->select($table, '*');
    }

}


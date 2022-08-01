<?php

declare(strict_types=1);

namespace App\DB;

use Medoo\Medoo;
use PDO;

class DBRemote extends DB
{
    public function __construct()
    {
        $pdo = new PDO('mysql:dbname=orbita74-sandbox;host=185.178.45.121;charset=utf8', 'user', 'dQ1GyRtYrfgrAELk');

        $this->db = new Medoo(['pdo' => $pdo, 'type' => 'mysql']);
    }
}


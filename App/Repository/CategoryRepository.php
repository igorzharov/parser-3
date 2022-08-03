<?php

declare(strict_types=1);

namespace App\Repository;

use App\DB\DBParser;
use PDO;

class CategoryRepository
{
    private DBParser $db;

    public function __construct()
    {
        $this->db = new DBParser();
    }

    public function getCategories(): array
    {
        return $this->db->select('categories', ['category_id', 'parent_id', 'title', 'description', 'url', 'image', 'parser_class', 'status']);
    }

    public function generateTree($categoryId, $data)
    {
        $table = 'categories_path';
        $selectColumns = ['category_id', 'path_id', 'level'];

        // MySQL Hierarchical Data Closure Table Pattern

        $rows = $this->db->select($table, $selectColumns, ['path_id[=]' => $categoryId, 'ORDER' => ['level' => 'ASC']]);

        if ($rows != [])
        {
            foreach ($rows as $categoryPath)
            {
                // Delete the path below the current one
                $this->db->delete($table, ['AND' => ['category_id[=]' => $categoryPath['category_id'], 'level[<]' => $categoryPath['level']]]);

                $path = [];

                // Get the nodes new parents

                $pathRows = $this->db->select($table, $selectColumns, ['category_id[=]' => $data['parent_id'], 'ORDER' => ['level' => 'ASC']]);

                foreach ($pathRows as $result)
                {
                    $path[] = $result['path_id'];
                }

                // Get whats left of the nodes current path

                $pathRows = $this->db->select($table, $selectColumns, ['category_id[=]' => $categoryPath['category_id'], 'ORDER' => ['level' => 'ASC']]);

                foreach ($pathRows as $result)
                {
                    $path[] = $result['path_id'];
                }

                // Combine the paths with a new level
                $level = 0;

                foreach ($path as $path_id)
                {
                    $this->db->query("REPLACE INTO `categories_path` SET category_id = '" . (int)$categoryPath['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . $level . "'");

                    $level++;
                }
            }
        } else
        {
            // Delete the path below the current one
            $this->db->delete($table, ['category_id[=]' => $categoryId]);

            // Fix for records with no paths
            $level = 0;

            $rows = $this->db->select($table, $selectColumns, ['category_id[=]' => $data['parent_id'], 'ORDER' => ['level' => 'ASC']]);

            foreach ($rows as $result)
            {
                $this->db->insert($table, ['category_id' => $categoryId, 'path_id' => $result['path_id'], 'level' => $level]);

                $level++;
            }

            $this->db->query("REPLACE INTO `categories_path` SET category_id = '" . (int)$categoryId . "', `path_id` = '" . (int)$categoryId . "', level = '" . $level . "'");
        }
    }

    public function getChildsCategory($categoryId)
    {
        return $this->db->select('categories_path', ['category_id', 'path_id', 'level'], ['path_id' => $categoryId, 'ORDER' => ['category_id' => 'ASC']]);
    }

    public function getCategoryChildIds($categoryId)
    {
        $query = $this->db->query("SELECT GROUP_CONCAT(path_id ORDER BY path_id SEPARATOR ',') as 'ids' FROM categories_path WHERE category_id = ". $categoryId ." GROUP BY category_id");

        return $query->fetchAll()[0][0];
    }


}
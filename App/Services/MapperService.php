<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\CategoryRepository;

class MapperService
{
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    public static function map($productCategoryId, $configCategories) : array
    {
        $categories = (new MapperService)->categoryRepository->getCategoryChildIds($productCategoryId);

        $categories = explode(',', $categories);

        $path = '';

        foreach ($categories as $category) {

            if (isset($configCategories[$category]))
            {
                $path = $path . ',' . $configCategories[$category];
            }
        }

        $path = trim($path, ',');

        $path = explode(',', $path);

        $path = array_unique($path);

        sort($path);

        $sortPath = [];

        foreach ($path as $key => $value)
        {
            $sortPath[$key] = $value;
        }

        return $sortPath;
    }
}
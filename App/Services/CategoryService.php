<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    public function generateTree()
    {
        $categories = $this->categoryRepository->getCategories();

        foreach ($categories as $category)
        {
            $this->categoryRepository->generateTree($category['category_id'], $category);
        }
    }

    public function getChildIds() : array
    {
        return $this->categoryRepository->getChildIds();
    }
}
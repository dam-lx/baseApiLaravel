<?php

namespace App\Api\V1\Http\Controllers;
use App\Api\V1\Services\Interfaces\CategoryServiceInterface;
use App\Core\Helpers\ResponseHelper;
class CategoryController extends Controller
{
    protected $service;
    
    public function __construct (CategoryServiceInterface $categoryService)
    {
        $this->service = $categoryService;
    }
    
    public function homeCategory(){
        return ResponseHelper::JsonDataResult($this->service->homeCategory());
    }
    
    public function allCategory(){
        return ResponseHelper::JsonDataResult($this->service->allCategory());
    }
}

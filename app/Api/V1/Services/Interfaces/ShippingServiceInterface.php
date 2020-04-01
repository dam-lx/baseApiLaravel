<?php

namespace App\Api\V1\Services\Interfaces;

interface ShippingServiceInterface
{
    public function detail($id);
    public function create($inputs);
    public function edit($id,$inputs);
    public function index();
    public function delete($id);
}

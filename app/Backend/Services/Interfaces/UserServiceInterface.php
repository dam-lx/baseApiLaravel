<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 6/14/2018
 * Time: 10:23 AM
 */
namespace App\Backend\Services\Interfaces;

interface UserServiceInterface
{
    public function getAll($request);
    public function delete($request);
    public function deleteAll($arrUser);
    public function getById($id);
    public function getRole();
    public function addUser($request);
    public function editUser($request);
    public function editProfile($request);

}

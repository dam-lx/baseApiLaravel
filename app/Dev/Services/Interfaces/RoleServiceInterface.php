<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 6/14/2018
 * Time: 10:23 AM
 */
namespace App\Dev\Services\Interfaces;
use App\Dev\Entities\DataResultCollection;
use App\Dev\Http\Requests\AddNewRoleRequest;
use App\Dev\Http\Requests\UpdateRoleRequest;

interface RoleServiceInterface
{
    public function addNewRole(AddNewRoleRequest $request):DataResultCollection;
    public function getRoleList():DataResultCollection;
    public function updateRole(UpdateRoleRequest $request):DataResultCollection;
    public function deleteRole($id):DataResultCollection;
}

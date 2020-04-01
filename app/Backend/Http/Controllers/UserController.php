<?php

namespace App\Backend\Http\Controllers;
use App\Backend\Http\Requests\AddUserRequest;
use App\Backend\Http\Requests\EditAccountRequest;
use App\Backend\Http\Requests\EditProfileRequest;
use App\Backend\Services\Interfaces\UserServiceInterface;
use App\Core\Common\Pagging;
use App\Core\Helpers\AuthHelper;
use App\Core\Helpers\CommonHelper;
use App\Core\Helpers\ResponseHelper;
use App\Core\Services\Interfaces\UploadServiceInterface;
use Illuminate\Http\Request;

class UserController
{
	protected $service;
    protected $uploadService;
    public function __construct(UserServiceInterface $userService,UploadServiceInterface $uploadService)
    {
        $this->service       = $userService;
        $this->uploadService = $uploadService;
    }
    public function getList(Request $request){
        $page    = ($request->page == null) ? 1 : (int)$request->page;
        $perPage = $request->input("perPage", Pagging::API_PER_PAGE);
        return view("backend.users.list", compact("page", "perPage"));
    }
    
    
    public function paginate(Request $request){
    	$result = $this->service->getAll($request);
    	return ResponseHelper::JsonDataResult($result);
    }
    
    public function profile(Request $request){
        $user         = AuthHelper::getUserInfor();
        $user         = $this->service->getById($user->id);
        return view("backend.users.profile", [
            "user" => $user
        ]);
    }
    
    public function editProfile(EditProfileRequest $request)
    {
        $result = $this->service->editProfile($request);
        return ResponseHelper::JsonDataResult($result);
    }
    
    public function add()
    {
        $arrRole = $this->service->getRole();
        return view("backend.users.add",["arrRole" => $arrRole]);
    }
    
    public function addPost(AddUserRequest $request)
    {
        $result = $this->service->addUser($request);
        return ResponseHelper::JsonDataResult($result);
    }
    
    public function getById(Request $request)
    {
        $user    = $this->service->getById($request->id);
        $arrRole = $this->service->getRole();
        return view("backend.users.edit", [
            "user" => $user,
            "arrRole" => $arrRole
        ]);
    }
    
    public function editPost(EditAccountRequest $request)
    {
        $result = $this->service->editUser($request);
        return ResponseHelper::JsonDataResult($result);
    }
    
    public function delete(Request $request){
        $result = $this->service->delete($request);
        return ResponseHelper::JsonDataResult($result);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 8/3/2018
 * Time: 10:44 AM
 */

namespace App\Backend\Services\Production;

use App\Backend\Http\Requests\Request;
use App\Backend\Services\Interfaces\UserServiceInterface;
use App\Core\Common\Pagging;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use App\Core\Helpers\AuthHelper;
use App\Core\Helpers\CommonHelper;
use App\Core\Services\Interfaces\UploadServiceInterface;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService extends BaseService implements UserServiceInterface
{
    protected $uploadService;
    function __construct(UploadServiceInterface $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    /**
     * @param $validateArray
     * @param $fileName
     * HELPER: Generation Config File contain text translated.
     */
    public function getAll($request)
    {
        $result  = new \App\Core\Entities\DataResultCollection();
        $perPage = $request->input("perPage", Pagging::API_PER_PAGE);
        try {
            $arrUser = SDB::table("users")
                         ->join("sys_roles","users.role_value","=","sys_roles.role_value")
                         ->leftJoin("users_detail as dt","users.id","=","dt.user_id")
                         ->orderby("users.id","desc")
                         ->select("users.*","sys_roles.name as role","dt.avatar","dt.gender","dt.birth_date")
                         ->paginate($perPage);
            foreach ($arrUser as $obj){
                $obj->src = CommonHelper::getImageSrc($obj->avatar);
            }
            $result->data = $arrUser;
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            \Log::debug($exception->getMessage());
        }
        return $result;
    }
    
    public function getRole()
    {
        $arrRole = DB::table("sys_roles")->get();
        return $arrRole;
    }
    
    public function getById($id)
    {
        $user = DB::table("users")
                    ->join("sys_roles","users.role_value","=","sys_roles.role_value")
                    ->leftJoin("users_detail","users.id","=","users_detail.user_id")
                    ->where("users.id",$id)
                    ->select("users.*","users_detail.gender","users_detail.birth_date","users_detail.avatar","sys_roles.name as RoleName")
                    ->get();
        return $user[0];
    }
    
    public function addUser($request)
    {
        $image         = $request->file("image");
        $result        = new \App\Core\Entities\DataResultCollection();
        $diskLocalName = CommonHelper::getDefaultStorageDiskName();
        //check file image, if have process and save image
        if($image!=NULL){
            $arrImage = $this->uploadService->uploadFile(array($image),$diskLocalName,'uploads/avatars','');
            
            $arrImage =$arrImage->data[0];
        }
        SDB::beginTransaction();
        try {
            $image              = ($image != NULL) ? $arrImage["uri"]: null;
            $user["email"]      = $request->email;
            $user["password"]   = Hash::make($request->password);
            $user["role_value"] = $request->role;
            $user["name"]       = $request->name;
            $user["is_active"]  = UserConst::active;
            //insert user info to db
            $userDetail["user_id"]    = SDB::table("users")->insertGetId($user);
            $userDetail["gender"]     = $request->gender;
            $userDetail["birth_date"] = $request->date;
            $userDetail["avatar"]     = $image;
            //insert table user detail
            SDB::table("users_detail")->insert($userDetail);
            $result->status  = SDBStatusCode::OK;
            $result->message = trans("backend.add_account_success");
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            \Log::debug($exception->getMessage());
        }
        SDB::commit();
        return $result;
    }
    
    public function editUser($request)
    {
        $image         = $request->file("image");
        $diskLocalName = CommonHelper::getDefaultStorageDiskName();
        $result        = new \App\Core\Entities\DataResultCollection();
        if ($request->role == "profile") {
            $user = AuthHelper::getUserInfor();
            $role = $user->role_value;
            $id   = $user->id;
        } else {
            $role = $request->role;
            $id   = $request->id;
        }
        //check file image, if have process and save image
        if($image!=NULL){
            Storage::disk($diskLocalName)->delete($request->oldImgSrc);
            $arrImage = $this->uploadService->uploadFile(array($image),$diskLocalName,'uploads/avatars','');
            $arrImage =$arrImage->data[0];
        }
        SDB::beginTransaction();
        try {
            $image = ($image != NULL) ? $arrImage["uri"]: null;
            //update table user
            SDB::table("users")
               ->where("id", $id)
               ->when($request->password != null, function ($query) use ($request, $role) {
                   return $query->update([
                                             "name"       => $request->name,
                                             "email"      => $request->email,
                                             "is_active"  => $request->is_active,
                                             "role_value" => $role,
                                             "password"   => Hash::make($request->password),
                                         ]);
               }, function ($query) use ($request, $role) {
                   return $query->update([
                                             "name"       => $request->name,
                                             "email"      => $request->email,
                                             "is_active"  => $request->is_active,
                                             "role_value" => $role
                                         ]);
               });
            //update table user detail
            SDB::table("users_detail")
               ->where("user_id", $id)
               ->when($image != null, function ($query) use ($request, $image) {
                   return $query->update([
                                             "gender"     => $request->gender,
                                             "birth_date" => $request->date,
                                             "avatar"     => $image
                                         ]);
               }, function ($query) use ($request) {
                   return $query->update([
                                             "gender"     => $request->gender,
                                             "birth_date" => $request->date,
                                         ]);
               });
            $result->status  = SDBStatusCode::OK;
            $result->message = trans("backend.edit_account_success");
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            \Log::debug($exception->getMessage());
        }
        SDB::commit();
        return $result;
    }
    
    public function editProfile($request)
    {
        $image         = $request->file("image");
        $diskLocalName = CommonHelper::getDefaultStorageDiskName();
        $result        = new \App\Core\Entities\DataResultCollection();
        $user          = Auth::user();
        $id            = $user->id;
        //check file image, if have process and save image
        if($image!=NULL){
            Storage::disk($diskLocalName)->delete($request->oldImgSrc);
            $arrImage = $this->uploadService->uploadFile(array($image),$diskLocalName,'uploads/avatars','');
            $arrImage =$arrImage->data[0];
        }
        SDB::beginTransaction();
        try {
            $image = ($image != NULL) ? $arrImage["uri"]: null;
            //update table user
            SDB::table("users")
               ->where("id", $id)
               ->when($request->password != null, function ($query) use ($request) {
                   return $query->update([
                                             "name"       => $request->name,
                                             "email"      => $request->email,
                                             "password"   => Hash::make($request->password),
                                         ]);
               }, function ($query) use ($request) {
                   return $query->update([
                                             "name"       => $request->name,
                                             "email"      => $request->email
                                         ]);
               });
            //update table user detail
            SDB::table("users_detail")
               ->where("user_id", $id)
               ->when($image != null, function ($query) use ($request, $image) {
                   return $query->update([
                                             "gender"     => $request->gender,
                                             "birth_date" => $request->date,
                                             "avatar"     => $image
                                         ]);
               }, function ($query) use ($request) {
                   return $query->update([
                                             "gender"     => $request->gender,
                                             "birth_date" => $request->date,
                                         ]);
               });
            $result->status  = SDBStatusCode::OK;
            $result->message = trans("backend.edit_account_success");
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            \Log::debug($exception->getMessage());
        }
        SDB::commit();
        return $result;
    }
    
    public function delete($request)
    {
        $diskLocalName = CommonHelper::getDefaultStorageDiskName();
        $result        = new \App\Core\Entities\DataResultCollection();
        try {
            SDB::beginTransaction();
            Storage::disk($diskLocalName)->delete($request->arrOldImage);
            SDB::table("users")
                ->whereIn("id", $request->arrId)
                ->delete();
            SDB::table("users_detail")
               ->whereIn("user_id", $request->arrId)
               ->delete();
            SDB::commit();
            $result->status = SDBStatusCode::OK;
            $result->message = trans('backend.delete_account_success');
        } catch (\Exception $e) {
            SDB::rollBack();
            $result->status  = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
            Log::debug($e->getMessage());
        }
        return $result;
    }
    public function deleteAll($arrUser)
    {
        $diskLocalName = "public";
        foreach($arrUser as $id) {
            $oldImgSrc = DB::table("users_detail")->where("user_id",$id)->select("avatar")->get();
            Storage::disk($diskLocalName)->delete($oldImgSrc[0]->avatar);
            DB::table("users")->where("id",$id)->delete();
            DB::table("users_detail")->where("user_id",$id)->delete();
        }
    }
}

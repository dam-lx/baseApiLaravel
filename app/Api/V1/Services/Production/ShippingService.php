<?php

namespace App\Api\V1\Services\Production;

use App\Api\V1\Services\Interfaces\ShippingServiceInterface;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\SysConst;
use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use App\Core\Helpers\CommonHelper;
use App\Core\Helpers\ShoppingHelper;
use Illuminate\Support\Facades\Auth;

class ShippingService implements ShippingServiceInterface
{
    public function detail($id)
    {
        $result = new DataResultCollection();
        $data   = SDB::table('dtb_customer_address')
                        ->where('customer_id', Auth::id())
                        ->where('customer_address_id', $id)
                        ->where('del_flg', SysConst::NOT_DEL_FLG)
                        ->first();
        if ($data) {
            $result->status = SDBStatusCode::OK;
            $result->data   = $data;
        } else {
            $result->status  = SDBStatusCode::Excep;
            $result->message = trans('api.address_not_exists');
        }
        return $result;
    }

    public function create($request)
    {
        $result = new DataResultCollection();
        try {
            SDB::beginTransaction();
            $data = array(
                'customer_id'  => Auth::id(),
                'country_id'   => $request->input('country_id', null),
                'pref'         => $request->input('pref', null),
                'name01'       => $request->input('name01', null),
                'name02'       => $request->input('name02', null),
                'kana01'       => $request->input('kana01', null),
                'kana02'       => $request->input('kana02', null),
                'company_name' => $request->input('company_name', null),
                'zip01'        => $request->input('zip01', null),
                'zip02'        => $request->input('zip02', null),
                'zipcode'      => $request->input('zip01', null) . $request->input('zip02', null),
                'addr01'       => $request->input('addr01', null),
                'addr02'       => $request->input('addr02', null),
                'tel01'        => $request->input('tel01', null),
                'tel02'        => $request->input('tel02', null),
                'tel03'        => $request->input('tel03', null),
                'fax01'        => $request->input('fax01', null),
                'fax02'        => $request->input('fax02', null),
                'fax03'        => $request->input('fax03', null),
                'del_flg'      => SysConst::NOT_DEL_FLG,
                'create_date'  => CommonHelper::dateNow(),
                'update_date'  => CommonHelper::dateNow(),
            );
            SDB::table('dtb_customer_address')->insert($data);
            SDB::commit();
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            SDB::rollBack();
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }

    public function edit($id, $request)
    {
        $result = new DataResultCollection();
        try {
            SDB::beginTransaction();
            $data = array(
                'country_id'   => $request->input('country_id', null),
                'pref'         => $request->input('pref', null),
                'name01'       => $request->input('name01', null),
                'name02'       => $request->input('name02', null),
                'kana01'       => $request->input('kana01', null),
                'kana02'       => $request->input('kana02', null),
                'company_name' => $request->input('company_name', null),
                'zip01'        => $request->input('zip01', null),
                'zip02'        => $request->input('zip02', null),
                'zipcode'      => $request->input('zip01', null) . $request->input('zip02'),
                'addr01'       => $request->input('addr01', null),
                'addr02'       => $request->input('addr02', null),
                'tel01'        => $request->input('tel01', null),
                'tel02'        => $request->input('tel02', null),
                'tel03'        => $request->input('tel03', null),
                'fax01'        => $request->input('fax01', null),
                'fax02'        => $request->input('fax02', null),
                'fax03'        => $request->input('fax03', null),
                'del_flg'      => SysConst::NOT_DEL_FLG,
                'create_date'  => CommonHelper::dateNow(),
                'update_date'  => CommonHelper::dateNow(),
            );
            SDB::table('dtb_customer_address')
                ->where('customer_id', Auth::id())
                ->where('customer_address_id', $id)
                ->update($data);
            SDB::commit();
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            SDB::rollBack();
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }

    public function index()
    {
        $result = new DataResultCollection();
        $data   = SDB::table('dtb_customer_address as ca')
                        ->leftJoin('mtb_pref', 'ca.pref', '=', 'mtb_pref.id')
                        ->select('ca.customer_address_id', 'ca.zip01', 'ca.zip02', 'ca.tel01',
                            'ca.tel02', 'ca.tel03', 'ca.name01', 'ca.name02', 'ca.addr01', 'ca.addr02', 'mtb_pref.name as pref_name')
                        ->where('customer_id', Auth::id())
                        ->where('del_flg', SysConst::NOT_DEL_FLG)
                        ->orderBy('create_date', 'DESC')
                        ->get();
        foreach ($data as $key => $entry){
            $data[$key] = ShoppingHelper::formatAddress($entry);
        }
        $result->status = SDBStatusCode::OK;
        $result->data = $data;
        return $result;
    }

    public function delete($id)
    {
        $result = new DataResultCollection();
        $delete = SDB::table('dtb_customer_address')
                        ->where('customer_id', Auth::id())
                        ->where('customer_address_id', $id)
                        ->update([
                            'del_flg' => SysConst::DEL_FLG
                        ]);
        if($delete == true){
            $result->status = SDBStatusCode::OK;
        }
        else{
            $result->status  = SDBStatusCode::Excep;
            $result->message = trans('api.address_not_exists');
        }
        return $result;
    }

}

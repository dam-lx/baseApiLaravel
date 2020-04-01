<?php


namespace App\Core\Helpers;


use App\Core\Common\SysConst;
use App\Core\Common\TaxRule;
use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use Illuminate\Support\Facades\Auth;

class TaxHelper
{
    public static function getTaxRule($productClassId = null)
    {
        $tax = [];
        if($productClassId!=null){
            $tax = SDB::table('dtb_tax_rule')
                      ->where('del_flg', SysConst::NOT_DEL_FLG)
                      ->where('apply_date', '<', CommonHelper::dateNow())
                      ->Where('product_class_id', $productClassId)
                      ->first();
        }
        if (empty($tax)) {

            $tax = SDB::table('dtb_tax_rule')
                ->where('del_flg', SysConst::NOT_DEL_FLG)
                ->where('apply_date', '<', CommonHelper::dateNow())
                ->whereNull('product_id')
                ->whereNull('country_id')
                ->whereNull('product_class_id')
                ->orderBy('create_date','DESC')
                ->first();
        }
        return $tax;
    }

    public static function calcTax($price, $taxRate, $calcRule, $taxAdjust = 0)
    {
        $tax = $price * $taxRate / 100;
        $roundTax = self::roundByCalcRule($tax, $calcRule);
        return $roundTax + $taxAdjust;
    }

    public static function roundByCalcRule($value, $calcRule)
    {
        switch ($calcRule) {
            case TaxRule::ROUND:
                $ret = round($value);
                break;
            case TaxRule::FLOOR:
                $ret = floor($value);
                break;
            default:
                $ret = ceil($value);
                break;
        }
        return $ret;
    }

    public static function getTax($price, $productClassId = null)
    {
        $taxRule = self::getTaxRule( $productClassId);
        return self::calcTax($price, $taxRule->tax_rate, $taxRule->calc_rule, $taxRule->tax_adjust);
    }

    public static function getPriceIncTax($price, $productClassId = null)
    {
        return $price + self::getTax($price, $productClassId);
    }
}
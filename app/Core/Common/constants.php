<?php
    
    /**
     * Created by PhpStorm.
     * User: MSI
     * Date: 6/21/2018
     * Time: 10:26 AM
     */
    
    namespace App\Core\Common;
    class SDBStatusCode
    {
        const OK             = 'OK';
        const WARNING        = 'WARNING';
        const DataNull       = 'DataNull';
        const Excep          = 'Excep';
        const ApiError       = 'ApiError';
        const WebError       = 'WebError';
        const ACLNotPass     = 'ACLNotPass';
        const ApiAuthNotPass = 'ApiAuthNotPass';
        const PDOExceoption  = 'PDOExceoption';
        const ValidateError  = 'ValidateError';
    }
    
    
    class ApiConst
    {
        const ApiAccessTokenParamName    = 'access_token';
        const ApiRefreshTokenParamName   = 'refresh_token';
        const UserInforResponseKeyName   = 'user_infor';
        const UserSettingResponseKeyName = 'user_setting';
        const ApiModuleName              = 'api';
    }
    
    
    class CoreConst
    {
        const CoreModuleName = 'Core';
    }
    
    class RoleConst
    {
        const PartyRole    = 10;
        const PublicRole   = 0;
        const SysAdminRole = 1;
        const NormalUser   = 2;
        const Manager      = 3;
    }
    
    class DateConst
    {
        const DATETIME_FORMAT_SQL = '%Y-%m-%d %H:%i:%s';
    }
    
    class Pagging
    {
        const ARR_PAGE
                           = array(
                10,
                30,
                50,
                80,
                100
            );
        const PER_PAGE     = 10;
        const API_PER_PAGE = 15;
    }
    
    class UploadConst
    {
        /*
         * Type image allow access
         */
        const FILE_IMAGE_UPLOAD_ACCESSED   = 'png,jpg,jpeg,bmp,gif';
        const UPLOAD_IMAGE_MAX             = '5120'; // 5MB
        const BACKEND_UPLOAD_IMAGE_PDF_MAX = '32768'; // 32MB
        const BACKEND_UPLOAD_IMAGE_MAX     = '5120'; // 5MB
        const UPLOAD_VIDEO_MAX             = '1048576'; // 1GB
        const BACKEND_UPLOAD_VIDEO_MAX     = '512000'; // 500MB
    }
    
    class CategoryConst
    {
        const DEL_FLG     = 1;
        const NEW         = "NEW";
        const PICKUP      = "PICKUP";
        const HIGH_SCORE  = "HIGH";
        const PROMOTION   = "PROMOTION";
        const BIG_BOTTLE  = "BIG";
        const NOT_DELETE  = 0;
        const TYPE_HTML   = 2;
        const TYPE_NORMAL = 1;
    }
    
    class SysConst
    {
        const DEL_FLG     = 1;
        const NOT_DEL_FLG = 0;
    }
    
    class LoggingConst
    {
        const SQL_LOG_channel = 'sql_query';
    }
    
    class UserConst
    {
        const limit                 = 10;
        const male                  = 1;
        const female                = 0;
        const active                = 2;
        const notActive             = 1;
        const DELETED               = 1;
        const changePass            = 1;
        const en                    = 'en';
        const jp                    = 'jp';
        const ENABLED               = 0;
        const DISABLED              = 1;
        const TEMPORARY_MEMBER      = 1;
        const MAIN_MEMBER           = 2;
        const SECRET_TYPE_PLAIN     = 'PLAIN';
        const CUSTOMER_RANK_GENERAL = 1;
        const CUSTOMER_NO_RANK      = 0;
        const VALIDATE_KANA         = '/^([\x{30A0}-\x{30FF}]+)$/u';
        const VALIDATE_INTERGER     = '/^[0-9]\d*$/';
        const VALIDATE_PASSWORD     = '/^[\x00-\x7F]*$/';
        const VALIDATE_SPACE        = '/^\S*$/u';
        const VALIDATE_EMAIL        = '/^([a-zA-Z0-9])([a-zA-Z0-9_\-]*)(\.[a-z0-9_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix';
        const PASSWORD_DEFAULT      = '**********';
    }
    
    class City
    {
        const list
            = [
                null,
                "北海道",
                "青森県",
                "岩手県",
                "宮城県",
                "秋田県",
                "山形県",
                "福島県",
                "茨城県",
                "栃木県",
                "群馬県",
                "埼玉県",
                "千葉県",
                "東京都",
                "神奈川県",
                "新潟県",
                "富山県",
                "石川県",
                "福井県",
                "山梨県",
                "長野県",
                "岐阜県",
                "静岡県",
                "愛知県",
                "三重県",
                "滋賀県",
                "京都府",
                "大阪府",
                "兵庫県",
                "奈良県",
                "和歌山県",
                "鳥取県",
                "島根県",
                "岡山県",
                "広島県",
                "山口県",
                "徳島県",
                "香川県",
                "愛媛県",
                "高知県",
                "福岡県",
                "佐賀県",
                "長崎県",
                "熊本県",
                "大分県",
                "宮崎県",
                "鹿児島県",
                "沖縄県"
            ];
    }
    
    class TaxRule
    {
        const ROUND = 1;
        const FLOOR = 2;
        const CEIL  = 3;
    }
    
    Class ProductConst
    {
        const STATUS_PUBLIC        = 1;
        const PUBLISH              = 1;
        const PRIVATE              = 2;
        const DELETED              = 1;
        const NOT_DELETE           = 0;
        const PRICE_LOWER          = 1;
        const PRICE_HIGHER         = 3;
        const PRODUCT_NEWER        = 2;
        const STOCK_UNLIMITED      = 1;
        const STATUS_ORDER_DEFAULT = 8;
    }
    
    class CalenderConst
    {
        const CALENDER_NAME    = 'お届け希望日';
        const DISPLAY_CALENDER = 1;
    }
    
    class FireBaseConst
    {
        const LINK           = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyCOmV7u4JxDnkGG9B3Tv5wyBRFoGtduuFo';
        const BUNDLE_ANDROID = 'com.wine_mobile';
        const BUNDLE_IOS     = 'org.reactjs.native.example.Wine-Mobile';
    }
    
    class PaymentConst
    {
        const  arrPayKbnKaisu
                          = [
                "01" => "一括払い",
                "02" => "分割払い(2回)",
                "03" => "分割払い(3回)",
                "04" => "分割払い(4回)",
                "05" => "分割払い(5回)",
                "06" => "分割払い(6回)",
                "07" => "分割払い(7回)",
                "08" => "分割払い(8回)",
                "09" => "分割払い(9回)",
                "10" => "分割払い(10回)",
                "11" => "分割払い(11回)",
                "12" => "分割払い(12回)",
                "15" => "分割払い(15回)",
                "16" => "分割払い(16回)",
                "18" => "分割払い(18回)",
                "20" => "分割払い(20回)",
                "24" => "分割払い(24回)",
                "30" => "分割払い(30回)",
                "36" => "分割払い(36回)",
                "48" => "分割払い(48回)",
                "54" => "分割払い(54回)",
                "60" => "分割払い(60回)",
                "72" => "分割払い(72回)",
                "88" => "リボルビング払い",
                "80" => "ボーナス一括払い",
            ];
        const  numbYear   = 10;
        const  monthStart = 1;
        const  monthEnd   = 12;
    }
    
    class OrderConst
    {
        const NEW_ORDER = 1;
    }



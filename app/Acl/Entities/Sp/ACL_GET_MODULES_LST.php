<?php 
//This is dev automatic generate 
 namespace App\Acl\Entities; 
use App\Core\Entities\Entity; 
class ACL_GET_MODULES_LST extends Entity{
	public $id;
	public $module_code;
	public $module_name;
	public $order_value;
	public $is_skip_acl;
	public  function __construct($object){
		 parent::__construct($object);
	}
} 

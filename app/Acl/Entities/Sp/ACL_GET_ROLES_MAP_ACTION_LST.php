<?php 
//This is dev automatic generate 
 namespace App\Acl\Entities; 
use App\Core\Entities\Entity; 
class ACL_GET_ROLES_MAP_ACTION_LST extends Entity{
	public $role_id;
	public $role_name;
	public $role_value;
	public $role_description;
	public  function __construct($object){
		 parent::__construct($object);
	}
} 

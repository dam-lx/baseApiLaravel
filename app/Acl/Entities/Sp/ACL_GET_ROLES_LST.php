<?php 
//This is dev automatic generate 
 namespace App\Acl\Entities; 
use App\Core\Entities\Entity; 
class ACL_GET_ROLES_LST extends Entity{
	public $id;
	public $name;
	public $role_value;
	public $description;
	public  function __construct($object){
		 parent::__construct($object);
	}
} 

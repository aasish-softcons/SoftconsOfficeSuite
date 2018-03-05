<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataDAO
 *
 * @author Shwetha
 */
require_once 'AbstractDAO.php';

define('USER_CREATED_SUCCESSFULLY', 0);


class DataDAO extends AbstractDAO {
    //put your code here
	
	/******************* Password Encryption ****************/
    function simple_encrypt($text,$salt)
    { 
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
 
    function simple_decrypt($text,$salt)
    {  
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }	
    
    
    //***************************************************    Rest API     **********************************************************************************
    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email_id, $password, $status) {
    	
    	
    	// fetching user by email
    	$query="select * from uni_user_master where email_id=:email_id and password=:password and status=:status";
    	$rslt = self::fetchQuery($query,array("email_id"=>$email_id, "password"=>$password, "status"=>$status));
    	//echo(sizeof($rslt));
    	if (sizeof($rslt) != 0) {
    		return "success";
    	} else {
    		// echo 'fail login';
    		return $rslt;
    	}
    }
    
    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByUserId($email_id) {
    	$query = "SELECT * FROM uni_user_master WHERE email_id = :email_id";
    	
    	
    	$rslt = self::fetchQuery($query,array("email_id"=>$email_id));
    	// $stmt->bind_param("s", $email_id);
    	if (sizeof($rslt )) {
    		// $user = $stmt->get_result()->fetch_assoc();
    		return $rslt;
    	} else {
    		return NULL;
    	}
    }
    //**************************************************Company************************************************************************/
    /**
	 * Fetching All Company List
	 * url-/getAllCompanyList
	 * method - GET All
	 * params 
	 */
    public function getAllCompanyList() {
    	$query = "SELECT * FROM uni_company_master";
        $rslt = self::fetchQuery($query,array());
    	if (sizeof($rslt)) 
    	{
    	  return $rslt;
    	} 
    	else 
    	{
    	 return NULL;
    	}
    } 
    
    /**
     * Fetching Company List based on the id
     * url-/getCompanyListById
     * method - GET by Id
     * params - id
     */
    public function getCompanyListById($id) {
    	$query = "SELECT * FROM uni_company_master where id =:id";
    	$rslt = self::fetchQuery($query,array("id"=>$id));
    	if (sizeof($rslt))
    	{
    		return $rslt;
    	}
    	else
    	{
    		return NULL;
    	}
    } 
    
    
    /**
     * Fetching Company Details
     * url-/companyExists
     * method - GET
     * params
     */
    public function companyExists($company_name) {
    	$query = "SELECT * FROM uni_company_master WHERE company_name=:company_name";
    	$rslt = self::fetchQuery($query,array("company_name"=>$company_name));
    	if (sizeof($rslt))
    	{
    		return $rslt;
    	}
    	else
    	{
    		return NULL;
    	}
    } 
    
    /**
     * Company Creation
     * url - /UpdateCompany
     * method - POST
     * params - id,company_name,contact_person, address,phone_no,email_id,subscription_date,expiry_date,asset_limit,user_limit
     * params - licence_version,licence_num,num_of_licence,	version,amount_paid,status,date_created,last_updated,nominal_flag
     * params - created_by,updated_by,sprint_days
     */ 
    
    public function updateCompany($company_name,$contact_person,$address,$phone_no,$email_id,$asset_limit,$subscription_date,
    		$expiry_date,$asset_limit,$user_limit,$licence_num,$num_of_licence,$licence_version,$version,$amount_paid,
    		$status,$date_created,$last_updated,$nominal_flag,$created_by,$updated_by,$sprint_days)
    {
    	
    	$response = array();
    	try {
    		$query="UPDATE company SET period_name = '$period_name', from_date = '$from_date',  to_date = '$to_date', modified_by = '$updated_by' WHERE period_id = '$period_id'";
    		$bind_array=array("period_id"=>$period_id,"period_name"=>$period_name,"from_date"=>$from_date,"to_date"=>$to_date,"updated_by"=>$updated_by);
    		$rslt=self::updateQuery($query,$bind_array);
    		if ($rslt) {
    			// asset type config successfully inserted
    			
    			//$userID = mysql_insert_id();
    			
    			//return $userID;
    			return $rslt;
    		} else {
    			// Failed to create user
    			return ASSET_CREATE_FAILED;
    		}
    	}
    	catch (PDOException $pde) {
    		throw $pde;
    	}
    }
    
    //***************************************************************Department***********************************************************************************************//
    /**
     * Adding Department
     * params-company_id,department_name,status,date_created,created_by
     **/
    
    public function addDepartment($company_id,$department_name,$status,$date_created,$created_by)
    {
    	$response = array();
    	
    	try
    	{
    		$query="INSERT INTO uni_department(company_id,department_name,status,date_created,created_by)VALUES(:company_id,:department_name,:status,:date_created,:created_by)";
    		
    		$bind_array=array("company_id"=>$company_id,"department_name"=>$department_name,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by);
    		
    		$rslt=self::insertQuery($query,$bind_array);
    		if ($rslt)
    		{
    			return $rslt;
    		}
    		else
    		{
    			return $rslt;
    		}
    	}
    	catch (PDOException $pde)
    	{
    		throw $pde;
    	} 	
    }
    /**
     * Updating Department
     * url - /updateDepartment
     * method - PUT
     * params -id
     */
    public static function updateDepartment($id,$company_id,$department_name,$last_updated,$updated_by)
    {
    	try{
    		$query="UPDATE  uni_department SET company_id=:company_id,department_name=:department_name,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
    		
    		$rslt= self::updateQuery($query,array("id"=>$id,"company_id"=>$company_id,"department_name"=>$department_name,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
    		if ($rslt) {
    			
    			return $rslt;
    		} else {
    			
    			return $rslt;
    		}
    	}
    	catch (PDOException $pde) {
    		throw $pde;
    	}
    }
    
    /**
     * Updating Department by Making Status Inactive
     * url - /deleteDepartment
     * method - PUT
     * params -id
     */
    public static function deleteDepartment($id)
    {
    	try{
    		$query="UPDATE  uni_department SET status=0 WHERE id=:id";
    		$rslt= self::updateQuery($query,array("id"=>$id));
    		if ($rslt) {
    			
    			return $rslt;
    		} else {
    			
    			return $rslt;
    		}
    	}
    	catch (PDOException $pde) {
    		throw $pde;
    	}
    }
    /**
     * Fetching All Department List
     * url-/getAllDepartmentList
     * method - GET All
     * params
     */
    public  function getAllDepartmentList() {
    	$query = "SELECT * FROM uni_department";
    	$rslt = self::fetchQuery($query,array());
    	if (sizeof($rslt))
    	{
    		return $rslt;
    	}
    	else
    	{
    		return NULL;
    	}
    }
    
    /**
     * Fetching Department List based on the id
     * url-/getDepartmentListById
     * method - GET by Id
     * params - id
     */
    public function getDepartmentListById($id) {
    	$query = "SELECT * FROM uni_department where id =:id";
    	$rslt = self::fetchQuery($query,array("id"=>$id));
    	if (sizeof($rslt))
    	{
    		return $rslt;
    	}
    	else
    	{
    		return NULL;
    	}
    } 
    
    
    //***************************************************************Users****************************************************************************************************//
    /**
     * Adding new User
     * params-first_name,last_name,company_id,email_id,password,user_address,phone_no,role_id,status,date_created,created_by
     **/
    public function addUser($first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$status,$date_created,$created_by)
    {
    	$response = array();
    	
    	try
    	{
    		$query="INSERT INTO uni_user_master(first_name,last_name,company_id,email_id,password,user_address,phone_no,role_id,status,date_created,created_by)
            VALUES(:first_name,:last_name,:company_id,:email_id,:password,:user_address,:phone_no,:role_id,:status,:date_created,:created_by)";
    		
    		$bind_array=array("first_name"=>$first_name,"last_name"=>$last_name,"company_id"=>$company_id,"email_id"=>$email_id,"password"=>$password,
    				"user_address"=>$user_address,"phone_no"=>$phone_no,"role_id"=>$role_id,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by);
    		
    		$rslt=self::insertQuery($query,$bind_array);
    		if ($rslt)
    		{
    			return $rslt;
    		}
    		else
    		{
    			return $rslt;
    		}
    	}
    	catch (PDOException $pde)
    	{
    		throw $pde;
    	}
    }
    
    /**
     * Updating User
     * url - /updateUser
     * method - PUT
     * params -id
     */
    public static function updateUser($id,$first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$last_updated,$updated_by)
    {
    	try{
    		$query="UPDATE  uni_user_master SET first_name=:first_name,last_name=:last_name,company_id=:company_id,email_id=:email_id,password=:password,
                    user_address=:user_address,phone_no=:phone_no,role_id=:role_id,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
    		
    		$rslt= self::updateQuery($query,array("id"=>$id,"first_name"=>$first_name,"last_name"=>$last_name,"company_id"=>$company_id,"email_id"=>$email_id,"password"=>$password,
    				"user_address"=>$user_address,"phone_no"=>$phone_no,"role_id"=>$role_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
    		if ($rslt) {
    			
    			return $rslt;
    		} else {
    			
    			return $rslt;
    		}
    	}
    	catch (PDOException $pde) {
    		throw $pde;
    	}
    }
    
    
    /**
     * Updating User by Making Status Inactive
     * url - /deleteUser
     * method - PUT
     * params -id
     */
    public static function deleteUser($id)
    {
    	try{
    		$query="UPDATE  uni_user_master SET status=0 WHERE id=:id";
    		$rslt= self::updateQuery($query,array("id"=>$id));
    		if ($rslt) {
    			
    			return $rslt;
    		} else {
    			
    			return $rslt;
    		}
    	}
    	catch (PDOException $pde) {
    		throw $pde;
    	}
    }
    
    
    /**
     * Fetching All Users List
     * url-/getAllUsersList
     * method - GET All
     * params
     */
   public  function getAllUsersList() {
    	$query = "SELECT * FROM uni_user_master";
    	$rslt = self::fetchQuery($query,array());
    	if (sizeof($rslt))
    	{
    		return $rslt;
    	}
    	else
    	{
    		return NULL;
    	}
    }
    
    /**
     * Fetching User List based on the id
     * url-/getUserListById
     * method - GET by Id
     * params - id
     */
    public function getUserListById($id) {
    	$query = "SELECT * FROM uni_user_master where id =:id";
    	$rslt = self::fetchQuery($query,array("id"=>$id));
    	if (sizeof($rslt))
    	{
    		return $rslt;
    	}
    	else
    	{
    		return NULL;
    	}
    } 
//***************************************************************Role*********************************************************************************************************//
    
/**
   * Adding new role
   * params -role_name,role_desc,status,date_created,last_updated,created_by,updated_by
**/
    public function addRole($role_name,$role_desc,$status,$date_created,$created_by)
    {
    $response = array();

    try 
    {
     $query="INSERT INTO uni_role(role_name,role_desc,status,date_created,created_by)VALUES(:role_name,:role_desc,:status,:date_created,:created_by)";
     
     $bind_array=array("role_name"=>$role_name,"role_desc"=>$role_desc,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by);
     
     $rslt=self::insertQuery($query,$bind_array);
     if ($rslt) 
     {
     	return $rslt;
     } 
     else 
     {
     	return $rslt;
     }
     }
     catch (PDOException $pde) 
     {
      throw $pde;
     }
   }
   
  
   
 /** 
	 * Updating Role
     * url - /updateRole
     * method - PUT
     * params -id
 */
   public static function updateRole($id,$role_name,$role_desc,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_role SET role_name=:role_name,role_desc=:role_desc,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		$rslt= self::updateQuery($query,array("id"=>$id,"role_name"=>$role_name,"role_desc"=>$role_desc,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
   		if ($rslt) {
   			// User successfully inserted
   			return $rslt;
   		} else {
   			// Failed to create user
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   
   
   /**
    * Updating Role by Making Status Inactive
    * url - /deleteRole
    * method - PUT
    * params -id
    */
   public static function deleteRole($id)
   {
   	try{
   		$query="UPDATE  uni_role SET status=0 WHERE id=:id";
   		$rslt= self::updateQuery($query,array("id"=>$id));
   		if ($rslt) {
   			// User successfully inserted
   			return $rslt;
   		} else {
   			// Failed to create user
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   
   
   /**
    * Fetching All Roles 
    * url-/getAllRoleList
    * method - GET All
    * params
    */
   public  function getAllRoleList() {
   	$query = "SELECT * FROM uni_role";
   	$rslt = self::fetchQuery($query,array());
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   }
   
   /**
    * Fetching Role based on the id
    * url-/getRoleListById
    * method - GET by Id
    * params - id
    */
   public function getRoleListById($id) {
   	$query = "SELECT * FROM uni_role where id =:id";
   	$rslt = self::fetchQuery($query,array("id"=>$id));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   } 
   
   //***************************************************************Clients*********************************************************************************************************//
   
   /**
    * Adding new Client
    * params -client_name,company_id,date_created,created_by,status
    **/
   public function addClients($client_name,$company_id,$date_created,$created_by,$status)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_client_master(client_name,company_id,date_created,created_by,status)VALUES(:client_name,:company_id,:date_created,:created_by,:status)";
   		
   		$bind_array=array("client_name"=>$client_name,"company_id"=>$company_id,"date_created"=>$date_created,"created_by"=>$created_by,"status"=>$status);
   		
   		$rslt=self::insertQuery($query,$bind_array);
   		if ($rslt)
   		{
   			return $rslt;
   		}
   		else
   		{
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde)
   	{
   		throw $pde;
   	}
   }
   
   
   
   /**
    * Updating Client
    * url - /updateClient
    * method - PUT
    * params -id
    */
   public static function updateClients($id,$client_name,$company_id,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_client_master SET client_name=:client_name,company_id=:company_id,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		$rslt= self::updateQuery($query,array("id"=>$id,"client_name"=>$client_name,"company_id"=>$company_id,"last_updated"=>$last_updated,"last_updated"=>$updated_by));
   		if ($rslt) {
   			// User successfully inserted
   			return $rslt;
   		} else {
   			// Failed to create user
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   
   
   /**
    * Updating Client by Making Status Inactive
    * url - /deleteClient
    * method - PUT
    * params -id
    */
   public static function deleteClient($id)
   {
   	try{
   		$query="UPDATE  uni_client_master SET status=0 WHERE id=:id";
   		$rslt= self::updateQuery($query,array("id"=>$id));
   		if ($rslt) {
   			// User successfully inserted
   			return $rslt;
   		} else {
   			// Failed to create user
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   
   
   /**
    * Fetching All Client
    * url-/getAllClientList
    * method - GET All
    * params
    */
   public  function getAllClientList() {
   	$query = "SELECT * FROM uni_client_master";
   	$rslt = self::fetchQuery($query,array());
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   }
   
   /**
    * Fetching Client based on the id
    * url-/getClientListById
    * method - GET by Id
    * params - id
    */
   public function getClientListById($id) {
   	$query = "SELECT * FROM uni_client_master where id =:id";
   	$rslt = self::fetchQuery($query,array("id"=>$id));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   } 
//***************************************************************Project*************************************************//
   /**
    * Adding new Project
    * params -project_name,client_id,company_id,start_date,end_date,project_type,date_created,status
    **/
   public function addProject($project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$date_created,$status)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_project_master(project_name,client_id,company_id,start_date,end_date,project_type,date_created,status) 
                                               VALUES(:project_name,:client_id,:company_id,:start_date,:end_date,:project_type,:date_created,:status)";
   		
   		$bind_array=array("project_name"=>$project_name,"client_id"=>$client_id,"company_id"=>$company_id,"start_date"=>$start_date,"end_date"=>$end_date,
   				"project_type=>$project_type","date_created"=>$date_created,"status"=>$status);
   		
   		$rslt=self::insertQuery($query,$bind_array);
   		if ($rslt)
   		{
   			return $rslt;
   		}
   		else
   		{
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde)
   	{
   		throw $pde;
   	}
   }
   
 /**
    * Updating Project
    * url - /updateProject
    * method - PUT
    * params -id
 */
   public static function updateProject($id,$project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$last_updated)
   {
   	try{
   		$query="UPDATE  uni_project_master SET project_name=:project_name,client_id=:client_id,company_id=:company_id,start_date=:start_date,
                end_date=:end_date,project_type=:project_type,last_updated=:last_updated WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"project_name"=>$project_name,"client_id"=>$client_id,"company_id"=>$company_id,"start_date"=>$start_date,
   				"end_date"=>$end_date,"project_type"=>$project_type,"last_updated"=>$last_updated));
   		if ($rslt) {
   			// User successfully inserted
   			return $rslt;
   		} else {
   			// Failed to create user
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   /**
    * Updating Project by Making Status Inactive
    * url - /deleteProject
    * method - PUT
    * params -id
    */
   public static function deleteProjects($id)
   {
   	try{
   		$query="UPDATE  uni_project_master SET status=0 WHERE id=:id";
   		$rslt= self::updateQuery($query,array("id"=>$id));
   		if ($rslt) {
   			// User successfully inserted
   			return $rslt;
   		} else {
   			// Failed to create user
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   
   
   /**
    * Fetching All Project
    * url-/getAllProjectList
    * method - GET All
    * params
    */
   public  function getAllProjectList() {
   	$query = "SELECT * FROM uni_project_master";
   	$rslt = self::fetchQuery($query,array());
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   }
   
   /**
    * Fetching Project based on the id
    * url-/getProjectListById
    * method - GET by Id
    * params - id
    */
   public function getProjectListById($id) {
   	$query = "SELECT * FROM uni_project_master where id =:id";
   	$rslt = self::fetchQuery($query,array("id"=>$id));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   } 
   //***************************************************************Module*********************************************************************************************************//
   /**
    * Adding Module
    * params-module_name,status,company_id,project_id,date_created,created_by
    **/
   
   public function addModules($module_name,$status,$company_id,$project_id,$date_created,$created_by)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_module(module_name,status,company_id,project_id,date_created,created_by)VALUES(:module_name,:status,:company_id,:project_id,:date_created,:created_by)";
   		
   		$bind_array=array("module_name"=>$module_name,"status"=>$status,"company_id"=>$company_id,"project_id"=>$project_id,"date_created"=>$date_created,"created_by"=>$created_by);
   		
   		$rslt=self::insertQuery($query,$bind_array);
   		if ($rslt)
   		{
   			return $rslt;
   		}
   		else
   		{
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde)
   	{
   		throw $pde;
   	}
   }
   /**
    * Updating Module
    * url - /updateModule
    * method - PUT
    * params -id
    */
   public static function updateModule($id,$module_name,$company_id,$project_id,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_module SET module_name=:module_name,company_id=:company_id,	project_id=:project_id,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"module_name"=>$module_name,"company_id"=>$company_id,"project_id"=>$project_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
   		if ($rslt) {
   			
   			return $rslt;
   		} else {
   			
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   
   /**
    * Updating Module by Making Status Inactive
    * url - /deleteModule
    * method - PUT
    * params -id
    */
   public static function deleteModule($id)
   {
   	try{
   		$query="UPDATE  uni_module SET status=0 WHERE id=:id";
   		$rslt= self::updateQuery($query,array("id"=>$id));
   		if ($rslt) {
   			
   			return $rslt;
   		} else {
   			
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   /**
    * Fetching All Modules
    * url-/getAllModuleList
    * method - GET All
    * params
    */
   public  function getAllModuleList() {
   	$query = "SELECT * FROM uni_module";
   	$rslt = self::fetchQuery($query,array());
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   }
   
   /**
    * Fetching Modules based on the id
    * url-/getModuleListById
    * method - GET by Id
    * params - id
    */
   public function getModuleListById($id) {
   	$query = "SELECT * FROM uni_module where id =:id";
   	$rslt = self::fetchQuery($query,array("id"=>$id));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   }
   //***************************************************************Team*********************************************************************************************************//
   /**
    * Adding Team
    * params-team_name,status,company_id,project_id,date_created,created_by
    **/
   
   public function addModules($module_name,$status,$company_id,$project_id,$date_created,$created_by)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_module(module_name,status,company_id,project_id,date_created,created_by)VALUES(:module_name,:status,:company_id,:project_id,:date_created,:created_by)";
   		
   		$bind_array=array("module_name"=>$module_name,"status"=>$status,"company_id"=>$company_id,"project_id"=>$project_id,"date_created"=>$date_created,"created_by"=>$created_by);
   		
   		$rslt=self::insertQuery($query,$bind_array);
   		if ($rslt)
   		{
   			return $rslt;
   		}
   		else
   		{
   			return $rslt;
   		}
   	}
   	catch (PDOException $pde)
   	{
   		throw $pde;
   	}
   }
   
}
?>

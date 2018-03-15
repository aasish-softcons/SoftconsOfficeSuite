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
    
    public static function ISTConversion()
    { 
    	$time = new DateTime('now', new DateTimeZone('Asia/Calcutta'));
    	return $time->format('Y-m-d H:i:s');
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
    //**************************************************Timesheet_config************************************************************************/
    /**
     * Adding addTimesheetConfig
     * params-company_id,timesheet_frequency,status,date_created,created_by,start_date,end_date
     **/
    
    public function addTimesheetConfig($company_id,$timesheet_frequency,$status,$date_created,$created_by,$start_date,$end_date)
    {
    	$response = array();
    	
    	try
    	{
    		$query="INSERT INTO timesheet_config(company_id,timesheet_frequency,status,date_created,created_by,start_date,end_date)VALUES(:company_id,:timesheet_frequency,:status,:date_created,:created_by,:start_date,:end_date)";
    		
    		$bind_array=array("company_id"=>$company_id,"timesheet_frequency"=>$timesheet_frequency,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
    		
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
     * Updating timesheet_config
     * url - /updateTimesheetConfig
     * method - PUT
     * params -id
     */
    public static function updateTimesheetConfig($id,$company_id,$timesheet_frequency,$last_updated,$updated_by,$start_date,$end_date)
    {
    	try{
    		$query="UPDATE  timesheet_config SET company_id=:company_id,timesheet_frequency=:timesheet_frequency,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date WHERE id=:id";
    		
    		$rslt= self::updateQuery($query,array("id"=>$id,"company_id"=>$company_id,"timesheet_frequency"=>$timesheet_frequency,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date));
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
     * Updating Timesheet_config by Making Status Inactive
     * url - /deleteTimesheetConfig
     * method - PUT
     * params -id
     */
    public static function deleteTimesheetConfig($id)
    {
    	try{
    		$query="UPDATE  timesheet_config SET status=0 WHERE id=:id";
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
	 * Fetching All Timesheet_config List
	 * url-/getAllTimesheetConfigList
	 * method - GET All
	 * params 
	 */
    public function getAllTimesheetConfigList() {
    	$query = "SELECT * FROM timesheet_config";
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
     * Fetching Timesheet_config List based on the id
     * url-/getTimesheetConfigListById
     * method - GET by Id
     * params - id
     */
    public function getTimesheetConfigListById($id) {
    	$query = "SELECT * FROM timesheet_config where id =:id";
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
     * Fetching Company Details
     * url-/companyExists
     * method - GET
     * params
     */
    public function userExists($email_id) {
    	$query = "SELECT email_id FROM uni_user_master WHERE email_id=:email_id";
    	$rslt = self::fetchQuery($query,array("email_id"=>$email_id));
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
    
    public function updateCompany($id,$contact_person,$address,$phone_no,$email_id,$subscription_date,$last_updated,$nominal_flag,$updated_by,$sprint_days)
    {
    	
    	$response = array();
    	try {
    		$query="UPDATE uni_company_master SET contact_person = '$contact_person', address = '$address',  phone_no = '$phone_no', email_id = '$email_id',subscription_date = '$subscription_date',last_updated = '$last_updated',nominal_flag = '$nominal_flag',updated_by = '$updated_by',sprint_days = '$sprint_days' WHERE id = '$id'";
    		$bind_array=array("id"=>$id,"contact_person"=>$contact_person,"address"=>$address,"phone_no"=>$phone_no,"email_id"=>$email_id,"subscription_date"=>$subscription_date,"last_updated"=>$last_updated,"nominal_flag"=>$nominal_flag,"updated_by"=>$updated_by,"sprint_days"=>$sprint_days);
    		$rslt=self::updateQuery($query,$bind_array);
    		if ($rslt) {
    			// asset type config successfully inserted
    			
    			//$userID = mysql_insert_id();
    			
    			//return $userID;
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
  
    
    //***************************************************************Department***********************************************************************************************//
    /**
     * Adding Department
     * params-company_id,department_name,department_head,department_location,department_function,department_members,status,date_created,created_by,start_date,end_date
     **/
    
    public function addDepartment($company_id,$department_name,$department_head,$department_location,$department_function,$department_members,$status,$date_created,$created_by,$start_date,$end_date)
    {
    	$response = array();
    	
    	try
    	{
    		$query="INSERT INTO uni_department(company_id,department_name,department_head,department_location,department_function,department_members,status,date_created,created_by,start_date,end_date)VALUES(:company_id,:department_name,:department_head,:department_location,:department_function,:department_members,:status,:date_created,:created_by,:start_date,:end_date)";
    		
    		$bind_array=array("company_id"=>$company_id,"department_name"=>$department_name,"department_head"=>$department_head,"department_location"=>$department_location,"department_function"=>$department_function,"department_members"=>$department_members,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
    		
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
    public static function updateDepartment($id,$company_id,$department_name,$department_head,$department_location,$department_function,$department_members,$last_updated,$updated_by,$start_date,$end_date)
    {
    	try{
    		$query="UPDATE  uni_department SET company_id=:company_id,department_name=:department_name,department_head=:department_head,department_location=:department_location,department_function=:department_function,department_members=:department_members,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date WHERE id=:id";
    		
    		$rslt= self::updateQuery($query,array("id"=>$id,"company_id"=>$company_id,"department_name"=>$department_name,"department_head"=>$department_head,"department_location"=>$department_location,"department_function"=>$department_function,"department_members"=>$department_members,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date));
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
    
    /**
   		 * Fetching Department List based on the company_id
		 * url-/getAllDepartmentByCId
		 * method - GET by company_id
		 * params - id
     */
    public function getAllDepartmentByCId($company_id,$cDate) {
    	//$cDate = ISTConversion();

    	$query = "SELECT * FROM uni_department where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
    	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
    	if (sizeof($rslt))
    	{
    		return $rslt;
    	}
    	else
    	{
    		return NULL;
    	}
    	//return $time;
    } 
    
    
    //***************************************************************Users****************************************************************************************************//
    /**
     * Adding new User
     * params-first_name,last_name,company_id,email_id,password,user_address,phone_no,role_id,department_id,status,date_created,created_by,password_activation,password_link_timestamp,version,position,start_date,end_date
     **/
    public function addUser($first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$status,$date_created,$created_by,$department_id,$position,$start_date,$end_date)
    {
    	$response = array();
    	
    	try
    	{
    		$query="INSERT INTO uni_user_master(first_name,last_name,company_id,email_id,password,user_address,phone_no,role_id,status,date_created,created_by,department_id,position,start_date,end_date)
            VALUES(:first_name,:last_name,:company_id,:email_id,:password,:user_address,:phone_no,:role_id,:status,:date_created,:created_by,:department_id,:position,:start_date,:end_date)";
    		
    		$bind_array=array("first_name"=>$first_name,"last_name"=>$last_name,"company_id"=>$company_id,"email_id"=>$email_id,"password"=>$password,
    				"user_address"=>$user_address,"phone_no"=>$phone_no,"role_id"=>$role_id,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"department_id"=>$department_id,"position"=>$position,"start_date"=>$start_date,"end_date"=>$end_date);
    		
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
    public static function updateUser($id,$first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$last_updated,$updated_by,$department_id,$position,$start_date,$end_date)
    {
    	try{
    		$query="UPDATE  uni_user_master SET first_name=:first_name,last_name=:last_name,company_id=:company_id,email_id=:email_id,password=:password,
                    user_address=:user_address,phone_no=:phone_no,role_id=:role_id,last_updated=:last_updated,updated_by=:updated_by,department_id=:department_id,position=:position,start_date=:start_date,end_date=:end_date WHERE id=:id";
    		
    		$rslt= self::updateQuery($query,array("id"=>$id,"first_name"=>$first_name,"last_name"=>$last_name,"company_id"=>$company_id,"email_id"=>$email_id,"password"=>$password,
    				"user_address"=>$user_address,"phone_no"=>$phone_no,"role_id"=>$role_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"department_id"=>$department_id,"position"=>$position,"start_date"=>$start_date,"end_date"=>$end_date));
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
    
    /**
     * Fetching User List based on the company_id
     * url-/getAllUserByCId
     * method - GET by company_id
     * params - id
     */
    public function getAllUserByCId($company_id,$cDate) {
    	
    	
    	$query = "SELECT * FROM uni_user_master where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
    	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
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
   * params -role_name,role_desc,status,date_created,created_by,start_date,end_date
**/
    public function addRole($role_name,$role_desc,$status,$date_created,$created_by,$start_date,$end_date)
    {
    $response = array();

    try 
    {
     $query="INSERT INTO uni_role(role_name,role_desc,status,date_created,created_by,start_date,end_date)VALUES(:role_name,:role_desc,:status,:date_created,:created_by,:start_date,:end_date)";
     
     $bind_array=array("role_name"=>$role_name,"role_desc"=>$role_desc,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
     
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
   public static function updateRole($id,$role_name,$role_desc,$last_updated,$updated_by,$start_date,$end_date)
   {
   	try{
   		$query="UPDATE  uni_role SET role_name=:role_name,role_desc=:role_desc,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date WHERE id=:id";
   		$rslt= self::updateQuery($query,array("id"=>$id,"role_name"=>$role_name,"role_desc"=>$role_desc,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date));
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
   
   /**
    * Fetching Role List based on the company_id
    * url-/getAllRoleByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllRoleByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_role where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
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
    * params -client_name,company_id,website_url,pan,gstn,regisered_address,managing_director,mailing_address,contact_person,phone_number,email_id,status,date_created,created_by,start_date,end_date
    **/
   public function addClients($client_name,$company_id,$website_url,$pan,$gstn,$registered_address,$managing_director,$mailing_address,$contact_person,$phone_number,$email_id,$status,$date_created,$created_by,$start_date,$end_date)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_client_master(client_name,company_id,website_url,pan,gstn,registered_address,managing_director,mailing_address,contact_person,phone_number,email_id,status,date_created,created_by,start_date,end_date)VALUES(:client_name,:company_id,:website_url,:pan,:gstn,:registered_address,:managing_director,:mailing_address,:contact_person,:phone_number,:email_id,:status,:date_created,:created_by,:start_date,:end_date)";
   		
   		$bind_array=array("client_name"=>$client_name,"company_id"=>$company_id,"website_url"=>$website_url,"pan"=>$pan,"gstn"=>$gstn,"registered_address"=>$registered_address,"managing_director"=>$managing_director,"mailing_address"=>$mailing_address,"contact_person"=>$contact_person,"phone_number"=>$phone_number,"email_id"=>$email_id,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
   		
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
   public static function updateClients($id,$client_name,$company_id,$website_url,$pan,$gstn,$registered_address,$managing_director,$mailing_address,$contact_person,$phone_number,$email_id,$last_updated,$updated_by,$start_date,$end_date)
   {
   	try{
   		$query="UPDATE  uni_client_master SET client_name='$client_name',company_id='$company_id',website_url='$website_url',pan='$pan',gstn='$gstn',registered_address='$registered_address',managing_director='$managing_director',mailing_address='$mailing_address',contact_person='$contact_person',phone_number='$phone_number',email_id='$email_id',last_updated='$last_updated',updated_by='$updated_by',start_date='$start_date',end_date='$end_date' WHERE id='$id'";
   		$rslt= self::updateQuery($query,array("id"=>$id,"client_name"=>$client_name,"company_id"=>$company_id,"website_url"=>$website_url,"pan"=>$pan,"gstn"=>$gstn,"registered_address"=>$registered_address,"managing_director"=>$managing_director,"mailing_address"=>$mailing_address,"contact_person"=>$contact_person,"phone_number"=>$phone_number,"email_id"=>$email_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>'$start_date',"end_date"=>'$end_date'));
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
   
   /**
    * Fetching Client List based on the company_id
    * url-/getAllClientByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllClientByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_client_master where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
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
    * params -project_name,client_id,company_id,start_date,end_date,project_type,date_created,status,billable_type,billing_type,team_id
    **/
   public function addProject($project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$date_created,$status,$billable_type,$billing_type,$team_id,$created_by)
   {
   	$response = array();
   	
   	try
   	{
   		//$query="INSERT INTO uni_project_master(project_name,client_id,company_id,start_date,end_date,project_type,date_created,status)VALUES(:project_name,:client_id,:company_id,:start_date,:end_date,:project_type,:date_created,:status)";
   		$query = "INSERT INTO uni_project_master(project_name,client_id,company_id,start_date,end_date,project_type,date_created,status,billable_type,billing_type,team_id,created_by)VALUES('$project_name','$client_id','$company_id','$start_date','$end_date','$project_type','$date_created','$status','$billable_type','$billing_type','$team_id','$created_by')";
   		
   		$bind_array=array("project_name"=>$project_name,"client_id"=>$client_id,"company_id"=>$company_id,"start_date"=>$start_date,"end_date"=>$end_date,"project_type=>$project_type","date_created"=>$date_created,"status"=>$status,"billable_type"=>$billable_type,"billing_type"=>$billing_type,"team_id"=>$team_id,"created_by"=>$created_by);
   		
   		$rslt=self::insertQuery($query,$bind_array);
   		if ($rslt)
   		{
   			return $query;
   		}
   		else
   		{
   			return $query;
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
   public static function updateProject($id,$project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$last_updated,$updated_by,$billable_type,$billing_type,$team_id)
   {
   	try{
   		//$query="UPDATE uni_project_master SET project_name=:project_name,client_id=:client_id,company_id=:company_id,start_date=:start_date,end_date=:end_date,project_type=:project_type,last_updated=:last_updated WHERE id=:id";
   		$query="UPDATE uni_project_master SET project_name='$project_name',client_id='$client_id',company_id='$company_id',start_date='$start_date',end_date='$end_date',project_type='$project_type',last_updated='$last_updated',updated_by='$updated_by',billing_type='$billable_type',billing_type='$billing_type',team_id='$team_id' WHERE id='$id'";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"project_name"=>$project_name,"client_id"=>$client_id,"company_id"=>$company_id,"start_date"=>$start_date,"end_date"=>$end_date,"project_type"=>$project_type,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"billing_type"=>$billable_type,"billing_type"=>$billing_type,"team_id"=>$team_id));
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
   
   /**
    * Fetching Project List based on the company_id
    * url-/getAllProjectByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllProjectByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT up.*, ut.team_name, uc.client_name FROM uni_project_master as up, uni_team as ut, uni_client_master as uc where up.start_date <= '$cDate' AND up.end_date >= '$cDate' AND up.company_id = '$company_id' AND up.status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
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
    * params-module_name,status,company_id,project_id,date_created,created_by,start_date,end_date
    **/
   
   public function addModules($module_name,$status,$company_id,$project_id,$date_created,$created_by,$start_date,$end_date)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_module(module_name,status,company_id,project_id,date_created,created_by,start_date,end_date)VALUES(:module_name,:status,:company_id,:project_id,:date_created,:created_by,:start_date,:end_date)";
   		
   		$bind_array=array("module_name"=>$module_name,"status"=>$status,"company_id"=>$company_id,"project_id"=>$project_id,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
   		
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
   public static function updateModule($id,$module_name,$company_id,$project_id,$last_updated,$updated_by,$start_date,$end_date)
   {
   	try{
   		$query="UPDATE  uni_module SET module_name=:module_name,company_id=:company_id,	project_id=:project_id,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"module_name"=>$module_name,"company_id"=>$company_id,"project_id"=>$project_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date));
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
   
   /**
    * Fetching Module List based on the company_id
    * url-/getAllModuleByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllModuleByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_module where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
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
    * params-team_name,status,company_id,date_created,created_by,start_date,end_date
    **/
   
   public function addTeams($team_name,$status,$company_id,$date_created,$created_by,$start_date,$end_date)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO  uni_team(team_name,status,company_id,date_created,created_by,start_date,end_date)VALUES(:team_name,:status,:company_id,:date_created,:created_by,:start_date,:end_date)";
   		
   		$bind_array=array("team_name"=>$team_name,"status"=>$status,"company_id"=>$company_id,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
   		
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
    * Updating Team
    * url - /updateTeam
    * method - PUT
    * params -id
    */
   public static function updateTeam($id,$team_name,$company_id,$last_updated,$updated_by,$start_date,$end_date)
   {
   	try{
   		$query="UPDATE  uni_team SET team_name=:team_name,company_id=:company_id,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"team_name"=>$team_name,"company_id"=>$company_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date));
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
    * Updating Team by Making Status Inactive
    * url - /deleteModule
    * method - PUT
    * params -id
    */
   public static function deleteTeam($id)
   {
   	try{
   		$query="UPDATE  uni_team SET status=0 WHERE id=:id";
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
    * Fetching All Teams
    * url-/getAllTeamList
    * method - GET All
    * params
    */
   public  function getAllTeamList() {
   	$query = "SELECT * FROM uni_team";
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
    * Fetching Teams based on the id
    * url-/getTeamListById
    * method - GET by Id
    * params - id
    */
   public function getTeamListById($id) {
   	$query = "SELECT * FROM uni_team where id =:id";
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
    * Fetching Team List based on the company_id
    * url-/getAllTeamByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllTeamByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_team where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   	
   } 
//***************************************************************Team Members*********************************************************************************************************//
   /**
    * Adding TeamMembers
    * params-user_id,project_id,status,company_id,isteamlead,date_created,created_by
    **/
   
   public function addTeamMembers($user_id,$project_id,$status,$company_id,$isteamlead,$start_date,$end_date,$date_created,$created_by)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_team_members(user_id,project_id,status,company_id,isteamlead,start_date,end_date,date_created,created_by)VALUES(:user_id,:project_id,:status,:company_id,:isteamlead,:start_date,:end_date,:date_created,:created_by)";
   		
   		$bind_array=array("user_id"=>$user_id,"project_id"=>$project_id,"status"=>$status,"company_id"=>$company_id,"isteamlead"=>$isteamlead,"start_date"=>$start_date,"end_date"=>$end_date,"date_created"=>$date_created,"created_by"=>$created_by);
   		
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
    * Updating TeamMembers
    * url - /updateTeamMembers
    * method - PUT
    * params -id
    */
   public static function updateTeamMembers($id,$user_id,$company_id,$project_id,$isteamlead,$start_date,$end_date,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_team_members SET user_id=:user_id,isteamlead=:isteamlead,start_date=:start_date,end_date=:end_date,company_id=:company_id,project_id=:project_id,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"user_id"=>$user_id,"isteamlead"=>$isteamlead,"start_date"=>$start_date,"end_date"=>$end_date,"company_id"=>$company_id,"project_id"=>$project_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
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
    * Updating TeamMembers by Making Status Inactive
    * url - /deleteTeamMember
    * method - PUT
    * params -id
    */
   public static function deleteTeamMember($id)
   {
   	try{
   		$query="UPDATE  uni_team_members SET status=0 WHERE id=:id";
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
    * Fetching All TeamMembers
    * url-/getAllTeamMemberList
    * method - GET All
    * params
    */
   public  function getAllTeamMemberList() {
   	$query = "SELECT * FROM uni_team_members";
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
    * Fetching TeamMembers based on the id
    * url-/getTeamMemberListById
    * method - GET by Id
    * params - id
    */
   public function getTeamMemberListById($id) {
   	$query = "SELECT * FROM uni_team_members where id =:id";
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
    * Fetching TeamMembers list  based on the company_id
    * url-/getAllTeamMemberByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllTeamMemberByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_team_members where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   	
   } 

//***************************************************************Sprint Plan*********************************************************************************************************//
   /**
    * Adding Sprint Plan
    * params-sprint_name,status,company_id,no_of_timesheets,timesheet_type,start_date,end_date,date_created,created_by
    **/
   
   public function addSprint($sprint_name,$status,$company_id,$no_of_timesheets,$timesheet_type,$start_date,$end_date,$date_created,$created_by)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_sprint_plan(sprint_name,status,company_id,no_of_timesheets,timesheet_type,start_date,end_date,date_created,created_by)VALUES(:sprint_name,:status,:company_id,:no_of_timesheets,:timesheet_type,:start_date,:end_date,:date_created,:created_by)";
   		
   		$bind_array=array("sprint_name"=>$sprint_name,"status"=>$status,"company_id"=>$company_id,"no_of_timesheets"=>$no_of_timesheets,"timesheet_type"=>$timesheet_type,"start_date"=>$start_date,"end_date"=>$end_date,"date_created"=>$date_created,"created_by"=>$created_by);
   		
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
    * Updating Sprint Plan
    * url - /updateSprint
    * method - PUT
    * params -id
    */
   public static function updateSprint($id,$sprint_name,$company_id,$no_of_timesheets,$timesheet_type,$start_date,$end_date,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_sprint_plan SET sprint_name=:sprint_name,company_id=:company_id,no_of_timesheets=:no_of_timesheets,timesheet_type=:timesheet_type,start_date=:start_date,end_date=:end_date,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"sprint_name"=>$sprint_name,"company_id"=>$company_id,"no_of_timesheets"=>$no_of_timesheets,"timesheet_type"=>$timesheet_type,"start_date"=>$start_date,"end_date"=>$end_date,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
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
    * Updating Sprint Plan by Making Status Inactive
    * url - /deleteModule
    * method - PUT
    * params -id
    */
   public static function deleteSprint($id)
   {
   	try{
   		$query="UPDATE  uni_sprint_plan SET status=0 WHERE id=:id";
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
    * Fetching All Sprint Plan
    * url-/getAllSprintList
    * method - GET All
    * params
    */
   public  function getAllSprintList() {
   	$query = "SELECT * FROM uni_sprint_plan";
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
    * Fetching Sprint Plan list based on the id
    * url-/getSprintListById
    * method - GET by Id
    * params - id
    */
   public function getSprintListById($id) {
   	$query = "SELECT * FROM uni_sprint_plan where id =:id";
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
    * Fetching  Sprint Plan list based on the company_id
    * url-/getAllSprintByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllSprintByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_sprint_plan where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   	
   } 
   //***************************************************************Tags*********************************************************************************************************//
   /**
    * Adding Tags
    * params-tag_name,tag_description,start_date,end_date,status,date_created,created_by
    **/
   
   public function addTags($tag_name,$tag_description,$start_date,$end_date,$status,$date_created,$created_by)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_tags(tag_name,tag_description,start_date,end_date,status,date_created,created_by)VALUES(:tag_name,:tag_description,:start_date,:end_date,:status,:date_created,:created_by)";
   		
   		$bind_array=array("tag_name"=>$tag_name,"tag_description"=>$tag_description,"start_date"=>$start_date,"end_date"=>$end_date,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by);
   		
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
    * Updating Tags
    * url - /updateTags
    * method - PUT
    * params -id
    */
   public static function updateTags($id,$tag_name,$tag_description,$start_date,$end_date,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_tags SET tag_name=:tag_name,tag_description=:tag_description,start_date=:start_date,end_date=:end_date,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"tag_name"=>$tag_name,"tag_description"=>$tag_description,"start_date"=>$start_date,"end_date"=>$end_date,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
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
    * Updating Tags by Making Status Inactive
    * url - /deleteTags
    * method - PUT
    * params -id
    */
   public static function deleteTags($id)
   {
   	try{
   		$query="UPDATE  uni_tags SET status=0 WHERE id=:id";
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
    * Fetching All Tags
    * url-/getAllTagsList
    * method - GET All
    * params
    */
   public  function getAllTagsList() {
   	$query = "SELECT * FROM uni_tags";
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
    * Fetching Tags based on the id
    * url-/getTagsListById
    * method - GET by Id
    * params - id
    */
   public function getTagsListById($id) {
   	$query = "SELECT * FROM uni_tags where id =:id";
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
    * Fetching Tags List  based on the company_id
    * url-/getAllTagsByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllTagsByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_tags where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   	
   }
   
   //***************************************************************Token Master*********************************************************************************************************//
   /**
    * Adding Token Master
    * params-user_id,auth_token,issued_on,issued_for,expireson,date_created,created_by,start_date,end_date
    **/
   
   public function addTokenMaster($user_id,$auth_token,$auth_token,$issued_on,$issued_for,$expireson,$date_created,$created_by,$start_date,$end_date)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_tokenmaster(user_id,auth_token,issued_on,issued_for,expireson,date_created,created_by,start_date,end_date)VALUES(:user_id,:auth_token,:issued_on,:issued_for,:expireson,:date_created,:created_by,:start_date,:end_date)";
   		
   		$bind_array=array("user_id"=>$user_id,"auth_token"=>$auth_token,"issued_on"=>$issued_on,"issued_for"=>$issued_for,"expireson"=>$expireson,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
   		
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
    * Updating Token Master
    * url - /updateTokenMaster
    * method - PUT
    * params -id
    */
   public static function updateTokenMaster($id,$user_id,$auth_token,$issued_on,$issued_for,$expireson,$start_date,$end_date,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_tokenmaster SET user_id=:user_id,auth_token=:auth_token,issued_on=:issued_on,issued_for=:issued_for,expireson=:expireson,start_date=:start_date,end_date=:end_date,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"user_id"=>$user_id,"auth_token"=>$auth_token,"issued_on"=>$issued_on,"issued_for"=>$issued_for,"expireson"=>$expireson,"start_date"=>$start_date,"end_date"=>$end_date,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
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
    * Updating Token Master by Making Status Inactive
    * url - /deleteTokenMaster
    * method - PUT
    * params -id
    */
   public static function deleteTokenMaster($id)
   {
   	try{
   		$query="UPDATE  uni_tokenmaster SET status=0 WHERE id=:id";
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
    * Fetching All Token Master
    * url-/getAllTokenMasterList
    * method - GET All
    * params
    */
   public  function getAllTokenMasterList() {
   	$query = "SELECT * FROM uni_tokenmaster";
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
    * Fetching Token Master list based on the id
    * url-/getTokenMasterListById
    * method - GET by Id
    * params - id
    */
   public function getTokenMasterListById($id) {
   	$query = "SELECT * FROM uni_tokenmaster where id =:id";
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
    * Fetching  Token Master list based on the company_id
    * url-/getAllTokenMasterByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllTokenMasterByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_tokenmaster where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("company_id"=>$company_id,"cDate"=>$cDate));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   	
   } 
   
//***************************************************************Forgot Password*********************************************************************************************************//   

   
   
   /**
    *
    * Forgot Password
    * @param emailid
    */
   public static function forgotPassword($email_id,$password_link_timestamp)
   {
   	$query="UPDATE uni_user_master SET password_activation=1,password_link_timestamp=:password_link_timestamp WHERE email_id=:email_id";
   	$rslt= self::updateQuery($query,array("email_id"=>$email_id,"password_link_timestamp"=>$password_link_timestamp));
   	
   	$query="select * from uni_user_master where email_id=:email_id";
   	$rslt = self::fetchQuery($query,array("email_id"=>$email_id));
   	if(sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else{
   		return NULL;
   	}
   	
   }
   
   /**
    *
    * Change Password
    * @param id,password,update_by
    */
   public static function changePassword($id,$password,$current_time)
   {
   	try{
   		$query="select password_link_timestamp from uni_user_master where id=$id AND password_activation=1";
   		$result = self::fetchQuery($query,array("id"=>$id));
   		if($result){
   			$date1=date_create($current_time);
   			$date2=date_create($result[0]["password_link_timestamp"]);
   			$diff=date_diff($date1,$date2);
   			if($diff->format("%h") < 48){
   				$query="UPDATE uni_user_master SET password=:password,password_activation=0 WHERE id=:id";
   				$rslt= self::updateQuery($query,array("id"=>$id,"password"=>$password));
   				if ($rslt) {
   					// User successfully inserted
   					return "Password Updated Succesfully";
   				} else {
   					// Failed to create user
   					return "Password Not Updated";
   				}
   			}
   			else{
   				return "Password Link Expired";
   			}
   		}
   		else{
   			return "Password Link Expired";
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }
   
   
   
   /**
    * Creating new user
    * @param String asset_type_id
    * @param String field_name,field_type,,created_by,updated_by
    *
    */
   public function updatePassword($pwd, $user_id)
   {
   	$response = array();
   	
   	// First check if asset type already existed in db
   	try {
   		$query="UPDATE uni_user_master SET 	password = '$pwd' where email_id='$email_id'";
   		$bind_array=array("pwd"=>$pwd,"email_id"=>$email_id);
   		$rslt=self::updateQuery($query,$bind_array);
   		if ($rslt) {
   			// asset type config successfully inserted
   			//return $rslt;
   			return PASSWORD_UPDATED_SUCCESSFULLY;
   		} else {
   			// Failed to create user
   			//return $rslt;
   			return PASSWORD_UPDATED_FAILED;
   		}
   	}
   	catch (PDOException $pde) {
   		throw $pde;
   	}
   }		
	


 /** * get Survey List based on Survey id
     * @param tagno
     */
    public static function getPassword($email_id, $status)
    {
		$query = "select * FROM uni_user_master WHERE email_id='$email_id' AND status='$status'";
		$rslt = self::fetchQuery($query,array("email_id"=>$email_id,"status"=>$status));
        if(sizeof($rslt))
        {
          return $rslt;  
        }
        else{
            return NULL;
        }
        
    }	   
   
}
?>

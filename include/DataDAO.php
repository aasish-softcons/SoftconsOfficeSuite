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
     * params-company_id,timesheet_type,status,date_created,created_by,start_date,end_date
     **/
    
    public function addTimesheetConfig($company_id,$timesheet_type,$status,$date_created,$created_by,$start_date,$end_date)
    {
    	$response = array();
    	
    	try
    	{
    		$query="INSERT INTO timesheet_config(company_id,timesheet_type,status,date_created,created_by,start_date,end_date)VALUES(:company_id,:timesheet_type,:status,:date_created,:created_by,:start_date,:end_date)";
    		
    		$bind_array=array("company_id"=>$company_id,"timesheet_type"=>$timesheet_type,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date);
    		
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
    public static function updateTimesheetConfig($id,$company_id,$timesheet_type,$last_updated,$updated_by,$start_date,$end_date)
    {
    	try{
    		$query="UPDATE  timesheet_config SET company_id=:company_id,timesheet_type=:timesheet_type,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date WHERE id=:id";
    		
    		$rslt= self::updateQuery($query,array("id"=>$id,"company_id"=>$company_id,"timesheet_type"=>$timesheet_type,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date));
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
    
    /**
     * Fetching Timesheet_config List based on the company_id
     * url-/getTimesheetConfigListByCId
     * method - GET by company_id
     * params - id
     */
    public function getTimesheetConfigListByCId($company_id,$cDate) {
    	//$cDate = ISTConversion();
    	
    	$query = "SELECT * FROM timesheet_config where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
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
    	
    	
    	$query = "SELECT uu.*, ud.department_name, ur.role_name FROM uni_user_master as uu, uni_department as ud, uni_role as ur where uu.start_date <= '$cDate' AND uu.end_date >= '$cDate' AND uu.company_id = '$company_id' AND uu.status=1";
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
   	
   	$query = "SELECT um.*, up.project_name FROM uni_module as um, uni_project_master as up where um.start_date <= '$cDate' AND um.end_date >= '$cDate' AND um.company_id = '$company_id' AND um.status=1";
   	//$query = "SELECT * FROM uni_module where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
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
   //***********************************************************************************************************************Team*************************************************************************************************************************************//
   /**
    * Adding Team
    * params-team_name,status,company_id,date_created,created_by,start_date,end_date,location,function,teamlead,members,department_id
    **/
   
   public function addTeams($team_name,$status,$company_id,$date_created,$created_by,$start_date,$end_date,$location,$function,$teamlead,$members,$department_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO  uni_team(team_name,status,company_id,date_created,created_by,start_date,end_date,location,function,teamlead,members,department_id)VALUES(:team_name,:status,:company_id,:date_created,:created_by,:start_date,:end_date,:location,:function,:teamlead,:members,department_id)";
   		
   		$bind_array=array("team_name"=>$team_name,"status"=>$status,"company_id"=>$company_id,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date,"location"=>$location,"function"=>$function,"teamlead"=>$teamlead,"members"=>$members,"department_id"=>$department_id);
   		
   		$rslt=self::insertQuery($query,$bind_array);
   		/*if ($rslt)
   		{
   			
   			return $rslt;
   			
   		}*/
   		if ($rslt)
   		{
   		$team_id = $rslt;
   		$query="INSERT INTO uni_team_members(status,company_id,isteamlead,team_id)VALUES(:status,:company_id,1,:team_id)";
   		$bind_array=array("status"=>$status,"company_id"=>$company_id,"team_id"=>$team_id);
   		$rslt1=self::insertQuery($query,$bind_array);
   		return $rslt1;
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
   public static function updateTeam($id,$team_name,$company_id,$last_updated,$updated_by,$start_date,$end_date,$location,$function,$teamlead,$members,$department_id)
   {
   	try{
   		$query="UPDATE  uni_team SET team_name=:team_name,company_id=:company_id,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date,location=:location,function=:function,
         teamlead=:teamlead,members=:members,department_id=:department_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"team_name"=>$team_name,"company_id"=>$company_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date,"location"=>$location,
   				"function"=>$function,"teamlead"=>$teamlead,"members"=>$members,"department_id"=>$department_id));
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
   
   public function addTeamMembers($user_id,$project_id,$status,$company_id,$isteamlead,$start_date,$end_date,$date_created,$created_by,$team_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_team_members(user_id,project_id,status,company_id,isteamlead,start_date,end_date,date_created,created_by,team_id)VALUES(:user_id,:project_id,:status,:company_id,:isteamlead,:start_date,:end_date,:date_created,:created_by,:team_id)";
   		
   		$bind_array=array("user_id"=>$user_id,"project_id"=>$project_id,"status"=>$status,"company_id"=>$company_id,"isteamlead"=>$isteamlead,"start_date"=>$start_date,"end_date"=>$end_date,"date_created"=>$date_created,"created_by"=>$created_by,"team_id"=>$team_id);
   		
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
   public static function updateTeamMembers($id,$user_id,$company_id,$project_id,$isteamlead,$start_date,$end_date,$last_updated,$updated_by,$team_id)
   {
   	try{
   		$query="UPDATE  uni_team_members SET user_id=:user_id,isteamlead=:isteamlead,start_date=:start_date,end_date=:end_date,company_id=:company_id,project_id=:project_id,last_updated=:last_updated,updated_by=:updated_by,team_id=:team_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"user_id"=>$user_id,"isteamlead"=>$isteamlead,"start_date"=>$start_date,"end_date"=>$end_date,"company_id"=>$company_id,"project_id"=>$project_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"team_id"=>$team_id));
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
    * params-tag_name,tag_description,start_date,end_date,status,date_created,created_by,company_id
    **/
   
   public function addTags($tag_name,$tag_description,$start_date,$end_date,$status,$date_created,$created_by,$company_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_tags(tag_name,tag_description,start_date,end_date,status,date_created,created_by,company_id)VALUES(:tag_name,:tag_description,:start_date,:end_date,:status,:date_created,:created_by,:company_id)";
   		
   		$bind_array=array("tag_name"=>$tag_name,"tag_description"=>$tag_description,"start_date"=>$start_date,"end_date"=>$end_date,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"company_id"=>$company_id);
   		
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
   public static function updateTags($id,$tag_name,$tag_description,$start_date,$end_date,$last_updated,$updated_by,$company_id)
   {
   	try{
   		$query="UPDATE  uni_tags SET tag_name=:tag_name,tag_description=:tag_description,start_date=:start_date,end_date=:end_date,last_updated=:last_updated,updated_by=:updated_by,company_id=:company_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"tag_name"=>$tag_name,"tag_description"=>$tag_description,"start_date"=>$start_date,"end_date"=>$end_date,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"company_id"=>$company_id));
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
    * params-user_id,auth_token,issued_on,issued_for,expireson,date_created,created_by,start_date,end_date,company_id 
    **/
   
   public function addTokenMaster($user_id,$auth_token,$auth_token,$issued_on,$issued_for,$expireson,$date_created,$created_by,$start_date,$end_date,$company_id )
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_tokenmaster(user_id,auth_token,issued_on,issued_for,expireson,date_created,created_by,start_date,end_date,company_id)VALUES(:user_id,:auth_token,:issued_on,:issued_for,:expireson,:date_created,:created_by,:start_date,:end_date,:company_id)";
   		
   		$bind_array=array("user_id"=>$user_id,"auth_token"=>$auth_token,"issued_on"=>$issued_on,"issued_for"=>$issued_for,"expireson"=>$expireson,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date,"company_id"=>$company_id);
   		
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
   public static function updateTokenMaster($id,$user_id,$auth_token,$issued_on,$issued_for,$expireson,$start_date,$end_date,$last_updated,$updated_by,$company_id)
   {
   	try{
   		$query="UPDATE  uni_tokenmaster SET user_id=:user_id,auth_token=:auth_token,issued_on=:issued_on,issued_for=:issued_for,expireson=:expireson,start_date=:start_date,end_date=:end_date,last_updated=:last_updated,updated_by=:updated_by,company_id=:company_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"user_id"=>$user_id,"auth_token"=>$auth_token,"issued_on"=>$issued_on,"issued_for"=>$issued_for,"expireson"=>$expireson,"start_date"=>$start_date,"end_date"=>$end_date,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"company_id"=>$company_id));
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
 //***************************************************************Tickets*************************************************************************************************************************************************//
   /** Adding Tickets
   * url - /addTickets
   * method - POST
   * params-ticket_number,is_billable,sprint_id,ticket_type,project_id,ticket_description,ticket_date,priority,ticket_status,due_date,team_id,external_ticket_id,estimated_hours,tag_id,start_date,end_date,status,date_created,created_by,company_id
   */
   
   public function addTickets($ticket_number,$is_billable,$sprint_id,$ticket_type,$project_id,$ticket_description,$ticket_date,$priority,$ticket_status,$due_date,$team_id,$external_ticket_id,$estimated_hours,$tag_id,$start_date,$end_date,$status,$date_created,$created_by,$company_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_tickets(ticket_number,is_billable,sprint_id,ticket_type,project_id,ticket_description,ticket_date,priority,ticket_status,due_date,team_id,external_ticket_id,estimated_hours,tag_id,start_date,end_date,status,date_created,created_by,company_id)
                VALUES(:ticket_number,:is_billable,:sprint_id,:ticket_type,:project_id,:ticket_description,:ticket_date,:priority,:ticket_status,:due_date,:team_id,:external_ticket_id,:estimated_hours,:tag_id,:start_date,:end_date,:status,:date_created,:created_by,:company_id)";
   		
   		$bind_array=array("ticket_number"=>$ticket_number,"is_billable"=>$is_billable,"sprint_id"=>$sprint_id,"ticket_type"=>$ticket_type,"project_id"=>$project_id,"ticket_description"=>$ticket_description,"ticket_date"=>$ticket_date,"priority"=>$priority,"ticket_status"=>$ticket_status,"due_date"=>$due_date,"team_id"=>$team_id,"external_ticket_id"=>$external_ticket_id,"estimated_hours"=>$estimated_hours,"tag_id"=>$tag_id,"start_date"=>$start_date,"end_date"=>$end_date,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"company_id"=>$company_id);
   		
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
    * Updating Tickets
    * url - /updateTickets
    * method - PUT
    * params -id
    */
   public static function updateTickets($id,$ticket_number,$is_billable,$sprint_id,$ticket_type,$project_id,$ticket_description,$ticket_date,$priority,$ticket_status,$due_date,$team_id,$external_ticket_id,$estimated_hours,$tag_id,$start_date,$end_date,$last_updated,$updated_by,$company_id)
   {
   	try{
   		$query="UPDATE  uni_tickets SET ticket_number=:ticket_number,is_billable=:is_billable,sprint_id=:sprint_id,ticket_type=:ticket_type,project_id=:project_id,ticket_description=:ticket_description,
        ticket_date=:ticket_date,ticket_date=:ticket_date,priority=:priority,ticket_status=:ticket_status,due_date=:due_date,team_id=:team_id,external_ticket_id=:external_ticket_id,tag_id=:tag_id,start_date=:start_date,end_date=:end_date,last_updated=:last_updated,updated_by=:updated_by,company_id=:company_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"ticket_number"=>$ticket_number,"is_billable"=>$is_billable,"sprint_id"=>$sprint_id,"ticket_type"=>$ticket_type,"project_id"=>$project_id,"ticket_description"=>$ticket_description,"ticket_date"=>$ticket_date,"priority"=>$priority,"ticket_status"=>$ticket_status,"due_date"=>$due_date,"team_id"=>$team_id,"external_ticket_id"=>$external_ticket_id,"estimated_hours"=>$estimated_hours,"tag_id"=>$tag_id,"start_date"=>$start_date,"end_date"=>$end_date,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"company_id"=>$company_id));
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
    * Updating Tickets by Making Status Inactive
    * url - /deleteTickets
    * method - PUT
    * params -id
    */
   public static function deleteTickets($id)
   {
   	try{
   		$query="UPDATE  uni_tickets SET status=0 WHERE id=:id";
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
    * Fetching All Tickets
    * url-/getAllTicketList
    * method - GET All
    * params
    */
   public  function getAllTicketList() {
   	$query = "SELECT * FROM uni_tickets";
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
    * Fetching Tickets list based on the id
    * url-/getTicketsListById
    * method - GET by Id
    * params - id
    */
   public function getTicketsListById($id) {
   	$query = "SELECT * FROM uni_tickets where id =:id";
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
    * Fetching  Tickets list based on the company_id
    * url-/getAllTicketsByCId
    * method - GET by company_id
    * params - id
    */
   public function getAllTicketsByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_tickets where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
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
   //***************************************************************Sprint Projects*********************************************************************************************************//
   /**
    * Adding Sprint Projects
    * params-sprint_id,project_id,start_date,end_date,status,date_created,created_by,company_id
    **/
   
   public function addSprintProjects($sprint_id,$project_id,$start_date,$end_date,$status,$date_created,$created_by,$company_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO uni_sprint_projects(sprint_id,project_id,start_date,end_date,status,date_created,created_by,company_id)VALUES(:sprint_id,:project_id,:start_date,:end_date,:status,:date_created,:created_by,:company_id)";
   		
   		$bind_array=array("sprint_id"=>$sprint_id,"project_id"=>$project_id,"start_date"=>$start_date,"end_date"=>$end_date,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"company_id"=>$company_id);
   		
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
 	 * Updating SprintProjects
 	 * url - /updateSprintProjects
 	 * method - PUT
 	 * params -id
  */
   public static function updateSprintProjects($id,$sprint_id,$project_id,$start_date,$end_date,$last_updated,$updated_by,$company_id)
   {
   	try{
   		$query="UPDATE  uni_sprint_projects SET sprint_id=:sprint_id,project_id=:project_id,start_date=:start_date,end_date=:end_date,last_updated=:last_updated,updated_by=:updated_by,company_id=:company_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"sprint_id"=>$sprint_id,"project_id"=>$project_id,"start_date"=>$start_date,"end_date"=>$end_date,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"company_id"=>$company_id));
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
    * Updating SprintProjects by Making Status Inactive
    * url - /deleteSprintProjects
    * method - PUT
    * params -id
    */
   public static function deleteSprintProjects($id)
   {
   	try{
   		$query="UPDATE  uni_sprint_projects SET status=0 WHERE id=:id";
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
    * Fetching All SprintProjects List
    * url - /getAllSprintProjectsList
    * method - GET
    * params
    */
   
   public  function getAllSprintProjectsList() {
   	$query = "SELECT * FROM uni_sprint_projects";
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
    * Fetching SprintProjects List based on the id
    * url-/getSprintProjectsListById
    * method - GET by Id
    * params - id
    */
   public function getSprintProjectsListById($id) {
   	$query = "SELECT * FROM uni_sprint_projects where id =:id";
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
    * Fetching SprintProjects List based on the company_id
    * url-/getAllSprintProjectsByCId
    * method - GET by company_id
    * params - id
    */
   
   public function getAllSprintProjectsByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_sprint_projects where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
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
   
 
   //***************************************************************Permissions*********************************************************************************************************//
   /**
    * Adding permissions
    * url - /addpermissions
    * method - POST
    * params-	page_name,permission,role_id,status,date_created,created_by
    */
   
   public function addpermissions($page_name,$permission,$role_id,$status,$date_created,$created_by)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO permissions(page_name,permission,role_id,status,date_created,created_by)VALUES(:page_name,:permission,:role_id,:status,:date_created,:created_by)";
   		
   		$bind_array=array("page_name"=>$page_name,"permission"=>$permission,"role_id"=>$role_id,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by);
   		
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
    * Updating permissions
    * url - /updatepermissions
    * method - PUT
    * params -id
    */
   public static function updatepermissions($id,$page_name,$permission,$role_id,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  permissions SET page_name=:page_name,permission=:permission,role_id=:role_id,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"page_name"=>$page_name,"permission"=>$permission,"role_id"=>$role_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
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
    * Updating permissions by Making Status Inactive
    * url - /deletepermissions
    * method - PUT
    * params -id
    */
   public static function deletepermissions($id)
   {
   	try{
   		$query="UPDATE  permissions SET status=0 WHERE id=:id";
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
    * Fetching All permissions
    * url-/getAllpermissionsList
    * method - GET All
    * params
    */
   public  function getAllpermissionsList() {
   	$query = "SELECT * FROM permissions";
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
    * Fetching permissions list based on the id
    * url-/getpermissionsListById
    * method - GET by Id
    * params - id
    */
   public function getpermissionsListById($id) {
   	$query = "SELECT * FROM permissions where id =:id";
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
    * Fetching permissions List based on the company_id
    * url-/getAllpermissionsByCId
    * method - GET by company_id
    * params - id
    */
   
   public function getAllpermissionsByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM permissions where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
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
   
   //***************************************************************PermissionMaster*********************************************************************************************************//
   /**
    * Adding permissionmaster
    * url - /addpermissions
    * method - POST
    * params-	permission_name,status,date_created,created_by,start_date,end_date,page_name,permission,role_id,company_id
    */
   
   public function addpermissionmaster($permission_name,$status,$date_created,$created_by,$start_date,$end_date,$page_name,$permission,$role_id,$company_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO  uni_permission_master(permission_name,status,date_created,created_by,start_date,end_date,page_name,permission,role_id,company_id)VALUES(:permission_name,:status,:date_created,:created_by,:start_date,:end_date,:page_name,:permission,:role_id,:company_id)";
   		
   		$bind_array=array("permission_name"=>$permission_name,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date,"page_name"=>$page_name,"permission"=>$permission_name,"role_id"=>$role_id,"company_id"=>$company_id);
   		
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
    * Updating permissionmaster
    * url - /updatepermissionmaster
    * method - PUT
    * params -id
    */
   public static function updatepermissionmaster($id,$permission_name,$last_updated,$updated_by,$start_date,$end_date,$page_name,$permission,$role_id,$company_id)
   {
   	try{
   		$query="UPDATE  uni_permission_master SET permission_name=:permission_name,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date,page_name=:page_name,permission=:permission,role_id=:role_id,company_id=:company_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"permission_name"=>$permission_name,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date,"page_name"=>$page_name,"permission"=>$permission,"role_id"=>$role_id,"company_id"=>$company_id));
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
    * Updating permissionmaster by Making Status Inactive
    * url - /deletepermissionmaster
    * method - PUT
    * params -id
    */
   public static function deletepermissionmaster($id)
   {
   	try{
   		$query="UPDATE  uni_permission_master SET status=0 WHERE id=:id";
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
    * Fetching All permissionmaster
    * url-/getAllpermissionmasterList
    * method - GET All
    * params
    */
   public  function getAllpermissionmasterList() {
   	$query = "SELECT * FROM uni_permission_master";
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
    * Fetching permissionmaster list based on the id
    * url-/getpermissionmasterListById
    * method - GET by Id
    * params - id
    */
   public function getpermissionmasterListById($id) {
   	$query = "SELECT * FROM uni_permission_master where id =:id";
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
    * Fetching permissionmaster List based on the company_id
    * url-/getAllpermissionmasterByCId
    * method - GET by company_id
    * params - id
    */
   
   public function getAllpermissionmasterByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_permission_master where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
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
   
   //***************************************************************Role Permission*********************************************************************************************************//
   /**
    * Adding  rolepermission
    * url - /addrolepermission
    * method - POST
    * params-role_id,permission_id,page_name,status,date_created,created_by,start_date,end_date,company_id
    */
   
   public function addrolepermission($role_id,$permission_id,$page_name,$status,$date_created,$created_by,$start_date,$end_date,$company_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO  uni_role_permission(role_id,permission_id,page_name,status,date_created,created_by,start_date,end_date,company_id)VALUES(:role_id,:permission_id,:page_name,:status,:date_created,:created_by,:start_date,:end_date,:company_id)";
   		
   		$bind_array=array("role_id"=>$role_id,"permission_id"=>$permission_id,"page_name"=>$page_name,"status"=>$status,"date_created"=>$date_created,"created_by"=>$created_by,"start_date"=>$start_date,"end_date"=>$end_date,"company_id"=>$company_id);
   		
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
    * Updating rolepermission
    * url - /updaterolepermission
    * method - PUT
    * params -id
    */
   public static function updaterolepermission($id,$role_id,$page_name,$permission_id,$last_updated,$updated_by,$start_date,$end_date,$company_id)
   {
   	try{
   		$query="UPDATE  uni_role_permission SET role_id=:role_id,page_name=:page_name,permission_id=:permission_id,last_updated=:last_updated,updated_by=:updated_by,start_date=:start_date,end_date=:end_date,company_id=:company_id WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"role_id"=>$role_id,"page_name"=>$page_name,"permission_id"=>$permission_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by,"start_date"=>$start_date,"end_date"=>$end_date,"company_id"=>$company_id));
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
    * Updating rolepermission by Making Status Inactive
    * url - /deleterolepermission
    * method - PUT
    * params -id
    */
   public static function deleterolepermission($id)
   {
   	try{
   		$query="UPDATE  uni_role_permission SET status=0 WHERE id=:id";
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
    * Fetching All rolepermission
    * url-/getAllRolePermissionList
    * method - GET All
    * params
    */
   public  function getAllRolePermissionList() {
   	$query = "SELECT * FROM uni_role_permission";
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
    * Fetching rolepermission list based on the id
    * url-/getRolePermissionListById
    * method - GET by Id
    * params - id
    */
   public function getRolePermissionListById($id) {
   	$query = "SELECT * FROM uni_role_permission where id =:id";
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
    * Fetching rolepermission List based on the company_id
    * url-/getAllRolePermissionByCId
    * method - GET by company_id
    * params - id
    */
   
   public function getAllRolePermissionByCId($company_id,$cDate) {
   	
   	
   	$query = "SELECT * FROM uni_role_permission where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
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
   
   //***************************************************************Sprint History************************************************************************************************************************************************************************************//
   /**
    * Fetching Sprint History based on the team_id,Sprint,Project
    * url-/getAllSprintHistory
    * method - GET by team_id,sprint_name,created_by
    * params - id
    */
   
   
   public function getAllSprintHistory($team_id,$company_id,$cDate) {
   	
   	$query=	"select sp.sprint_name,pm.project_name,t.id team_id,tm.isteamlead,tm.user_id,sp.created_by,t.teamlead,t.team_name,tk.ticket_id,tk.id task_id,sp.start_date,sp.end_date,tk.sprint_id,tk.task,tk.task_description,tk.task_date,tk.is_billable,tk.task_type,tk.priority,tk.task_status,tk.tag_id,tkt.ticket_number,tkt.sprint_id ticketsprint_id,tkt.ticket_type,tkt.project_id,tkt.ticket_date,tkt.ticket_status,tkt.start_date ticketstart_date,tkt.created_by ticketcreated_by
   	from uni_sprint_plan sp
   	left join uni_sprint_projects sproj on sp.id=sproj.sprint_id
   	join uni_project_master pm on pm.id=sproj.project_id
   	join uni_team t on pm.team_id=t.id
   	join uni_team_members tm on pm.id=tm.project_id
   	join uni_tasks tk on sp.id=tk.sprint_id
   	join uni_tickets tkt on tkt.id=tk.ticket_id";
   	
   	//$query = "SELECT * FROM uni_role_permission where start_date <= '$cDate' AND end_date >= '$cDate' AND company_id ='$company_id' AND status=1";
   	$rslt = self::fetchQuery($query,array("team_id"=>$team_id,"company_id"=>$company_id,"cDate"=>$cDate));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   	
   }
   //***************************************************************Current Sprint************************************************************************************************************************************************************************************//
   /**
    * Fetching Current Sprint by comparing current_date with start_date and end_date of uni_sprint_plan
    * url-/getAllCurrentSprint
    * method - GET by team_id,company_id,created_by
    * params - id
    */
   public function  getAllCurrentSprint($team_id,$company_id,$cDate) {
   	
   	
 $query = "select sp.sprint_name,pm.project_name,t.id team_id,tm.isteamlead,tm.user_id,sp.created_by,t.teamlead,t.team_name,tk.ticket_id,tk.id task_id,sp.start_date,sp.end_date,tk.sprint_id,tk.task,tk.task_description,tk.task_date,tk.is_billable,tk.task_type,tk.priority,tk.task_status,tk.tag_id,tkt.ticket_number,tkt.sprint_id ticketsprint_id,tkt.ticket_type,tkt.project_id,tkt.ticket_date,tkt.ticket_status,tkt.start_date ticketstart_date,tkt.created_by ticketcreated_by
from uni_sprint_plan sp
left join uni_sprint_projects sproj on sp.id=sproj.sprint_id
join uni_project_master pm on pm.id=sproj.project_id
 join uni_team t on pm.team_id=t.id
 join uni_team_members tm on pm.id=tm.project_id
join uni_tasks tk on sp.id=tk.sprint_id
join uni_tickets tkt on tkt.id=tk.ticket_id

WHERE sp.end_date >= curdate() and sp.start_date < curdate()";
   	$rslt = self::fetchQuery($query,array($team_id,$company_id,$cDate));
   	if (sizeof($rslt))
   	{
   		return $rslt;
   	}
   	else
   	{
   		return NULL;
   	}
   	
   }
   
   //***************************************************************TasksToResources*********************************************************************************************************//
   /**
    * Adding TasksToResources
    * url - /addTasksToResources
    * method - POST
    * params-ticket_id,sprint_id,status,task,task_description,due_date,priority,task_status,team_member_id,notes,company_id,is_billable,task_type,start_date,end_date,tag_id,project_id
    */
   
   public function addTasksToResources($ticket_id,$sprint_id,$status,$task,$task_description,$due_date,$priority,$date_created,$task_status,$team_member_id,$notes,$company_id,$is_billable,$task_type,$tag_id,$start_date,$end_date,$project_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO  uni_tasks(ticket_id,sprint_id,status,task,task_description,due_date,priority,date_created,task_status,team_member_id,notes,company_id,is_billable,task_type,start_date,end_date,tag_id,project_id)VALUES(:ticket_id,:sprint_id,:status,:task,:task_description,:due_date,:priority,:date_created,:task_status,:team_member_id,:notes,:company_id,:is_billable,:task_type,:start_date,:end_date,:tag_id,:project_id)";
   		
   		$bind_array=array("ticket_id"=>$ticket_id,"sprint_id"=>$sprint_id,"status"=>$status,"task"=>$task,"task_description"=>$task_description,"due_date"=>$due_date,"priority"=>$priority,"date_created"=>$date_created,"task_status"=>$task_status,"team_member_id"=>$team_member_id,"notes"=>$notes,"company_id"=>$company_id,"is_billable"=>$is_billable,"task_type"=>$task_type,"start_date"=>$start_date,"end_date"=>$end_date,"tag_id"=>$tag_id,"project_id"=>$project_id);
   		
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
    * Updating TasksToResources
    * url - /updateTasksToResources
    * method - PUT
    * params -id
    */
   
   public static function updateTasksToResources($id,$ticket_id,$sprint_id,$task,$task_description,$due_date,$priority,$task_status,$team_member_id,$notes,$company_id,$is_billable,$task_type,$tag_id,$start_date,$end_date,$project_id,$last_updated,$updated_by)
   {
   	try{
   		$query="UPDATE  uni_tasks SET ticket_id=:ticket_id,sprint_id=:sprint_id,task=:task,task_description=:task_description,due_date=:due_date,priority=:priority,task_status=:task_status,team_member_id=:team_member_id,notes=:notes,company_id=:company_id,is_billable=:is_billable,task_type=:task_type,tag_id=:tag_id,start_date=:start_date,end_date=:end_date,project_id=:project_id,last_updated=:last_updated,updated_by=:updated_by WHERE id=:id";
   		
   		$rslt= self::updateQuery($query,array("id"=>$id,"ticket_id"=>$ticket_id,"sprint_id"=>$sprint_id,"task"=>$task,"task_description"=>$task_description,"due_date"=>$due_date,"priority"=>$priority,"task_status"=>$task_status,"team_member_id"=>$team_member_id,"notes"=>$notes,"company_id"=>$company_id,"is_billable"=>$is_billable,"task_type"=>$task_type,"tag_id"=>$tag_id,"start_date"=>$start_date,"end_date"=>$end_date,"project_id"=>$project_id,"last_updated"=>$last_updated,"updated_by"=>$updated_by));
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
 //**********************************************************************************************************TicketsToResources****************************************************************************************************************************************//
   /**
    * Adding TicketsToResources
    * url - /addTicketsToResources
    * method - POST
    * params-is_billable,sprint_id,ticket_type,project_id,ticket_number,ticket_description,due_date,priority,ticket_status,start_date,end_date,team_id,tag_id,company_id    notes,user_id,ticket_name
    * 
    */
   public function addTicketsToResources($is_billable,$sprint_id,$status,$ticket_type,$project_id,$ticket_number,$ticket_description,$due_date,$priority,$ticket_status,$start_date,$end_date,$team_id,$tag_id,$company_id)
   {
   	$response = array();
   	
   	try
   	{
   		$query="INSERT INTO  uni_tickets(is_billable,sprint_id,status,ticket_type,project_id,ticket_number,ticket_description,due_date,priority,ticket_status,start_date,end_date,team_id,tag_id,company_id)VALUES(:is_billable,:sprint_id,:status,:ticket_type,:project_id,:ticket_number,:ticket_description,:due_date,:priority,:ticket_status,:start_date,:end_date,:team_id,:tag_id,:company_id)";
   		
   		$bind_array=array("is_billable"=>$is_billable,"sprint_id"=>$sprint_id,"status"=>$status,"ticket_type"=>$ticket_type,"project_id"=>$project_id,"ticket_number"=>$ticket_number,"ticket_description"=>$ticket_description,"due_date"=>$due_date,"priority"=>$priority,"ticket_status"=>$ticket_status,"start_date"=>$start_date,"end_date"=>$end_date,"team_id"=>$team_id,"tag_id"=>$tag_id,"company_id"=>$company_id);
   		
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

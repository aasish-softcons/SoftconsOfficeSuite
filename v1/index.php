<?php

require_once '../include/DataDAO.php';
require_once '../include/PassHash.php';
require_once '../commons/Utils.php';
require '.././libs/Slim/Slim.php';
require_once '../include/sendmail.php';

header('Content-Type: application/json ');

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;


/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DataDAO();

        echo "hi......................................";
        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user_id = $db->getUserId($api_key);
        }
    } else {
        // api key is missing in headerget
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}


/**
     * Generating random Unique MD5 String for user Api key
     */
     function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
   /**
 * ----------- METHODS WITHOUT AUTHENTICATION ---------------------------------*/ 

    
/**
 * Validating email address
 */
function validateEmail($email_id) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}


/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
   // exit();
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}
/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}





//***************************************************    Rest API     **********************************************************************************
/**
 * User Login
 * url - /login
 * method - POST
 * params - email_id, password
 */
$app->post('/login', function() use ($app) {
	
	
	$db = new DataDAO();
	$userDetails = json_decode(file_get_contents("php://input"));
	$users = $userDetails->LoginInfo;
	$loginData = json_decode($users);
	$email_id = $loginData->EmailID;
	$password = $loginData->Password;
	$salt ="*ISoftcons_Office_Suite*";
	$pwd = $db->simple_encrypt($password, $salt);
	$status = 1;
	$response = array();
	$res = $db->checkLogin($email_id, $pwd, $status);
	if ($res == 'success') {
		$user = $db->getUserByUserId($email_id);
		$response['error'] = false;
		$response['message'] = 'Valid Credentials';
		$response['value'] = $user;
	} else {
		$response['error'] = true;
		$response['message'] = 'Invalid Credentials';
	}
	echoRespnse(201, $response);
});
	//***************************************************************Timesheet_config********************************************************************************************************//
	/**
	 * Adding timesheet_config
	 * url - /addTimesheetConfig
	 * method - POST
	 * params-company_id,timesheet_frequency,status,date_created,created_by,start_date,end_date
	 */
	$app->post('/addTimesheetConfig', function() use ($app) {
		$response = array();
		$db = new DataDAO();
		$timesheetDetails=json_decode(file_get_contents("php://input"));
		$timesheet=$timesheetDetails->TimesheetInfo;
		$timesheetData=json_decode($timesheet);
		$company_id = $timesheetData->company_id;
		$timesheet_frequency= $timesheetData->timesheet_frequency;
		$status = 1;
		$date_created= $timesheetData->date_created;
		$created_by= $timesheetData->created_by;
		$start_date = $timesheetData->start_date;
		$end_date = $timesheetData->end_date;
		
		
		$res = $db->addTimesheetConfig($company_id,$timesheet_frequency,$status,$date_created,$created_by,$start_date,$end_date);
		if ($res)
		{
			$response["error"] = false;
			$response["message"] = "timesheet_config created successfully";
		} else
		{
			$response["error"] = true;
			$response["message"] = "Oops! An error occurred while creating timesheet_config";
		}
		echoRespnse(201, $response);
	});
		
		/**
		 * Updating timesheet_config
		 * url - /updateTimesheetConfig
		 * method - PUT
		 * params -id
		 */
		$app->post('/updateTimesheetConfig', function() use ($app) {
			$response = array();
			$db = new DataDAO();
			$timesheetDetails=json_decode(file_get_contents("php://input"));
			$timesheet=$timesheetDetails->TimeSheetInfo;
			$timesheetData=json_decode($timesheet);
			$id = $timesheetData->id;
			$company_id=$timesheetData->company_id;
			$timesheet_frequency=$timesheetData->timesheet_frequency;
			$last_updated=$timesheetData->last_updated;
			$updated_by= $timesheetData->updated_by;
			$start_date = $timesheetData->start_date;
			$end_date = $timesheetData->end_date;
			$result = $db->updateTimesheetConfig($id,$company_id,$timesheet_frequency,$last_updated,$updated_by,$start_date,$end_date);
			if ($result)
			{
				$response["error"] = false;
				$response["message"] = "You are successfully updated the Timesheet_config";
			} else
			{
				$response["error"] = true;
				$response["message"] = "Oops! An error occurred while updating Timesheet_config";
			}
			echoRespnse(201, $id);
		});
		
/**
	* Updating Timesheet_config by Making Status Inactive
	* url - /deleteTimesheetConfig
	* method - PUT
	* params -id
*/
			$app->post('/deleteTimesheetConfig',function() use($app) {
				$db = new DataDAO();
				$response = array();
				$timesheetDetails=json_decode(file_get_contents("php://input"));
				$timesheet=$timesheetDetails->TimeSheetInfo;
				$timesheetData=json_decode($timesheet);
				$id = $timesheetData->id;
				$result = $db->deleteTimesheetConfig($id);
				
				if ($result)
				{
					$response["error"] = false;
					$response["message"] = "You are successfully made Inactive";
				} else
				{
					$response["error"] = true;
					$response["message"] = "Oops! An error occurred while making status Inactive";
				}
				echoRespnse(201, $response);
			});
				
			
	/**
	 * Fetching All Timesheet_config List
	 * url-/getAllTimesheetConfigList
	 * method - GET All
	 * params 
	 */
	
	$app->get('/getAllTimesheetConfigList/',function() use($app){
		$db = new DataDAO();
		$res = $db->getAllTimesheetConfigList();
	    if(sizeof($res))
	    {
	     echoRespnse(201, $res);
		}
		else{
			echoRespnse(201, $res);
		}
	  }); 
					
	
/**
	 * Fetching Timesheet_config List based on the id
	 * url-/getTimesheetConfigListById
	 * method - GET by Id
	 * params - id
	 */
	$app->get('/getTimesheetConfigListById/:id',function($id) use($app){
		$db = new DataDAO();
		$res = $db->getTimesheetConfigListById($id);
		if(sizeof($res))
		{
		 echoRespnse(201, $res);
		}
		});
			

//**************************************************Company************************************************************************/

	/**
	 * Fetching All Company List
	 * url-/getAllCompanyList
	 * method - GET All
	 * params 
	 */
	
	$app->get('/getAllCompanyList/',function() use($app){
		$db = new DataDAO();
		$res = $db->getAllCompanyList();
	    if(sizeof($res))
	    {
	     echoRespnse(201, $res);
		}	
	  }); 

	
	/**
	 * Fetching Company List based on the id
	 * url-/getCompanyListById
	 * method - GET by Id
	 * params - id
	 */
	$app->get('/getCompanyListById/:id',function($id) use($app){
		$db = new DataDAO();
		$res = $db->getCompanyListById($id);
		if(sizeof($res))
		{
		 echoRespnse(201, $res);
		}
		}); 

	/** 
	 * Adding Company
     * url - /addCompany
     * method - POST
     * params - id,company_name,contact_person, address,phone_no,email_id,subscription_date,expiry_date,asset_limit,user_limit
     * params - licence_version,licence_num,num_of_licence,	version,amount_paid,status,date_created,last_updated,nominal_flag
     * params - created_by,updated_by,sprint_days
    */   
	/*$app->post('/addCompany', function() use ($app) {
		$db = new DataDAO();
		$response = array();
		$companydetails=json_decode(file_get_contents("php://input"));
		$company=$companydetails->CompanyInfo;
		$companyData=json_decode($company);
		
		$company_name = $companyData->company_name;
		$contact_person=$companyData->contact_person;
		$address = $companyData->address;
		$phone_no =$companyData->phone_no;
		$email_id = $companyData->email_id;
		$subscription_date = $companyData->subscription_date;
		$subscription_date = $companyData->subscription_date;
		$expiry_date =$companyData->expiry_date;
		$asset_limit = $companyData->asset_limit;
		$user_limit = $companyData->user_limit;
		$licence_version = $companyData->licence_version;
		$licence_num = $companyData->licence_num;
		$num_of_licence = $companyData->num_of_licence;
		$version = $companyData->version;
		$amount_paid = $companyData->amount_paid;
		$status = $companyData->status;
		$date_created = $companyData->date_created;
		$last_updated = $companyData->last_updated;
		$nominal_flag = $companyData->nominal_flag;
		$created_by = $companyData->created_by;
		$updated_by = $companyData->updated_by;
		$sprint_days = $companyData->sprint_days;
		
		
		$companyExists = $db -> companyExists($company_name);
		
		if(!$companyExists){
			$res = $db->addCompany($company_name,$contact_person,$address,$phone_no,$email_id,$asset_limit,$subscription_date,
					$expiry_date,$asset_limit,$user_limit,$licence_num,$num_of_licence,$licence_version,$version,$amount_paid,
					$status,$date_created,$last_updated,$nominal_flag,$created_by,$updated_by,$sprint_days);
			if ($res !=0)
			{
				$response["error"] = false;
				$response["message"] = "You are Successfully added a Company";
			} else if ($res == COMPANY_CREATE_FAILED) {
				$response["error"] = true;
				$response["message"] = "Oops! An error occurred while adding";
			}
		}
		else{
			$response["error"] = true;
			$response["message"] = "Sorry, this company already existed";
		}
		
			echoRespnse(201, $response);
			
			
		}); */
	/** 
	 * Updating  Company
     * url - /updateCompany
     * method - POST
     * params - id,contact_person, address,phone_no,email_id,subscription_date,last_updated,updated_by,sprint_days
    */
	
		$app->post('/updateCompany', function() use ($app) {
			$db = new DataDAO();
			$response = array();
			$companydetails=json_decode(file_get_contents("php://input"));
			$company=$companydetails->CompanyInfo;
			$companyData=json_decode($company);
			$id = $companyData->id;
			$company_name = $companyData->company_name;
			$contact_person = $companyData->contact_person;
			$address = $companyData->address;
			$phone_no = $companyData->phone_no;
			$email_id = $companyData->email_id;
			$subscription_date = $companyData->subscription_date;
			//$expiry_date =$companyData->expiry_date;
			//$asset_limit = $companyData->asset_limit;
			//$user_limit = $companyData->user_limit;
			//$licence_version = $companyData->licence_version;
			//$licence_num = $companyData->licence_num;
			//$num_of_licence = $companyData->num_of_licence;
			//$version = $companyData->version;
			//$amount_paid = $companyData->amount_paid;
			//$date_created = $companyData->date_created;
			//$last_updated = $companyData->last_updated;
			$last_updated = $db->ISTConversion();
			$nominal_flag = $companyData->nominal_flag;
			//$created_by = $companyData->created_by;
			$updated_by = $companyData->updated_by;
			$sprint_days = $companyData->sprint_days;
			
			
			$companyExists = $db -> companyExists($company_name);
			
				/*$res = $db->updateCompany($company_name,$contact_person,$address,$phone_no,$email_id,$asset_limit,$subscription_date,
						$expiry_date,$asset_limit,$user_limit,$licence_num,$num_of_licence,$licence_version,$version,$amount_paid,
						$status,$date_created,$last_updated,$nominal_flag,$created_by,$updated_by,$sprint_days);*/
				$res = $db->updateCompany($id,$contact_person,$address,$phone_no,$email_id,$subscription_date,$last_updated,$nominal_flag,$updated_by,$sprint_days);						
				if ($res)
				{
					$response["error"] = false;
					$response["message"] = "You are Successfully Updated a Company";
				} else {
					$response["error"] = true;
					$response["message"] = "Oops! An error occurred while Updating";
				}
	       echoRespnse(201, $response);
	 });
			

    


//***************************************************************Department********************************************************************************************************//
	/**
	 * Adding Department
	 * url - /addDepartment
	 * method - POST
	 * params-company_id,department_name,department_head,department_location,department_function,department_members,status,date_created,created_by,start_date,end_date
	 */
		$app->post('/addDepartment', function() use ($app) {
			$response = array();
			$db = new DataDAO();
			$DepartmentDetails=json_decode(file_get_contents("php://input"));
			$Departments=$DepartmentDetails->DepartmentInfo;
			$DepartmentData=json_decode($Departments);
			$company_id = $DepartmentData->company_id;
			$department_name = $DepartmentData->department_name;
			$department_head = $DepartmentData->department_head;
			$department_location = $DepartmentData->department_location;
			$department_function = $DepartmentData->department_function;
			$department_members = $DepartmentData->department_members;
			$status = 1;
			//$date_created= $DepartmentData->date_created;
			$created_by= $DepartmentData->created_by;
			$start_date= $DepartmentData->start_date;
			$end_date= $DepartmentData->end_date;
			$date_created = $db->ISTConversion();
			$res = $db->addDepartment($company_id,$department_name,$department_head,$department_location,$department_function,$department_members,$status,$date_created,$created_by,$start_date,$end_date);
			if ($res)
			{
				$response["error"] = false;
				$response["message"] = "Department created successfully";
			} else
			{
				$response["error"] = true;
				$response["message"] = "Oops! An error occurred while creating Department";
			}
			echoRespnse(201, $response);
		});
			
			
		/**
			 * Updating Department
			 * url - /updateDepartment
			 * method - PUT
			 * params -id
		*/
			$app->post('/updateDepartment',function() use($app) {
				$db = new DataDAO();
				$response = array();
				$DepartmentDetails=json_decode(file_get_contents("php://input"));
				$Departments=$DepartmentDetails->DepartmentInfo;
				$DepartmentData=json_decode($Departments);
				$id = $DepartmentData->id;
				$company_id=$DepartmentData->company_id;
				$department_name=$DepartmentData->department_name;
				$department_head = $DepartmentData->department_head;
				$department_location = $DepartmentData->department_location;
				$department_function = $DepartmentData->department_function;
				$department_members = $DepartmentData->department_members;				
				//$last_updated=$DepartmentData->last_updated;
				$last_updated = $db->ISTConversion();
				$updated_by= $DepartmentData->updated_by;
				$start_date= $DepartmentData->start_date;
				$end_date= $DepartmentData->end_date;
				$result = $db->updateDepartment($id,$company_id,$department_name,$department_head,$department_location,$department_function,$department_members,$last_updated,$updated_by,$start_date,$end_date);
				if ($result)
				{
					$response["error"] = false;
					$response["message"] = "You are successfully updated the Department";
				} else
				{
					$response["error"] = true;
					$response["message"] = "Oops! An error occurred while updating Department";
				}
				echoRespnse(201, $response);
			});
			
/**
	 * Updating Department by Making Status Inactive
	 * url - /deleteDepartment
	 * method - PUT
	 * params -id
 */
				$app->post('/deleteDepartment',function() use($app) {
					$db = new DataDAO();
					$response = array();
					$DepartmentDetails=json_decode(file_get_contents("php://input"));
					$Departments=$DepartmentDetails->DepartmentInfo;
					$DepartmentData=json_decode($Departments);
					$id = $DepartmentData->id;
					$result = $db->deleteDepartment($id);
					
					if ($result)
					{
						$response["error"] = false;
						$response["message"] = "You are successfully made Inactive";
					} else
					{
						$response["error"] = true;
						$response["message"] = "Oops! An error occurred while making status Inactive";
					}
					echoRespnse(201, $response);
				});

/**
	 * Fetching All Department List
	 * url - /getAllDepartmentList
	 * method - GET
	 * params
*/
	$app->get('/getAllDepartmentList/',function() use($app){
	$db = new DataDAO();
	$res = $db->getAllDepartmentList();
	if(sizeof($res))
		{
		echoRespnse(201, $res);
		}
	});
						
/**
	* Fetching Department List based on the id
	* url-/getDepartmentListById
	* method - GET by Id
	* params - id
 */
	$app->get('/getDepartmentListById/:id',function($id) use($app){
			$db = new DataDAO();
			$res = $db->getDepartmentListById($id);
			if(sizeof($res))
			{
			echoRespnse(201, $res);
			}
		});
	
	
/**
		 * Fetching Department List based on the company_id
		 * url-/getAllDepartmentByCId
		 * method - GET by company_id
		 * params - id
 */
		$app->get('/getAllDepartmentByCId/:company_id',function($company_id) use($app){
			$db = new DataDAO();
			$cDate = $db->ISTConversion();
			$res = $db->getAllDepartmentByCId($company_id,$cDate);
			if(sizeof($res))
			{
				echoRespnse(201, $res);
			}
			;
		});
			


							
//***************************************************************Users********************************************************************************************************//
	/**
	 * Adding User
	 * url - /addUser
	 * method - POST
	 * params -first_name,last_name,company_id,email_id,password,user_address,phone_no,role_id,department_id,status,date_created,created_by,password_activation,password_link_timestamp,version,position,start_date,end_date
	 */
		$app->post('/addUser', function() use ($app) {
			$response = array();
			$db = new DataDAO();
			$userDetails=json_decode(file_get_contents("php://input"));
			$users=$userDetails->UserInfo;
			$userData=json_decode($users);
			$first_name = $userData->first_name;
			$last_name = $userData->last_name;
			$company_id=$userData->company_id;
			$email_id=$userData->email_id;
			$password=$userData->password;
			$user_address=$userData->user_address;
			$phone_no=$userData->phone_no;
			$role_id=$userData->role_id;
			//$department_id=$userData->department_id;
			$status = 1;
			//$date_created= $userData->date_created;
			$date_created = $db->ISTConversion();
			$created_by= $userData->created_by;
			//$password_activation=1;
			//$password_link_timestamp=$userData->password_link_timestamp;
			//$version=$userData->version;
			$department_id= $userData->department_id;
			$position= $userData->position;
			$start_date= $userData->start_date;
			$end_date= $userData->end_date;
			$userRes = $db->userExists($email_id);
			if(!$userRes){
				$res = $db->addUser($first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$status,$date_created,$created_by,$department_id,$position,$start_date,$end_date);
				if ($res)
				{
					$response["error"] = false;
					$response["message"] = "User created successfully";
				} else
				{
					$response["error"] = true;
					$response["message"] = "Oops! An error occurred while creating User";
				}				
			}
			else{
				$response["error"] = true;
				$response["message"] = "Oops! An error occurred while creating User";
			}
			echoRespnse(201, $response);
			
	  });
		
	/**
	 * Updating User
	 * url - /updateUser
	 * method - PUT
	 * params -id
	*/
			$app->post('/updateUser',function() use($app) {
				$db = new DataDAO();
				$response = array();
				$userDetails=json_decode(file_get_contents("php://input"));
				$users=$userDetails->UserInfo;
				$userData=json_decode($users);
				$id = $userData->id;
				$first_name = $userData->first_name;
				$last_name = $userData->last_name;
				$company_id=$userData->company_id;
				$email_id=$userData->email_id;
				$password=$userData->password;
				$user_address=$userData->user_address;
				$phone_no=$userData->phone_no;
				$role_id=$userData->role_id;
				//$last_updated= $userData->last_updated;
				$last_updated = $db->ISTConversion();
				$updated_by= $userData->updated_by;
				$department_id= $userData->department_id;
				$position= $userData->position;
				$start_date= $userData->start_date;
				$end_date= $userData->end_date;
				$result = $db->updateUser($id,$first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$last_updated,$updated_by,$department_id,$position,$start_date,$end_date);
			    if ($result)
				{
					$response["error"] = false;
					$response["message"] = "You are successfully updated";
				} else
				{
					$response["error"] = true;
					$response["message"] = "Oops! An error occurred while updating";
				}
				echoRespnse(201, $response);
			});
				
/**
	 * Updating User by Making Status Inactive
	 * url - /deleteUser
	 * method - PUT
	 * params -id
*/
				$app->post('/deleteUser',function() use($app) {
					$db = new DataDAO();
					$response = array();
					$userDetails=json_decode(file_get_contents("php://input"));
					$users=$userDetails->UserInfo;
					$userData=json_decode($users);
					$id = $userData->id;
					$result = $db->deleteUser($id);
					
					if ($result)
					{
						$response["error"] = false;
						$response["message"] = "You are successfully updated";
					} else
					{
						$response["error"] = true;
						$response["message"] = "Oops! An error occurred while updating";
					}
					echoRespnse(201, $response);
				});
					
/**
 * Fetching All Users List
 * url - /getAllUsersList
 * method - GET
 * params 
 */
     $app->get('/getAllUsersList/',function() use($app){
			$db = new DataDAO();
			$res = $db->getAllUsersList();
			if(sizeof($res))
			{
				echoRespnse(201, $res);
			}
		});
			
/**
	* Fetching Users List based on the id
	* url-/getUserListById
	* method - GET by Id
	* params - id
*/
			$app->get('/getUserListById/:id',function($id) use($app){
				$db = new DataDAO();
				$res = $db->getUserListById($id);
				if(sizeof($res))
				{
					echoRespnse(201, $res);
				}
			});
				
			
/**
	* Fetching User List based on the company_id
    * url-/getAllUserByCId
	* method - GET by company_id
	* params - id
 */
				$app->get('/getAllUserByCId/:company_id',function($company_id) use($app){
					$db = new DataDAO();
					$cDate = $db->ISTConversion();
					$res = $db->getAllUserByCId($company_id,$cDate);
					if(sizeof($res))
					{
						echoRespnse(201, $res);
					}
			
				});
					
			
//***************************************************************Role*************************************************//
/** 
	 * Adding Role
     * url - /addRole
     * method - POST
     * params -role_name,role_desc,status,date_created,created_by,start_date,end_date
 */
$app->post('/addRole', function() use ($app) {
$response = array();
$db = new DataDAO();
$roleDetails=json_decode(file_get_contents("php://input"));
$roles=$roleDetails->RoleInfo;
$roleData=json_decode($roles);
$role_name = $roleData->role_name;
$role_desc = $roleData->role_desc;
$status = 1;
//$date_created= $roleData->date_created;
$date_created = $db->ISTConversion();
$created_by= $roleData->created_by;
$start_date= $roleData->start_date;
$end_date= $roleData->end_date;
$res = $db->addRole($role_name,$role_desc,$status,$date_created,$created_by,$start_date,$end_date);
if ($res) 
{
  $response["error"] = false;
  $response["message"] = "Role created successfully";
} else 
{
   $response["error"] = true;
   $response["message"] = "Oops! An error occurred while creating ROLE";
}
echoRespnse(201, $response);
}); 


/** 
	 * Updating Role
     * url - /updateRole
     * method - PUT
     * params -id,role_name,role_desc,last_updated,updated_by,start_date,end_date
 */
	$app->post('/updateRole',  function() use($app) {
	$db = new DataDAO();
	$response = array();
	$roleDetails=json_decode(file_get_contents("php://input"));
	$roles=$roleDetails->RoleInfo;
	$roleData=json_decode($roles);
	$id = $roleData->id;
	$role_name = $roleData->role_name;
	$role_desc = $roleData->role_desc;
	//$last_updated= $roleData->last_updated;
	$last_updated = $db->ISTConversion();
	$updated_by= $roleData->updated_by;
	$start_date= $roleData->start_date;
	$end_date= $roleData->end_date;
	$result = $db->updateRole($id,$role_name,$role_desc,$last_updated,$updated_by,$start_date,$end_date);
	
	if ($result) 
	{
	 $response["error"] = false;
	 $response["message"] = "You are successfully updated";
	} else 
	{
	 $response["error"] = true;
	 $response["message"] = "Oops! An error occurred while updating";
	 }
	 echoRespnse(201, $response);
	});
	
						
/**
* Updating Roles by Making Status Inactive
* url - /deleteRole
* method - PUT
* params -id
*/
$app->post('/deleteRole',  function() use($app) {
	$db = new DataDAO();
	$response = array();
	$roleDetails=json_decode(file_get_contents("php://input"));
	$roles=$roleDetails->RoleInfo;
	$roleData=json_decode($roles);
	$id = $roleData->id;
	$result = $db->deleteRole($id);
	
	if ($result)
	{
		$response["error"] = false;
		$response["message"] = "You are successfully updated";
	} else
	{
		$response["error"] = true;
		$response["message"] = "Oops! An error occurred while updating";
	}
	echoRespnse(201, $response);
});

		
		
/**
	* Fetching All Roles 
	* url-/getAllRoleList
	* method - GET All
	* params
*/
	$app->get('/getAllRoleList/',function() use($app){
			$db = new DataDAO();
			$res = $db->getAllRoleList();
			if(sizeof($res))
			{
				echoRespnse(201, $res);
			}
		});
			
/**
	* Fetching Roles List based on the id
	* url-/getUserListById
	* method - GET by Id
	* params - id
*/
			$app->get('/getRoleListById/:id',function($id) use($app){
				$db = new DataDAO();
				$res = $db->getRoleListById($id);
				if(sizeof($res))
				{
					echoRespnse(201, $res);
				}
			});
			
/**
	* Fetching Roles List based on the company_id
	* url-/getAllRoleByCId
	* method - GET by company_id
	* params - id
 */
				$app->get('/getAllRoleByCId/:company_id',function($company_id) use($app){
					$db = new DataDAO();
					$cDate = $db->ISTConversion();
					$res = $db->getAllRoleByCId($company_id,$cDate);
					if(sizeof($res))
					{
						echoRespnse(201, $res);
					}
					
				});
					
//***************************************************************Clients******************************************************************************//
/**
 * Adding Clients
 * url - /addClients
 * method - POST
 * params -client_name,website_url,pan,gstn,registered_address,mailing_address,managing_director,contact_person,phone_number,email_id,company_id,date_created,created_by,status,start_date,end_date
*/
				$app->post('/addClients', function() use ($app) {
					$response = array();
					$db = new DataDAO();
					$clientDetails=json_decode(file_get_contents("php://input"));
					$clients=$clientDetails->ClientInfo;
					$clientData=json_decode($clients);
					//$id = $roleData->id;
					$client_name = $clientData->client_name;
					$website_url = $clientData->website_url;
					$pan = $clientData->pan;
					$gstn = $clientData->gstn;
					$registered_address = $clientData->registered_address;
					$mailing_address = $clientData->mailing_address;
					$managing_director = $clientData->managing_director;
					$contact_person = $clientData->contact_person;
					$phone_number = $clientData->phone_number;
					$email_id = $clientData->email_id;
					$company_id = $clientData->company_id;
					//$date_created= $clientData->date_created;
					$date_created = $db->ISTConversion();
					$created_by= $clientData->created_by;
					$status = 1;
					$start_date= $clientData->start_date;
					$end_date= $clientData->end_date;
					$res = $db->addClients($client_name,$company_id,$website_url,$pan,$gstn,$registered_address,$managing_director,$mailing_address,$contact_person,$phone_number,$email_id,$status,$date_created,$created_by,$start_date,$end_date);
					if ($res)
					{
						$response["error"] = false;
						$response["message"] = "Clients are added Successfully";
					} else
					{
						$response["error"] = true;
						$response["message"] = "Oops! An error occurred while creating Clients";
					}
					echoRespnse(201, $response);
		});
					
					
/**
	* Updating Clients
	* url - /updateClients
	* method - PUT
	* params -id
*/
					$app->post('/updateClients',  function() use($app) {
						$db = new DataDAO();
						$response = array();
						$clientDetails=json_decode(file_get_contents("php://input"));
						$clients=$clientDetails->ClientInfo;
						$clientData=json_decode($clients);
						$id = $clientData->id;
						$client_name = $clientData->client_name;
						$website_url = $clientData->website_url;
						$pan = $clientData->pan;
						$gstn = $clientData->gstn;
						$registered_address = $clientData->registered_address;
						$mailing_address = $clientData->mailing_address;
						$managing_director = $clientData->managing_director;
						$contact_person = $clientData->contact_person;
						$phone_number = $clientData->phone_number;
						$email_id = $clientData->email_id;						
						$company_id = $clientData->company_id;
						//$last_updated= $clientData->last_updated;
						$last_updated = $db->ISTConversion();
						$updated_by=$clientData->updated_by;
						$start_date= $clientData->start_date;
						$end_date= $clientData->end_date;
						$start_date= $clientData->start_date;
						$end_date= $clientData->end_date;
						//$status = 1;
						$res = $db->updateClients($id,$client_name,$company_id,$website_url,$pan,$gstn,$registered_address,$managing_director,$mailing_address,$contact_person,$phone_number,$email_id,$last_updated,$updated_by,$start_date,$end_date);
						if ($res)
						{
							$response["error"] = false;
							$response["message"] = "You are successfully updated";
						} else
						{
							$response["error"] = true;
							$response["message"] = "Oops! An error occurred while updating";
						}
						echoRespnse(201, $response);
					});
						
/**
* Updating Clients by Making Status Inactive
* url - /deleteClients
* method - PUT
* params -id
*/
						$app->post('/deleteClient', function() use($app) {
							$db = new DataDAO();
							$response = array();
							$clientDetails=json_decode(file_get_contents("php://input"));
							$clients=$clientDetails->ClientInfo;
							$clientData=json_decode($clients);
							$id = $clientData->id;
							$result = $db->deleteClient($id);
							
							if ($result)
							{
								$response["error"] = false;
								$response["message"] = "You are successfully updated";
							} else
							{
								$response["error"] = true;
								$response["message"] = "Oops! An error occurred while updating";
							}
							echoRespnse(201, $response);
						});
							
							
							
/**
	* Fetching All Clients
	* url-/getAllClientList
	* method - GET All
	* params
*/
	$app->get('/getAllClientList/',function() use($app){
			$db = new DataDAO();
			$res = $db->getAllClientList();
			if(sizeof($res))
			{
			echoRespnse(201, $res);
			}
			});
								
/**
	* Fetching Clients List based on the id
	* url-/getClientListById
	* method - GET by Id
	* params - id
*/
	$app->get('/getClientListById/:id',function($id) use($app){
		$db = new DataDAO();
		$res = $db->getClientListById($id);
		if(sizeof($res))
		{
		echoRespnse(201, $res);
		}
	});
	
		/**
		 * Fetching Client List based on the company_id
		 * url-/getAllClientByCId
		 * method - GET by company_id
		 * params - id
		 */
		$app->get('/getAllClientByCId/:company_id',function($company_id) use($app){
			$db = new DataDAO();
			$cDate = $db->ISTConversion();
			$res = $db->getAllClientByCId($company_id,$cDate);
			if(sizeof($res))
			{
				echoRespnse(201, $res);
			}
			
		});
			
 
 //***************************************************************Project*************************************************//
 /**
  * Adding Project
  * url - /addProject
  * method - POST
  * params -project_name,client_id,company_id,start_date,end_date,project_type,date_created,status,billable_type,billing_type,team_id,created_by
  */
 $app->post('/addProject', function() use ($app) {
 	$response = array();
 	$db = new DataDAO();
 	$projectDetails=json_decode(file_get_contents("php://input"));
 	$project=$projectDetails->ProjectInfo;
 	$projectData=json_decode($project);
 	//$id = $roleData->id;
 	$project_name = $projectData->project_name;
 	$client_id = $projectData->client_id;
 	$company_id=$projectData->company_id;
    $start_date= $projectData->start_date;
 	$end_date= $projectData->end_date;
 	$project_type= $projectData->project_type;
 //	$date_created=$projectData->date_created;
 	$date_created = $db->ISTConversion();
    $status=1;
    $billable_type=$projectData->billable_type;
    $billing_type=$projectData->billing_type;
    $team_id=$projectData->team_id;
    $created_by= $projectData->created_by;
    //$query = "INSERT INTO uni_project_master(project_name,client_id,company_id,start_date,end_date,project_type,date_created,status,billable_type,billing_type,team_id,created_by)VALUES('$project_name','$client_id','$company_id','$start_date','$end_date','$project_type','$date_created','$status','$billable_type','$billing_type','$team_id','$created_by')";
    $res = $db->addProject($project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$date_created,$status,$billable_type,$billing_type,$team_id,$created_by);
    
    if ($res)
 	{
 		$response["error"] = false;
 		$response["message"] = "Project created successfully";
 	} else
 	{
 		$response["error"] = true;
 		$response["message"] = "Oops! An error occurred while creating Project";
 	}
 	echoRespnse(201, $response);
 });
 
 /**
 	 * Updating Project
 	 * url - /updateProject
 	 * method - PUT
 	 * params -id
 */
 	$app->post('/updateProject',  function() use($app) {
 		$db = new DataDAO();
 		$response = array();
 		$projectDetails=json_decode(file_get_contents("php://input"));
 		$project=$projectDetails->ProjectInfo;
 		$projectData=json_decode($project);
 		$id = $projectData->id;
 		$project_name = $projectData->project_name;
 		$client_id = $projectData->client_id;
 		$company_id= $projectData->company_id;
 		$start_date= $projectData->start_date;
 		$end_date= $projectData->end_date;
 		$project_type= $projectData->project_type;
 		//$last_updated= $projectData->last_updated;
 		$last_updated = $db->ISTConversion();
 		$updated_by=$projectData->updated_by;
 		$team_id=$projectData->team_id;
 		$billable_type=$projectData->billable_type;
 		$billing_type=$projectData->billing_type;
 		$result = $db->updateProject($id,$project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$last_updated,$updated_by,$billable_type,$billing_type,$team_id);
 		
 		if ($result)
 		{
 			$response["error"] = false;
 			$response["message"] = "You are successfully updated";
 		} else
 		{
 			$response["error"] = true;
 			$response["message"] = "Oops! An error occurred while updating";
 		}
 		echoRespnse(201, $response);
 	});
 	
/**
 	* Updating Projects by Making Status Inactive
 	* url - /deleteProjects
 	* method - PUT
 	* params -id
 */
 		$app->post('/deleteProjects', function() use($app) {
 			$db = new DataDAO();
 			$response = array();
 			$projectDetails=json_decode(file_get_contents("php://input"));
 			$project=$projectDetails->ProjectInfo;
 			$projectData=json_decode($project);
 			$id = $projectData->id;
 			$result = $db->deleteProjects($id);
 			
 			if ($result)
 			{
 				$response["error"] = false;
 				$response["message"] = "You are successfully updated";
 			} else
 			{
 				$response["error"] = true;
 				$response["message"] = "Oops! An error occurred while updating";
 			}
 			echoRespnse(201, $response);
 		});
 			
 			
/**
 	* Fetching All Projects
 	* url-/getAllProjectList
 	* method - GET All
 	* params
*/
 			$app->get('/getAllProjectList/',function() use($app){
 				$db = new DataDAO();
 				$res = $db->getAllProjectList();
 				if(sizeof($res))
 				{
 					echoRespnse(201, $res);
 				}
 			});
 			
 				
/**
 	* Fetching Projects List based on the id
 	* url-/getProjectListById
 	* method - GET by Id
 	* params - id
 */
 				$app->get('/getProjectListById/:id',function($id) use($app){
 					$db = new DataDAO();
 					$res = $db->getProjectListById($id);
 					if(sizeof($res))
 					{
 						echoRespnse(201, $res);
 					}
 				});
 				
 /**
 	 * Fetching Project List based on the company_id
     * url-/getAllProjectByCId
 	 * method - GET by company_id
 	 * params - id
  */
 					$app->get('/getAllProjectByCId/:company_id',function($company_id) use($app){
 						$db = new DataDAO();
 						$cDate = $db->ISTConversion();
 						$res = $db->getAllProjectByCId($company_id,$cDate);
 						if(sizeof($res))
 						{
 							echoRespnse(201, $res);
 						}
 						
 					});
 						
//***************************************************************Modules*************************************************************************************************************************************************//
 /**
 	* Adding Modules
 	* url - /addModules
 	* method - POST
 	* params-module_name,status,company_id,project_id,date_created,created_by,start_date,end_date
 */
 					$app->post('/addModules', function() use ($app) {
 						$response = array();
 						$db = new DataDAO();
 						$ModuleDetails=json_decode(file_get_contents("php://input"));
 						$Modules=$ModuleDetails->ModuleInfo;
 						$ModuletData=json_decode($Modules);
 						$module_name = $ModuletData->module_name;
 						$status = 1;
 						$company_id =$ModuletData->company_id;
 						$project_id = $ModuletData->project_id;
 						//$date_created= $ModuletData->date_created;
 						$date_created = $db->ISTConversion();
 						$created_by= $ModuletData->created_by;
 						$start_date= $ModuletData->start_date;
 						$end_date= $ModuletData->end_date;
 						
 						$res = $db->addModules($module_name,$status,$company_id,$project_id,$date_created,$created_by,$start_date,$end_date);
 						if ($res)
 						{
 							$response["error"] = false;
 							$response["message"] = "Modules created successfully";
 						} else
 						{
 							$response["error"] = true;
 							$response["message"] = "Oops! An error occurred while creating Modules";
 						}
						echoRespnse(201, $response);
 						
 					});
/**
 * Updating Modules
 * url - /updateModule
 * method - PUT
  * params -id
*/
 						$app->post('/updateModule',function() use($app) {
 							$db = new DataDAO();
 							$response = array();
 							$ModuleDetails=json_decode(file_get_contents("php://input"));
 							$Modules=$ModuleDetails->ModuleInfo;
 							$ModuletData=json_decode($Modules);
 							$id = $ModuletData->id;
 							$module_name = $ModuletData->module_name;
 							//$status = 1;
 							$company_id =$ModuletData->company_id;
 							$project_id = $ModuletData->project_id;
 							//$last_updated= $ModuletData->last_updated;
 							$last_updated = $db->ISTConversion();
 							$updated_by= $ModuletData->updated_by;
 							$start_date= $ModuletData->start_date;
 							$end_date= $ModuletData->end_date;
 							
 							$result = $db->updateModule($id,$module_name,$company_id,$project_id,$last_updated,$updated_by,$start_date,$end_date);
 							
 							if ($result)
 							{
 								$response["error"] = false;
 								$response["message"] = "You are successfully updated";
 							} else
 							{
 								$response["error"] = true;
 								$response["message"] = "Oops! An error occurred while updating";
 							}
 							echoRespnse(201, $response);
 						});
 /**
 	* Updating Modules by Making Status Inactive
 	* url - /deleteModule
 	* method - PUT
 	* params -id
*/
 							$app->post('/deleteModule',function() use($app) {
 								$db = new DataDAO();
 								$response = array();
 								$ModuleDetails=json_decode(file_get_contents("php://input"));
 								$Modules=$ModuleDetails->ModuleInfo;
 								$ModuletData=json_decode($Modules);
 								$id = $ModuletData->id;
 								$result = $db->deleteModule($id);
 								
 								if ($result)
 								{
 									$response["error"] = false;
 									$response["message"] = "You are successfully made Inactive";
 								} else
 								{
 									$response["error"] = true;
 									$response["message"] = "Oops! An error occurred while making status Inactive";
 								}
 								echoRespnse(201, $response);
 							});
 							
 /**
 	* Fetching All Module List
 	* url - /getAllModuleList
 	* method - GET
 	* params
 */
 	$app->get('/getAllModuleList/',function() use($app){
 	$db = new DataDAO();
 	$res = $db->getAllModuleList();
 	if(sizeof($res))
 		{
 		echoRespnse(201, $res);
 		}
 	});
 									
 /**
 	 * Fetching Module List based on the id
 	 * url-/getModuleListById
 	 * method - GET by Id
 	 * params - id
 */
 	$app->get('/getModuleListById/:id',function($id) use($app){
 	$db = new DataDAO();
 	$res = $db->getModuleListById($id);
 	if(sizeof($res))
 	{
 	echoRespnse(201, $res);
 	}
 	});
 	
 	/**
 		 * Fetching Module List based on the company_id
 		 * url-/getAllModuleByCId
 		 * method - GET by company_id
 		 * params - id
 		 */
 		$app->get('/getAllModuleByCId/:company_id',function($company_id) use($app){
 			$db = new DataDAO();
 			$cDate = $db->ISTConversion();
 			$res = $db->getAllModuleByCId($company_id,$cDate);
 			if(sizeof($res))
 			{
 				echoRespnse(201, $res);
 			}
 			
 		});
 			
 //***************************************************************Team*************************************************************************************************************************************************//
 		/**
 		 * Adding Team
 		 * url - /addTeams
 		 * method - POST
 		 * params-team_name,status,company_id,date_created,created_by,start_date,end_date
 		 */
 		$app->post('/addTeams', function() use ($app) {
 			$response = array();
 			$db = new DataDAO();
 			$TeamDetails=json_decode(file_get_contents("php://input"));
 			$Teams=$TeamDetails->TeamInfo;
 			$TeamData=json_decode($Teams);
 			$team_name = $TeamData->team_name;
 			$status = 1;
 			$company_id =$TeamData->company_id;
 			//$project_id = $TeamData->project_id;
 			//$date_created= $TeamData->date_created;
 			$date_created = $db->ISTConversion();
 			$created_by= $TeamData->created_by;
 			$start_date= $TeamData->start_date;
 			$end_date= $TeamData->end_date;
 			
 			$res = $db->addTeams($team_name,$status,$company_id,$date_created,$created_by,$start_date,$end_date);
 			if ($res)
 			{
 				$response["error"] = false;
 				$response["message"] = "Teams are created successfully";
 			} else
 			{
 				$response["error"] = true;
 				$response["message"] = "Oops! An error occurred while creating Teams";
 			}
 			echoRespnse(201, $response);
 		});
/**
 * Updating Team
 * url - /updateTeam
 * method - PUT
  * params -id
*/
 						$app->post('/updateTeam',function() use($app) {
 							$db = new DataDAO();
 							$response = array();
 							$TeamDetails=json_decode(file_get_contents("php://input"));
 							$Teams=$TeamDetails->TeamInfo;
 							$TeamData=json_decode($Teams);
 							$id = $TeamData->id;
 							$team_name = $TeamData->team_name;
 							//$status = 1;
 							$company_id =$TeamData->company_id;
 							//$project_id = $TeamData->project_id;
 							//$last_updated= $TeamData->last_updated;
 							$last_updated = $db->ISTConversion();
 							$updated_by= $TeamData->updated_by;
 							$start_date= $TeamData->start_date;
 							$end_date= $TeamData->end_date;
 							
 							$result = $db->updateTeam($id,$team_name,$company_id,$last_updated,$updated_by,$start_date,$end_date);
 							
 							if ($result)
 							{
 								$response["error"] = false;
 								$response["message"] = "You are successfully updated";
 							} else
 							{
 								$response["error"] = true;
 								$response["message"] = "Oops! An error occurred while updating";
 							}
 							echoRespnse(201, $response);
 						});
 /**
 	* Updating Teams by Making Status Inactive
 	* url - /deleteTeam
 	* method - PUT
 	* params -id
*/
 							$app->post('/deleteTeam',function() use($app) {
 								$db = new DataDAO();
 								$response = array();
 								$TeamDetails=json_decode(file_get_contents("php://input"));
 								$Modules=$TeamDetails->TeamInfo;
 								$TeamData=json_decode($Modules);
 								$id = $TeamData->id;
 								$result = $db->deleteTeam($id);
 								
 								if ($result)
 								{
 									$response["error"] = false;
 									$response["message"] = "You are successfully made Inactive";
 								} else
 								{
 									$response["error"] = true;
 									$response["message"] = "Oops! An error occurred while making status Inactive";
 								}
 								echoRespnse(201, $response);
 							}); 	
 /**
 	* Fetching All Team List
 	* url - /getAllTeamList
 	* method - GET
 	* params
 */
 	$app->get('/getAllTeamList/',function() use($app){
 	$db = new DataDAO();
 	$res = $db->getAllTeamList();
 	if(sizeof($res))
 		{
 		echoRespnse(201, $res);
 		}
 	});
 									
 /**
 	 * Fetching Team List based on the id
 	 * url-/getTeamListById
 	 * method - GET by Id
 	 * params - id
 */
 	$app->get('/getTeamListById/:id',function($id) use($app){
 	$db = new DataDAO();
 	$res = $db->getTeamListById($id);
 	if(sizeof($res))
 	{
 	echoRespnse(201, $res);
 	}
 	});	
 		/**
 		 * Fetching Team List based on the company_id
 		 * url-/getAllTeamByCId
 		 * method - GET by company_id
 		 * params - id
 		 */
 		$app->get('/getAllTeamByCId/:company_id',function($company_id) use($app){
 			$db = new DataDAO();
 			$cDate = $db->ISTConversion();
 			$res = $db->getAllTeamByCId($company_id,$cDate);
 			if(sizeof($res))
 			{
 				echoRespnse(201, $res);
 			}
 			
 		});
 			
//***************************************************************Team Members*************************************************************************************************************************************************//
 		/**
 		 * Adding TeamMembers
 		 * url - /addTeamMembers
 		 * method - POST
 		 * params-user_id,project_id,status,company_id,isteamlead,date_created,created_by
 		 */
 		$app->post('/addTeamMembers', function() use ($app) {
 			$response = array();
 			$db = new DataDAO();
 			$TeamMemberDetails=json_decode(file_get_contents("php://input"));
 			$TeamMembers=$TeamMemberDetails->TeamMemberInfo;
 			$TeamMemData=json_decode($TeamMembers);
 			$user_id = $TeamMemData->user_id;
			$project_id = $TeamMemData->project_id;
 			$status = 1;
 			$company_id =$TeamMemData->company_id;
 			$isteamlead = $TeamMemData->isteamlead;
 			$start_date = $TeamMemData->start_date;
 			$end_date = $TeamMemData->end_date; 
 			//$date_created= $TeamMemData->date_created;
 			$date_created = $db->ISTConversion();
 			$created_by= $TeamMemData->created_by;
 			
 			
 			$res = $db->addTeamMembers($user_id,$project_id,$status,$company_id,$isteamlead,$start_date,$end_date,$date_created,$created_by);
 			if ($res)
 			{
 				$response["error"] = false;
 				$response["message"] = "TeamMembers are created successfully";
 			} else
 			{
 				$response["error"] = true;
 				$response["message"] = "Oops! An error occurred while creating TeamMembers";
 			}
 			echoRespnse(201, $response);
 		});	
		
/**
 * Updating Team
 * url - /updateTeamMembers
 * method - PUT
  * params -id
*/
 						$app->post('/updateTeamMembers',function() use($app) {
 							$db = new DataDAO();
 							$response = array();
							$TeamMemberDetails=json_decode(file_get_contents("php://input"));
							$TeamMembers=$TeamMemberDetails->TeamMemberInfo;
							$TeamMemData=json_decode($TeamMembers);
							$id = $TeamMemData->id;
							$user_id = $TeamMemData->user_id;
							$project_id = $TeamMemData->project_id;
							$company_id =$TeamMemData->company_id;
							$isteamlead = $TeamMemData->isteamlead;
							$start_date = $TeamMemData->start_date;
							$end_date = $TeamMemData->end_date; 
							//$last_updated= $TeamMemData->last_updated;
							$last_updated = $db->ISTConversion();
							$updated_by= $TeamMemData->updated_by;
 							
							$result = $db->updateTeamMembers($id,$user_id,$company_id,$project_id,$isteamlead,$start_date,$end_date,$last_updated,$updated_by);
 							
 							if ($result)
 							{
 								$response["error"] = false;
 								$response["message"] = "You are successfully updated TeamMembers";
 							} else
 							{
 								$response["error"] = true;
 								$response["message"] = "Oops! An error occurred while updating TeamMembers";
 							}
 							echoRespnse(201, $response);
 						});		
						
						
 /**
 	* Updating Teams by Making Status Inactive
 	* url - /deleteTeam
 	* method - PUT
 	* params -id
*/
 							$app->post('/deleteTeamMember',function() use($app) {
 								$db = new DataDAO();
 								$response = array();
 								$TeamMemberDetails=json_decode(file_get_contents("php://input"));
 								$TeamMembers=$TeamMemberDetails->TeamMemberInfo;
 								$TeamMemData=json_decode($TeamMembers);
 								$id = $TeamMemData->id;
 								$result = $db->deleteTeamMember($id);
 								
 								if ($result)
 								{
 									$response["error"] = false;
 									$response["message"] = "You are successfully made Inactive";
 								} else
 								{
 									$response["error"] = true;
 									$response["message"] = "Oops! An error occurred while making status Inactive";
 								}
 								echoRespnse(201, $response);
 							}); 			

 /**
 	* Fetching All Team List
 	* url - /getAllTeamList
 	* method - GET
 	* params
 */
 	$app->get('/getAllTeamMemberList/',function() use($app){
 	$db = new DataDAO();
 	$res = $db->getAllTeamMemberList();
 	if(sizeof($res))
 		{
 		echoRespnse(201, $res);
 		}
 	});
 									
 /**
 	 * Fetching Team List based on the id
 	 * url-/getTeamListById
 	 * method - GET by Id
 	 * params - id
 */
 	$app->get('/getTeamMemberListById/:id',function($id) use($app){
 	$db = new DataDAO();
 	$res = $db->getTeamMemberListById($id);
 	if(sizeof($res))
 	{
 	echoRespnse(201, $res);
 	}
 	});	
 	
 		/**
 		 * Fetching TeamMember List based on the company_id
 		 * url-/getAllTeamMemberByCId
 		 * method - GET by company_id
 		 * params - id
 		 */
 		$app->get('/getAllTeamMemberByCId/:company_id',function($company_id) use($app){
 			$db = new DataDAO();
 			$cDate = $db->ISTConversion();
 			$res = $db->getAllTeamMemberByCId($company_id,$cDate);
 			if(sizeof($res))
 			{
 				echoRespnse(201, $res);
 			}
 		
 		});
 			

//***************************************************************Sprint Plan*************************************************************************************************************************************************//
 		/**
 		 * Adding Sprint Plan
 		 * url - /addSprintPlan
 		 * method - POST
 		 * params-sprint_name,status,company_id,no_of_timesheets,timesheet_type,start_date,end_date,date_created,created_by
 		 */
 		$app->post('/addSprint', function() use ($app) {
 			$response = array();
 			$db = new DataDAO();
 			$SprintDetails=json_decode(file_get_contents("php://input"));
 			$Sprint=$SprintDetails->SprintInfo;
 			$SprintData=json_decode($Sprint);
 			$status = 1;
			$sprint_name = $SprintData->sprint_name;
			$company_id =$SprintData->company_id;
			$no_of_timesheets =$SprintData->no_of_timesheets;
			$timesheet_type =$SprintData->timesheet_type;
			$start_date = $SprintData->start_date;
			$end_date = $SprintData->end_date;
 			///$date_created= $SprintData->date_created;
			$date_created = $db->ISTConversion();
 			$created_by= $SprintData->created_by;
 			
 			
 			$res = $db->addSprint($sprint_name,$status,$company_id,$no_of_timesheets,$timesheet_type,$start_date,$end_date,$date_created,$created_by);
 			if ($res)
 			{
 				$response["error"] = false;
 				$response["message"] = "Sprint Plan are created successfully";
 			} else
 			{
 				$response["error"] = true;
 				$response["message"] = "Oops! An error occurred while creating Sprint Plan";
 			}
 			echoRespnse(201, $response);
 		});	
		
/**
 * Updating Sprint
 * url - /updateSprint
 * method - PUT
  * params -id
*/
 						$app->post('/updateSprint',function() use($app) {
 							$db = new DataDAO();
 							$response = array();
							$SprintDetails=json_decode(file_get_contents("php://input"));
							$Sprint=$SprintDetails->SprintInfo;
							$SprintData=json_decode($Sprint);
							$id = $SprintData->id;
							$sprint_name = $SprintData->sprint_name;
							$company_id =$SprintData->company_id;
							$no_of_timesheets =$SprintData->no_of_timesheets;
							$timesheet_type =$SprintData->timesheet_type;
							$start_date = $SprintData->start_date;
							$end_date = $SprintData->end_date;
							//$last_updated= $SprintData->last_updated;
							$last_updated = $db->ISTConversion();
							$updated_by= $SprintData->updated_by;
 							
							$result = $db->updateSprint($id,$sprint_name,$company_id,$no_of_timesheets,$timesheet_type,$start_date,$end_date,$last_updated,$updated_by);
 							
 							if ($result)
 							{
 								$response["error"] = false;
 								$response["message"] = "You are successfully updated Sprint Plan";
 							} else
 							{
 								$response["error"] = true;
 								$response["message"] = "Oops! An error occurred while updating Sprint Plan";
 							}
 							echoRespnse(201, $response);
 						});		
						
						
 /**
 	* Updating Sprint by Making Status Inactive
 	* url - /deleteSprint
 	* method - PUT
 	* params -id
*/
 							$app->post('/deleteSprint',function() use($app) {
 								$db = new DataDAO();
 								$response = array();
 								$SprintDetails=json_decode(file_get_contents("php://input"));
 								$Sprint=$SprintDetails->SprintInfo;
 								$SprintData=json_decode($Sprint);
 								$id = $SprintData->id;
 								$result = $db->deleteSprint($id);
 								
 								if ($result)
 								{
 									$response["error"] = false;
 									$response["message"] = "You are successfully made Inactive";
 								} else
 								{
 									$response["error"] = true;
 									$response["message"] = "Oops! An error occurred while making status Inactive";
 								}
 								echoRespnse(201, $response);
 							}); 			

 /**
 	* Fetching All Sprint List
 	* url - /getAllSprintList
 	* method - GET
 	* params
 */
 	$app->get('/getAllSprintList/',function() use($app){
 	$db = new DataDAO();
 	$res = $db->getAllSprintList();
 	if(sizeof($res))
 		{
 		echoRespnse(201, $res);
 		}
 	});
 									
 /**
 	 * Fetching Sprint List based on the id
 	 * url-/getSprintListById
 	 * method - GET by Id
 	 * params - id
 */
 	$app->get('/getSprintListById/:id',function($id) use($app){
 	$db = new DataDAO();
 	$res = $db->getSprintListById($id);
 	if(sizeof($res))
 	{
 	echoRespnse(201, $res);
 	}
 	});	
 	
 	/**
 		 * Fetching Sprint List based on the company_id
 		 * url-/getAllSprintByCId
 		 * method - GET by company_id
 		 * params - id
 		 */
 		$app->get('/getAllSprintByCId/:company_id',function($company_id) use($app){
 			$db = new DataDAO();
 			$cDate = $db->ISTConversion();
 			$res = $db->getAllSprintByCId($company_id,$cDate);
 			if(sizeof($res))
 			{
 				echoRespnse(201, $res);
 			}
 			
 		});
//***************************************************************Tags*************************************************************************************************************************************************//
 			/**
 			 * Adding Tags
 			 * url - /addTags
 			 * method - POST
 			 * params-tag_name,tag_description,start_date,end_date,status,date_created,created_by
 			 */
 			$app->post('/addTags', function() use ($app) {
 				$response = array();
 				$db = new DataDAO();
 				$TagsDetails=json_decode(file_get_contents("php://input"));
 				$Tags=$TagsDetails->TagInfo;
 				$TagData=json_decode($Tags);
 				$tag_name = $TagData->tag_name;
 				$tag_description = $TagData->tag_description;
 				$start_date = $TagData->start_date;
 				$end_date = $TagData->end_date;
 				$status = 1;
 				//$date_created= $TagData->date_created;
 				$date_created = $db->ISTConversion();
 				$created_by= $TagData->created_by;
 				
 				
 				$res = $db->addTags($tag_name,$tag_description,$start_date,$end_date,$status,$date_created,$created_by);
 				if ($res)
 				{
 					$response["error"] = false;
 					$response["message"] = "Tags are created successfully";
 				} else
 				{
 					$response["error"] = true;
 					$response["message"] = "Oops! An error occurred while creating Tags";
 				}
 				echoRespnse(201, $response);
 			});
 				
 				/**
 				 * Updating Tags
 				 * url - /updateTags
 				 * method - PUT
 				 * params -id
 				 */
 				$app->post('/updateTags',function() use($app) {
 					$db = new DataDAO();
 					$response = array();
 					$TagsDetails=json_decode(file_get_contents("php://input"));
 					$Tags=$TagsDetails->TagInfo;
 					$TagData=json_decode($Tags);
 					$id = $TagData->id;
 					$tag_name = $TagData->tag_name;
 					$tag_description = $TagData->tag_description;
 					$start_date = $TagData->start_date;
 					$end_date = $TagData->end_date;
 					//$last_updated= $TagData->last_updated;
 					$last_updated = $db->ISTConversion();
 					$updated_by= $TagData->updated_by;
 					
 					$result = $db->updateTags($id,$tag_name,$tag_description,$start_date,$end_date,$last_updated,$updated_by);
 					
 					if ($result)
 					{
 						$response["error"] = false;
 						$response["message"] = "You are successfully updated Tags";
 					} else
 					{
 						$response["error"] = true;
 						$response["message"] = "Oops! An error occurred while updating Tags";
 					}
 					echoRespnse(201, $response);
 				});
 					
 					
 					/**
 					 * Updating Tags by Making Status Inactive
 					 * url - /deleteTags
 					 * method - PUT
 					 * params -id
 					 */
 					$app->post('/deleteTags',function() use($app) {
 						$db = new DataDAO();
 						$response = array();
 						$TagsDetails=json_decode(file_get_contents("php://input"));
 						$Tags=$TagsDetails->TagInfo;
 						$TagData=json_decode($Tags);
 						$id = $TagData->id;
 						$result = $db->deleteTags($id);
 						
 						if ($result)
 						{
 							$response["error"] = false;
 							$response["message"] = "You are successfully made Inactive";
 						} else
 						{
 							$response["error"] = true;
 							$response["message"] = "Oops! An error occurred while making status Inactive";
 						}
 						echoRespnse(201, $response);
 					});
 						
 						/**
 						 * Fetching All Tags List
 						 * url - /getAllTagsList
 						 * method - GET
 						 * params
 						 */
 						$app->get('/getAllTagsList/',function() use($app){
 							$db = new DataDAO();
 							$res = $db->getAllTagsList();
 							if(sizeof($res))
 							{
 								echoRespnse(201, $res);
 							}
 						});
 							
 							/**
 							 * Fetching Tags List based on the id
 							 * url-/getTagsListById
 							 * method - GET by Id
 							 * params - id
 							 */
 							$app->get('/getTagsListById/:id',function($id) use($app){
 								$db = new DataDAO();
 								$res = $db->getTagsListById($id);
 								if(sizeof($res))
 								{
 									echoRespnse(201, $res);
 								}
 							});
 								
 								/**
 								 * Fetching Tags List based on the company_id
 								 * url-/getAllTagsByCId
 								 * method - GET by company_id
 								 * params - id
 								 */
 								$app->get('/getAllTagsByCId/:company_id',function($company_id) use($app){
 									$db = new DataDAO();
 									$cDate = $db->ISTConversion();
 									$res = $db->getAllTagsByCId($company_id,$cDate);
 									if(sizeof($res))
 									{
 										echoRespnse(201, $res);
 									}
 									
 								});
//***************************************************************Token Master*************************************************************************************************************************************************//
 									/**
 									 * Adding Token Master
 									 * url - /addTokenMaster
 									 * method - POST
 									 * params-user_id,auth_token,issued_on,	issued_for,expireson,date_created,created_by,start_date,end_date
 									 */
 									$app->post('/addTokenMaster', function() use ($app) {
 										$response = array();
 										$db = new DataDAO();
 										$TokenMasterDetails=json_decode(file_get_contents("php://input"));
 										$TokenMaster=$TokenMasterDetails->TokenMasterInfo;
 										$TokenMasterData=json_decode($TokenMaster);
 										$status = 1;
 										$user_id = $TokenMasterData->user_id;
 										$auth_token =$TokenMasterData->auth_token;
 										$issued_on =$TokenMasterData->issued_on;
 										$issued_for =$TokenMasterData->issued_for;
 										$expireson =$TokenMasterData->expireson;
 										$date_created = $db->ISTConversion();
 										$created_by= $TokenMasterData->created_by;
 										$start_date = $TokenMasterData->start_date;
 										$end_date = $TokenMasterData->end_date;
 										///$date_created= $SprintData->date_created;
 								
 										
 										$res = $db->addTokenMaster($user_id,$auth_token,$auth_token,$issued_on,$issued_for,$expireson,$date_created,$created_by,$start_date,$end_date);
 										if ($res)
 										{
 											$response["error"] = false;
 											$response["message"] = "Token Master are created successfully";
 										} else
 										{
 											$response["error"] = true;
 											$response["message"] = "Oops! An error occurred while creating Token Master";
 										}
 										echoRespnse(201, $response);
 									});
 										
 										/**
 										 * Updating Token Master
 										 * url - /updateTokenMaster
 										 * method - PUT
 										 * params -id
 										 */
 										$app->post('/updateTokenMaster',function() use($app) {
 											$db = new DataDAO();
 											$response = array();
 											$TokenMasterDetails=json_decode(file_get_contents("php://input"));
 											$TokenMaster=$TokenMasterDetails->TokenMasterInfo;
 											$TokenMasterData=json_decode($TokenMaster);
 											$id = $TokenMasterData->id;
 											$user_id = $TokenMasterData->user_id;
 											$auth_token =$TokenMasterData->auth_token;
 											$issued_on =$TokenMasterData->issued_on;
 											$issued_for =$TokenMasterData->issued_for;
 											$expireson =$TokenMasterData->expireson;
 											$start_date = $TokenMasterData->start_date;
 											$end_date = $TokenMasterData->end_date;
 											//$last_updated= $SprintData->last_updated;
 											$last_updated = $db->ISTConversion();
 											$updated_by= $TokenMasterData->updated_by;
 											
 											$result = $db->updateTokenMaster($id,$user_id,$auth_token,$issued_on,$issued_for,$expireson,$start_date,$end_date,$last_updated,$updated_by);
 											
 											if ($result)
 											{
 												$response["error"] = false;
 												$response["message"] = "You are successfully updated Token Master";
 											} else
 											{
 												$response["error"] = true;
 												$response["message"] = "Oops! An error occurred while updating Token Master";
 											}
 											echoRespnse(201, $response);
 										});
 											
 											
 											/**
 											 * Updating Token Master by Making Status Inactive
 											 * url - /deleteTokenMaster
 											 * method - PUT
 											 * params -id
 											 */
 											$app->post('/deleteTokenMaster',function() use($app) {
 												$db = new DataDAO();
 												$response = array();
 												$TokenMasterDetails=json_decode(file_get_contents("php://input"));
 												$TokenMaster=$TokenMasterDetails->TokenMasterInfo;
 												$TokenMasterData=json_decode($TokenMaster);
 												$id = $TokenMasterData->id;
 												$result = $db->deleteTokenMaster($id);
 												
 												if ($result)
 												{
 													$response["error"] = false;
 													$response["message"] = "You are successfully made Inactive";
 												} else
 												{
 													$response["error"] = true;
 													$response["message"] = "Oops! An error occurred while making status Inactive";
 												}
 												echoRespnse(201, $response);
 											});
 												
 												/**
 												 * Fetching All TokenMaster List
 												 * url - /getAllTokenMasterList
 												 * method - GET
 												 * params
 												 */
 												$app->get('/getAllTokenMasterList/',function() use($app){
 													$db = new DataDAO();
 													$res = $db->getAllTokenMasterList();
 													if(sizeof($res))
 													{
 														echoRespnse(201, $res);
 													}
 												});
 													
 													/**
 													 * Fetching TokenMaster List based on the id
 													 * url-/getTokenMasterListById
 													 * method - GET by Id
 													 * params - id
 													 */
 													$app->get('/getTokenMasterListById/:id',function($id) use($app){
 														$db = new DataDAO();
 														$res = $db->getTokenMasterListById($id);
 														if(sizeof($res))
 														{
 															echoRespnse(201, $res);
 														}
 													});
 														
 														/**
 														 * Fetching TokenMaster List based on the company_id
 														 * url-/getAllTokenMasterByCId
 														 * method - GET by company_id
 														 * params - id
 														 */
 														$app->get('/getAllTokenMasterByCId/:company_id',function($company_id) use($app){
 															$db = new DataDAO();
 															$cDate = $db->ISTConversion();
 															$res = $db->getAllTokenMasterByCId($company_id,$cDate);
 															if(sizeof($res))
 															{
 																echoRespnse(201, $res);
 															}
 															
 														});

//***************************************************************Tickets*************************************************************************************************************************************************//
 	/**
 		* Adding Tickets
 		* url - /addTickets
 		* method - POST
 		* params-ticket_number,is_billable,sprint_id,ticket_type,project_id,ticket_description,ticket_date,priority,ticket_status,due_date,team_id,external_ticket_id,estimated_hours,tag_id,start_date,end_date,status,date_created,created_by
 	*/
 															/*$app->post('/addTickets', function() use ($app) {
 																$response = array();
 																$db = new DataDAO();
 																$TicketDetails=json_decode(file_get_contents("php://input"));
 																$Ticket=$TicketDetails->TicketInfo;
 																$TicketData=json_decode($Ticket);
 																$ticket_number = $TicketData->ticket_number;
 																$is_billable = $TicketData->is_billable;
 																$sprint_id = $TicketData->sprint_id;
 																$ticket_type = $TicketData->ticket_type;
 																$project_id = $TicketData->project_id;
 																$ticket_description = $TicketData->ticket_description;
 																$ticket_date = $TicketData->ticket_date;
 																$priority = $TicketData->priority;
 																$ticket_status = $TicketData->ticket_status;
 																$due_date = $TicketData->due_date;
 																$team_id = $TicketData->team_id;
 																$external_ticket_id = $TicketData->external_ticket_id;
 																$estimated_hours = $TicketData->estimated_hours;
 																$tag_id = $TicketData->tag_id;
 																$start_date = $TicketData->start_date;
 																$end_date = $TicketData->end_date;
 																$status = 1;
 																//$date_created= $TagData->date_created;
 																$date_created = $db->ISTConversion();
 																$created_by= $TicketData->created_by;
 																
 																
 																$res = $db->addTickets($ticket_number,$is_billable,$sprint_id,$ticket_type,$project_id,$ticket_description,$ticket_date,$priority,$ticket_status,$due_date,$team_id,$external_ticket_id,$estimated_hours,$tag_id,$start_date,$end_date,$status,$date_created,$created_by);
 																if ($res)
 																{
 																	$response["error"] = false;
 																	$response["message"] = "Tickets are created successfully";
 																} else
 																{
 																	$response["error"] = true;
 																	$response["message"] = "Oops! An error occurred while creating Tickets";
 																}
 																echoRespnse(201, $response);
 															});*/
 																
 																/**
 																 * Updating Tags
 																 * url - /updateTags
 																 * method - PUT
 																 * params -id
 																 */
 															/*	$app->post('/updateTags',function() use($app) {
 																	$db = new DataDAO();
 																	$response = array();
 																	$TagsDetails=json_decode(file_get_contents("php://input"));
 																	$Tags=$TagsDetails->TagInfo;
 																	$TagData=json_decode($Tags);
 																	$id = $TagData->id;
 																	$tag_name = $TagData->tag_name;
 																	$tag_description = $TagData->tag_description;
 																	$start_date = $TagData->start_date;
 																	$end_date = $TagData->end_date;
 																	//$last_updated= $TagData->last_updated;
 																	$last_updated = $db->ISTConversion();
 																	$updated_by= $TagData->updated_by;
 																	
 																	$result = $db->updateTags($id,$tag_name,$tag_description,$start_date,$end_date,$last_updated,$updated_by);
 																	
 																	if ($result)
 																	{
 																		$response["error"] = false;
 																		$response["message"] = "You are successfully updated Tags";
 																	} else
 																	{
 																		$response["error"] = true;
 																		$response["message"] = "Oops! An error occurred while updating Tags";
 																	}
 																	echoRespnse(201, $response);
 																});*/
 																	
 																	
 																	/**
 																	 * Updating Tags by Making Status Inactive
 																	 * url - /deleteTags
 																	 * method - PUT
 																	 * params -id
 																	 */
 																/*	$app->post('/deleteTags',function() use($app) {
 																		$db = new DataDAO();
 																		$response = array();
 																		$TagsDetails=json_decode(file_get_contents("php://input"));
 																		$Tags=$TagsDetails->TagInfo;
 																		$TagData=json_decode($Tags);
 																		$id = $TagData->id;
 																		$result = $db->deleteTags($id);
 																		
 																		if ($result)
 																		{
 																			$response["error"] = false;
 																			$response["message"] = "You are successfully made Inactive";
 																		} else
 																		{
 																			$response["error"] = true;
 																			$response["message"] = "Oops! An error occurred while making status Inactive";
 																		}
 																		echoRespnse(201, $response);
 																	});*/
 																		
 																		/**
 																		 * Fetching All Tags List
 																		 * url - /getAllTagsList
 																		 * method - GET
 																		 * params
 																		 */
 																	/*	$app->get('/getAllTagsList/',function() use($app){
 																			$db = new DataDAO();
 																			$res = $db->getAllTagsList();
 																			if(sizeof($res))
 																			{
 																				echoRespnse(201, $res);
 																			}
 																		});*/
 																			
 																			/**
 																			 * Fetching Tags List based on the id
 																			 * url-/getTagsListById
 																			 * method - GET by Id
 																			 * params - id
 																			 */
 																		/*	$app->get('/getTagsListById/:id',function($id) use($app){
 																				$db = new DataDAO();
 																				$res = $db->getTagsListById($id);
 																				if(sizeof($res))
 																				{
 																					echoRespnse(201, $res);
 																				}
 																			});*/
 																				
 																				/**
 																				 * Fetching Tags List based on the company_id
 																				 * url-/getAllTagsByCId
 																				 * method - GET by company_id
 																				 * params - id
 																				 */
 																				/*$app->get('/getAllTagsByCId/:company_id',function($company_id) use($app){
 																					$db = new DataDAO();
 																					$cDate = $db->ISTConversion();
 																					$res = $db->getAllTagsByCId($company_id,$cDate);
 																					if(sizeof($res))
 																					{
 																						echoRespnse(201, $res);
 																					}
 																					
 																				});
 																					*/
//***************************************************************Forgot Password*************************************************************************************************************************************************//	


 		
   /**
 * User Login
 * url - /login
 * method - POST
 * params - email_id, password
 */
    $app->post('/forgotPassword', function() use ($app) {
           $db = new DataDAO();
            $userDetails = json_decode(file_get_contents("php://input"));
			$users = $userDetails->ForgotPasswordInfo;
			$forgotPasswordData = json_decode($users);			
	        $email_id = $forgotPasswordData->EmailID;
			$current_time = $forgotPasswordData->current_time;
			$res = $db->forgotPassword($email_id,$current_time);
			$status = 1;
			//$res = $db->getPassword($email_id, $status);
	        //$salt ="*ISoftcons_Office_Suite*";
            //$pwd = $db->simple_decrypt($res[0]["password"], $salt);
            $response = array();
            if ($res) {
				/*************** Mail Function Begins ***************/
				//$Id=$result[0]["id"];
				//$Username=$result[0]["user_name"];
				$sendmail = new Mailer();
				$subject = "SOS - Forgot Password";
				//$content = "Dear ".$res[0]['first_name'].",<br><br><span>Your Password for EmailID <b>$email_id</b> is <b>".$pwd."</b></span>";
				$content = "Dear ".$res[0]['first_name'].",<br><br><a href='http://106.51.72.111:8083/SoftconsSuiteRestService/#forgotPassword/".$res[0]['id']."'>Click here to reset your password.</a><br><br><span style='color:#ff0000;font-weight:bold;'>Note: This reset password link is valid for 1 hour.</span>";
				$to = $email_id;
				$mailresult = $sendmail->sendMail($subject,$content,$to);
				if($mailresult == "Message sent!"){
					   $response["error"] = false;
					   $response["message"] = "Email Id is valid";
				}
				else{
				   $response["error"] = true;
				   $response["message"] = "Oops! Error Occured please try again!";
				}		
				/*************** Mail Function Ends ***************/					
                // get the user by email
				$response['error'] = true;
                $response['message'] = 'Valid EmailID';
			
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['message'] = 'Invalid EmailID';
                //returnJSONData($app,$response);
            }
			echoRespnse(201, $response);
        });   	
		
$app->post('/changePassword',  function() use($app) {
    $db = new DataDAO();
//$aes= new AES();
    $response = array();
    
                   // $user = json_decode($app->request->post('user'));
					$changePasswordDetails = json_decode(file_get_contents("php://input"));
					$changePasswords = $changePasswordDetails->ChangePasswordInfo;
					$changePasswordData = json_decode($changePasswords);			
					$id = $changePasswordData->UserID;				   
					$password = $changePasswordData->NewPassword;
					$current_time = $changePasswordData->current_time;

			$salt ="*ISoftcons_Office_Suite*";
            $pwd = $db->simple_encrypt($password, $salt);

	//$password=$user->password;
	//$current_time=$user->current_time;
       //#Encrypt
        //$encrypted = $aes->encrypt($password);   
       // #Decrypt
        //$decrypted = $aes->decrypt($encrypted);    
        //$updated_by = $user->updated_by;
	
        	$result = $db->changePassword($id, $pwd, $current_time);
                  if ($result) {
                      $response["error"] = false;
                      $response["message"] = "Password Updated Successfully";
                  } else if ($result) {
                      $response["error"] = true;
                      $response["message"] = "Oops! An error occurred while updating Password";
                  } else if ($result == "Password Link Expired") {
                      $response["error"] = true;
                      $response["message"] = "Oops! Password Activation Link Expired";
                  }
        // echo json response
            echoRespnse(201, $result);
});		
	
					
		
 	$app->run();
?>
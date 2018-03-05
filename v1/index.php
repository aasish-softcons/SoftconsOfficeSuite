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
	
	//verifyRequiredParams(array('user_name', 'password'));
	$db = new DataDAO();
	// reading post params
	$userDetails = json_decode(file_get_contents("php://input"));
	$users = $userDetails->LoginInfo;
	$loginData = json_decode($users);
	
	//$e = $app->request()->post('EmailID');
	
	$email_id = $loginData->EmailID;
	$password = $loginData->Password;
	$salt ="*ISoftcons_Office_Suite*";
	$pwd = $db->simple_encrypt($password, $salt);
	$status = 1;
	//echo "userbname is".$email_id." passwrod is>".$password;
	$response = array();
	$res = $db->checkLogin($email_id, $pwd, $status);
	
	//print_r($db->checkLogin($email_id, $password));
	// check for correct email and password
	if ($res == 'success') {
		// get the user by email
		// echo "login success";
		$user = $db->getUserByUserId($email_id);
		//returnJSONData($app, $user);
		$response['error'] = false;
		$response['message'] = 'Valid Credentials';
		$response['value'] = $user;
		//echoRespnse(201, $response);
	} else {
		// user credentials are wrong
		//echo "login fail";
		$response['error'] = true;
		$response['message'] = 'Invalid Credentials';
		//returnJSONData($app,$response);
	}
	echoRespnse(201, $response);
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
			
			//$company_name = $companyData->company_name;
			$contact_person=$companyData->contact_person;
			$address = $companyData->address;
			$phone_no =$companyData->phone_no;
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
			$last_updated = $companyData->last_updated;
			//$nominal_flag = $companyData->nominal_flag;
			//$created_by = $companyData->created_by;
			$updated_by = $companyData->updated_by;
			$sprint_days = $companyData->sprint_days;
			
			
			$companyExists = $db -> companyExists($company_name);
			
			if(!$companyExists){
				$res = $db->updateCompany($company_name,$contact_person,$address,$phone_no,$email_id,$asset_limit,$subscription_date,
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
			
			
		});
    


//***************************************************************Department********************************************************************************************************//
	/**
	 * Adding Department
	 * url - /addDepartment
	 * method - POST
	 * params-company_id,department_name,status,date_created,created_by
	 */
		$app->post('/addDepartment', function() use ($app) {
			$response = array();
			$db = new DataDAO();
			$DepartmentDetails=json_decode(file_get_contents("php://input"));
			$Departments=$DepartmentDetails->DepartmentInfo;
			$DepartmentData=json_decode($Departments);
			$company_id = $DepartmentData->company_id;
			$department_name = $DepartmentData->department_name;
			$status = 1;
			$date_created= $DepartmentData->date_created;
			$created_by= $DepartmentData->created_by;
			
			
			$res = $db->addDepartment($company_id,$department_name,$status,$date_created,$created_by);
			if ($res)
			{
				$response["error"] = false;
				$response["message"] = "Department created successfully";
			} else
			{
				$response["error"] = true;
				$response["message"] = "Oops! An error occurred while creating Department";
			}
			
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
				$last_updated=$DepartmentData->last_updated;
				$updated_by= $DepartmentData->updated_by;
				$result = $db->updateDepartment($id,$company_id,$department_name,$last_updated,$updated_by);
				
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
							







































//***************************************************************Users********************************************************************************************************//
	/**
	 * Adding User
	 * url - /addUser
	 * method - POST
	 * params -first_name,last_name,company_id,email_id,password,user_address,phone_no,role_id,status,date_created,created_by
	 */
		$app->post('/addUser', function() use ($app) {
			$response = array();
			$db = new DataDAO();
			$userDetails=json_decode(file_get_contents("php://input"));
			$users=$userDetails->UserInfo;
			$userData=json_decode($users);
			//$id = $userData->id;
			$first_name = $userData->first_name;
			$last_name = $userData->last_name;
			$company_id=$userData->company_id;
			$email_id=$userData->email_id;
			$password=$userData->password;
			$user_address=$userData->user_address;
			$phone_no=$userData->phone_no;
			$role_id=$userData->role_id;
			$status = 1;
			$date_created= $userData->date_created;
			$created_by= $userData->created_by;
			

			$res = $db->addUser($first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$status,$date_created,$created_by);
			if ($res)
			{
				$response["error"] = false;
				$response["message"] = "User created successfully";
			} else
			{
				$response["error"] = true;
				$response["message"] = "Oops! An error occurred while creating User";
			}
			
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
				$last_updated= $userData->last_updated;
				$updated_by= $userData->updated_by;
				$result = $db->updateUser($id,$first_name,$last_name,$company_id,$email_id,$password,$user_address,$phone_no,$role_id,$last_updated,$updated_by);
				
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
				
			
//***************************************************************Role*************************************************//
/** 
	 * Adding Role
     * url - /addRole
     * method - POST
     * params -role_name,role_desc,status,date_created,last_updated,created_by,updated_by
 */
$app->post('/addRole', function() use ($app) {
$response = array();
$db = new DataDAO();
$roleDetails=json_decode(file_get_contents("php://input"));
$roles=$roleDetails->RoleInfo;
$roleData=json_decode($roles);
//$id = $roleData->id;
$role_name = $roleData->role_name;
$role_desc = $roleData->role_desc;
$status = 1;
$date_created= $roleData->date_created;
//$last_updated= $roleData->last_updated;
$created_by= $roleData->created_by;
//$updated_by= $roleData->updated_by;
$res = $db->addRole($role_name,$role_desc,$status,$date_created,$created_by);
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
     * params -id
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
	$last_updated= $roleData->last_updated;
	$updated_by= $roleData->updated_by;
	$result = $db->updateRole($id,$role_name,$role_desc,$last_updated,$updated_by);
	
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
 * Updating Role
 * url - /updateRole
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
	* Fetching Users List based on the id
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
//***************************************************************Clients******************************************************************************//
/**
 * Adding Clients
 * url - /addClients
 * method - POST
 * params -client_name,company_id,date_created,created_by,status
*/
				$app->post('/addClients', function() use ($app) {
					$response = array();
					$db = new DataDAO();
					$clientDetails=json_decode(file_get_contents("php://input"));
					$clients=$clientDetails->ClientInfo;
					$clientData=json_decode($clients);
					//$id = $roleData->id;
					$client_name = $clientData->client_name;
					$company_id = $clientData->company_id;
					$date_created= $clientData->date_created;
					$created_by= $clientData->created_by;
					$status = 1;
					$res = $db->addClients($client_name,$company_id,$date_created,$created_by,$status);
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
						$id = $clientData->$id;
						$client_name = $clientData->client_name;
						$company_id = $clientData->company_id;
						$last_updated= $clientData->last_updated;
						$updated_by=$clientData->updated_by;
						//$status = 1;
						$res = $db->updateClients($id,$client_name,$company_id,$last_updated,$updated_by);
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
 
 //***************************************************************Project*************************************************//
 /**
  * Adding Project
  * url - /addProject
  * method - POST
  * params -project_name,client_id,company_id,start_date,end_date,project_type,date_created,status
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
 	$date_created=$projectData->date_created;
    $status=1;
    $res = $db->addProject($project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$date_created,$status);
 	if ($res)
 	{
 		$response["error"] = false;
 		$response["message"] = "Project created successfully";
 	} else
 	{
 		$response["error"] = true;
 		$response["message"] = "Oops! An error occurred while creating Project";
 	}
 	echoRespnse(201,$response );
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
 		$id = $projectData->$id;
 		$project_name = $projectData->project_name;
 		$client_id = $projectData->client_id;
 		$company_id= $projectData->company_id;
 		$start_date= $projectData->start_date;
 		$end_date= $projectData->end_date;
 		$project_type= $projectData->project_type;
 		$last_updated= $projectData->last_updated;
 		$result = $db->updateProject($id,$project_name,$client_id,$company_id,$start_date,$end_date,$project_type,$last_updated);
 		
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
 				
//***************************************************************Modules*************************************************************************************************************************************************//
 /**
 	* Adding Modules
 	* url - /addModules
 	* method - POST
 	* params-module_name,status,company_id,project_id,date_created,created_by
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
 						$date_created= $ModuletData->date_created;
 						$created_by= $ModuletData->created_by;
 						
 						
 						$res = $db->addModules($module_name,$status,$company_id,$project_id,$date_created,$created_by);
 						if ($res)
 						{
 							$response["error"] = false;
 							$response["message"] = "Modules created successfully";
 						} else
 						{
 							$response["error"] = true;
 							$response["message"] = "Oops! An error occurred while creating Modules";
 						}
 						
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
 							$last_updated= $ModuletData->last_updated;
 							$updated_by= $ModuletData->updated_by;
 							
 							$result = $db->updateModule($id,$module_name,$company_id,$project_id,$last_updated,$updated_by);
 							
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
 	
 //***************************************************************Team*************************************************************************************************************************************************//
 		/**
 		 * Adding Team
 		 * url - /addTeams
 		 * method - POST
 		 * params-team_name,status,company_id,project_id,date_created,created_by
 		 */
 		$app->post('/addTeams', function() use ($app) {
 			$response = array();
 			$db = new DataDAO();
 			$TeamDetails=json_decode(file_get_contents("php://input"));
 			$Teams=$TeamDetails->TeamInfo;
 			$TeamData=json_decode($Teams);
 			$team_name = $TeamData->module_name;
 			$status = 1;
 			$company_id =$TeamData->company_id;
 			$project_id = $TeamData->project_id;
 			$date_created= $TeamData->date_created;
 			$created_by= $TeamData->created_by;
 			
 			
 			$res = $db->addTeams($team_name,$status,$company_id,$project_id,$date_created,$created_by);
 			if ($res)
 			{
 				$response["error"] = false;
 				$response["message"] = "Teams are created successfully";
 			} else
 			{
 				$response["error"] = true;
 				$response["message"] = "Oops! An error occurred while creating Teams";
 			}
 			
 		});
 							
 	$app->run();
 	?>
?>
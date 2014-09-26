<?php

/*
 *  
 */

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require_once '../libs/Slim/Slim.php';

$server = $_SERVER;
$request = $_REQUEST;
$requestmethod = 
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

//User id from db - Global Variable
$user_id = NULL;

/**
 *  Verify all params
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $request;
    //Handling PUT request params
    if($server['REQUEST_METHOD'] === 'PUT') {
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
    //Fields are missing or empty
    //echo  errors
    $response = array();
    $app = \Slim\Slim::getInstance();
    $response["error"] = true;
    $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2 ) . ' is missing or empty';
    echoResponse(400, $response);
    $app->stop();
  }
}
   
  /*
   * Validate email address
   */
   function validateEmail($email) {
       $app = \Slim\Slim::getInstance();
       if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           $response["error"] = true;
           $response["message"] = 'Email address not validating';
           echoResponse(400, $response);
           $app->stop();
       }
   }
  
   /**
    * Send email to ADMIN
    * @param String  
    */
   function echoResponse($response) {
       $app = \Slim\Slim::getInstance();
       $app->contentType('application/json');
       
       echo json_encode($response);
   }
 
$app->run();

$app->post('/register', function() use ($app) {
    verifyRequiredParams(array('name', 'email', 'password'));
    
    $response = array();
    
    //read POST params
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    $password = $app->request-post('password');
    
    //validate email address
    validateEmail($email);
    
    $db = new DbHandler();
    $res = $db->createUser($name, $email, $password);
    
    if ($res == USER_CREATED_SUCCESSFULLY) {
       $initialemail = new SendEarntMail();
       return $initialmail;
    } else if ($res == USER_CREATE_FAILED) {
       $failemail = new SendFailMail();
       return $failmail;
    } else if ($res == USER_ALREADY_EXISTS) {
       $anotheremail = new AnotherMail();  
    }

?>
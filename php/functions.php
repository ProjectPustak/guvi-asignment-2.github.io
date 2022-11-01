<?php
require 'vendor/autoload.php';  

include('connection.php');

//mysql
$mysqlObj = new Connection;

//redis
$redis = new Predis\Client();

//mongo
$mongoObj = new MongoDB\Client("mongodb://localhost:27017");  
$mongoCollection = $mongoObj->assignment->users; 

$_action = $_GET['action'];

function checkAuthorization()
{
  global $redis;
  $headers = getallheaders();
  $session_token = $headers['Authorization'];
  //check redis for session
  $record = $redis->get($session_token);
  if(empty($record)){
    $resp = array(
      'status' => FALSE,
      'message' => 'Unauthorized'
    );
    echo json_encode($resp);
    die;
  } else {
    return $record;
  }
}

if($_action == 'check-authorization')
{
  global $redis;
  $headers = getallheaders();
  $session_token = $headers['Authorization'];
  //check redis for session
  $record = $redis->get($session_token);
  if(empty($record)){
    $resp = array(
      'status' => FALSE,
      'message' => 'Unauthorized'
    );
    echo json_encode($resp);
    die;
  } else {
    $resp = array(
      'status' => TRUE,
      'message' => 'Authorized'
    );
    echo json_encode($resp);
    die;
  }
}

if($_action == 'get-user')
{
  $email = checkAuthorization();
  $record = $mongoCollection->findOne(['email' => $email]);
  if(!empty($record))
  {
    $data = $record['data'];
    $user['name'] = $data->name;
    $user['mobile'] = $data->mobile;
    $user['age'] = $data->age;
    $resp = array(
      'status' => TRUE,
      'data' => $user
    );
    echo json_encode($resp);
    die;
  } else {
  
    $resp = array(
      'status' => FALSE,
      'data' => []
    );
    echo json_encode($resp);
    die;
  }
}

if($_action == 'update')
{
  $email = checkAuthorization();
  $posted_data = file_get_contents("php://input");
  $posted_object = json_decode($posted_data);
  if(empty($posted_object->name)){
    $resp = array(
      'status' => FALSE,
      'title' => 'Not allowed!',
      'html' => 'Name is required.'
    );
    echo json_encode($resp);
    die;
  }
  //update mongodb
  $data = array(
    'data' =>array(
      'name' => $posted_object->name,
      'mobile' => $posted_object->mobile,
      'age' => $posted_object->age
    )
  );
  $mongoCollection->updateOne(['email'=>$email],['$set' => $data]);
  $resp = array(
    'status' => TRUE,
    'title' => 'Done!',
    'html' => 'Update successfully done.'
  );
  echo json_encode($resp);
  die;
}

if($_action == 'login')
{
  extract($_POST);
  if((!isset($email)) && (empty($email))){
    $resp = array(
      'status' => FALSE,
      'title' => 'Not allowed!',
      'html' => 'Email is required.'
    );
    echo json_encode($resp);
    die;
  }
  if((!isset($password)) && (empty($password))){
    $resp = array(
      'status' => FALSE,
      'title' => 'Not allowed!',
      'html' => 'Password is required.'
    );
    echo json_encode($resp);
    die;
  }
  $user = $mysqlObj->loginUser($email, $password);
  if(!empty($user)){
    //redis session start here
    $session_token = bin2hex(random_bytes(16));
    //redis session end here
    $redis->set($session_token, $email);
    $redis->expire($session_token, REDIS_SESSION_TIMEOUT);
    $resp = array(
      'status' => TRUE,
      'title' => 'Done!',
      'token' => $session_token,
      'html' => 'Login success.'
    );
    echo json_encode($resp);
    die;
  } else {
    $resp = array(
      'status' => FALSE,
      'title' => 'Not Allowed!',
      'html' => 'User not found.'
    );
    echo json_encode($resp);
    die;
  }
}

if($_action == 'register')
{
  extract($_POST);
  if( (!isset($_POST['email'])) || (empty($email)) ){  
    $resp = array(
      'status' => FALSE,
      'title' => 'Not allowed!',
      'html' => 'E-mail is required.'
    );
    echo json_encode($resp);
    die;
  }
  $user = $mysqlObj->checkUser($email);
  if(!empty($user)){
    $resp = array(
      'status' => FALSE,
      'title' => 'Not allowed!',
      'html' => 'E-mail already exist try another email.'
    );
    echo json_encode($resp);
    die;
  }
  
  if( (!isset($_POST['password'])) || (empty($password)) ){
    $resp = array(
      'status' => FALSE,
      'title' => 'Not allowed!',
      'html' => 'Password is required.'
    );
    echo json_encode($resp);
    die;
  }
  if($mysqlObj->registerUser($email, $password)){
    //insert into mongodb
    $record = array(
      'email' => $email,
      'data' => array(
        'name' => '',
        'mobile' => '',
        'age' => ''
      )
    );
    $mongoCollection->insertOne($record);  
    $resp = array(
      'status' => TRUE,
      'title' => 'Done!',
      'html' => 'Register successfully.'
    );
    echo json_encode($resp);
    die;
  } else {
  
    $resp = array(
      'status' => FALSE,
      'title' => 'Not Allowed!',
      'html' => 'Something went wrong. Please check.'
    );
    echo json_encode($resp);
    die;
  }
}

if($_action == 'logout')
{
  $session_token = checkAuthorization();
  $redis->del($session_token);
  $resp = array(
    'status' => TRUE,
    'title' => 'Done!',
    'html' => 'Logout success.'
  );
  echo json_encode($resp);
  die;
}
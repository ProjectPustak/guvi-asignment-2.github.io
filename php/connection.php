<?php
include('config.php');

class Connection{
  var $servername = DB_HOST;
  var $username = DB_USER;
  var $password = DB_PASS;
  var $conn;

  public function __construct(){
    try {
      $this->conn = new PDO("mysql:host=$this->servername;dbname=" . DB_NAME, $this->username, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      die("Connection failed: " . $e->getMessage());
    }
  }

  public function checkUser($email = NULL){
    $query = $this->conn->prepare("SELECT * FROM `tbl_users` WHERE `email` = '$email' LIMIT 1");
    $query->execute();
    return $query->fetch();
  }


  public function loginUser($email = NULL, $password = NULL){
    $query = $this->conn->prepare("SELECT * FROM `tbl_users` WHERE `email` = '$email' AND `password` = '$password' LIMIT 1");
    $query->execute();
    return $query->fetch();
  }


  public function registerUser($email = NULL, $password = NULL){
    $create_date = date('Y-m-d');
    $create_time = date('h:i:s');
    $query = $this->conn->prepare("INSERT INTO `tbl_users`(`email`, `password`,`create_date`, `create_time`) VALUES ('$email','$password','$create_date','$create_time')");
    $query->execute();
    return TRUE;
  }
}

?>
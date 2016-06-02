<?php

@ob_start();
session_start();

if(!isset($_SESSION['myusername'])){
  header("location:../index.php");
}

require_once("../config/dbConfig.php");
require_once("../config/uploadConfig.php");

if(isset($_POST['id']))
{
  $id=$_POST['id'];
  $type = $_POST['type'];

  // Create connection
  $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  if($type =='toggle'){
      $sql = "update osedetail set status=".$_POST['value']." where Id='$id'";
    }
    else if ($type =='delete'){
      $sql = "delete from osedetail where Id='$id'";
    }


  if ($conn->query($sql) === TRUE) { //success
      $output = "Record deleted successfully";
      $path = '../'.UPLOAD_ROOT_DIRECTORY.'/'.$_POST['country']."/".$_POST['filename'];
      if(file_exists($path)){
        if(unlink($path))//delete from filesystem
        {
            $output = $output . ", also deleted the file ".$path;
        }
        else {
          $output = $output . ", but cant delete the file, please delete the file manually ".$path;
        }
      }
      else {
          $output = $output . ", also deleted the file ".$path;
      }
      echo json_encode(['success' => true, 'msg' => $output]);
  }
  else {
      echo json_encode(array('success' => false, 'msg' => "Error deleting record: " . $conn->error));
  }

  $conn->close();
  }
?>

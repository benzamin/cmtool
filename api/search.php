<?php

    require_once("../config/dbConfig.php");

    $link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    if (mysqli_connect_errno()){
        exit();
    }

    $key=$_GET['key'];
    $array = array();
    $query=mysqli_query($link, "select * from osedetail where country LIKE '%{$key}%' OR osename LIKE '%{$key}%'");
    while($row=mysqli_fetch_assoc($query))
    {
      $sentense = $row['country']." ".$row["osename"];
      $output_array = array();
      preg_match_all("/\S*(?i)".$key."+\S*/", $sentense, $output_array, PREG_PATTERN_ORDER);

       $array = array_merge($array, array_values($output_array[0]));
    }
    echo json_encode($array);
?>

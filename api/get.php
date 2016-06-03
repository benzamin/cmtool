<?php
@ob_start();
session_start();

$is_admin = isset($_POST['type']) && $_POST['type'] =='edit' &&  isset($_SESSION['myusername']);
$is_search = isset($_POST['searchfield']) && $_POST['searchfield'] !='';
if($is_search)
  $search_text = $_POST['searchfield'];

require_once("../config/dbConfig.php");
require_once("../config/uploadConfig.php");
require("./utilities.php");

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if (mysqli_connect_errno()){
    echo "Error: Can't connect to database!";
    exit();
}
//ready the count query
if($is_search){
  $countSql = "SELECT COUNT(`id`) AS total FROM `osedetail`\n"
      . "where country LIKE  '%{$search_text}%'\n"
      . "OR osename LIKE  '%{$search_text}%'";
}else{
    $countSql = "SELECT COUNT(`id`) AS total FROM `osedetail`";
}
$countResult = mysqli_query($link,$countSql);
$get_total_rows = mysqli_fetch_row($countResult); //hold total records in variable
$count=$get_total_rows[0];

//-------------- Paging things ------------------
if(isset($_POST["page"])){
  $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
  $page_number = 1; //if there's no page number, set it to 1
}
//write the current page number to a global window variable
echo "<script type=text/javascript'>window.currentPage = ".$page_number.";</script>";
//get item per page
$item_per_page = ITEM_PER_PAGE;
if(isset($_POST["perpage"]) && $_POST["perpage"] != '' && $_POST["perpage"] >= 10){
  $item_per_page = $_POST["perpage"];
}
//break records into pages
$total_pages = ceil($count/$item_per_page);
//if somehow the pagenumber is greater then total pages, then set it to 1
if($page_number > $total_pages)
    $page_number = 1;
//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

//--------------------------------------------
//ready the all query
if($is_search){
  $querySql = "SELECT * FROM `osedetail`\n"
      . "where country LIKE  '%{$search_text}%'\n"
      . "OR osename LIKE  '%{$search_text}%'\n"
      . "ORDER BY `osedetail`.`osename` ASC LIMIT ".$page_position.", ".$item_per_page;
}else{
    $querySql = "SELECT * FROM `osedetail`\n"
        . "ORDER BY `osedetail`.`createdtime` DESC LIMIT ".$page_position.", ".$item_per_page;
}
$result = mysqli_query($link,$querySql);

if(!$is_search){
  echo "<h4>Detailed List &nbsp;&nbsp;|&nbsp;&nbsp; <small>Total: ".$count." &nbsp;&nbsp;|&nbsp;&nbsp;Order by: Created Time</small> </h4>";
}
else
{
    echo "<h4>Searched List &nbsp;&nbsp;|&nbsp;&nbsp;<small>Searched for: ".$search_text."&nbsp;&nbsp;|&nbsp;&nbsp; Total:".$count." &nbsp;&nbsp;|&nbsp;&nbsp;Order by: Name</small> </h4>";
}
//print the paging number and links
echo "<div align='center'>";
echo "<div class='inline'><div>";
echo paginate_function($item_per_page, $page_number, $count, $total_pages);
echo "</div></div><div class='inline'><div>";
echo "<select class='form-control' id='rowCount' onchange='rowCountSelectionChanged(this);'>";
foreach($ROW_COUNT_LIST as $key => $value):
    if($value == $item_per_page)
      echo "<option value='".$value."' selected = 'selected'>".$value."</option>";
    else
      echo "<option value='".$value."'>".$value."</option>";
endforeach;
echo "</select>";
echo "<script type=text/javascript>$('#rowCount').value = ".$item_per_page.";</script>";
echo "</div></div>";
echo '</div>';

//print table headers
  echo "<table id='oseTable' class='table table-bordered table-striped table-condensed'>";
  echo "<thead><tr>";
    echo "<th>Category</th>";
    echo "<th>Name</th>";
    echo "<th>File</th>";
    echo "<th>Remarks</th>";
    echo "<th>Created</th>";
    if($is_admin){
        echo "<th>User</th>";
        echo "<th>IP</th>";
        //echo "<th>Edit</th>";
        echo "<th>Delete</th>";
      }
  echo "</tr></thead>";

  echo " <tbody>";
  //print the rows based on sql query
  while ($row = mysqli_fetch_array($result)) {
      echo "<tr id='".$row['Id']."'>";
      echo "<td>" . $row["country"] . "</td>";
      echo "<td>" . $row["osename"] . "</td>";
      echo "<td>   <a href='" . $row["osezip"] . "'>Download File</a> </td>";
      echo "<td>" . $row["remark"] . "</td>";
      echo "<td>" . $row["createdtime"] . "</td>";
      // $isLive = strcmp($row["status"],'0');  // if 0 then both are equal
      // if($isLive == 0) echo "<td class='tg-red'>Live<br></td>";
      // else             echo "<td class='tg-green'>Other<br></td>";
      if($is_admin){
        echo "<td>" . $row["user"] . "</td>";
        echo "<td>" . $row["remoteaddress"] . "</td>";
        //echo "<td><a href ='#' class='linkedit' rel='" .$row['Id'] . "' val='".$row["status"]."'>&#9998</a></td>";
        echo "<td><a href ='#' class='linkdelete' rel='" .$row['Id'] ."' country='".$row["country"]."' filename='".$row["osename"]. "'>&#10008</a></td>";
      }
      echo "</tr>";
  }

  echo " </tbody></table>";

  echo '<div align="center">';
  echo paginate_function($item_per_page, $page_number, $count, $total_pages);
  echo '</div>';


  mysqli_close($link);

?>

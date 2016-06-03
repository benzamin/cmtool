<?php
@ob_start();
session_start();

$is_edit_mode = isset($_GET['editmode']) && $_GET['editmode'] == 'edit';
$is_logged_in = isset($_SESSION['myusername']);
if($is_logged_in)
  $username = $_SESSION['myusername'];
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CM Utility | Widespace</title>
  <!-- Bootstrap Core CSS -->
  <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
  <!-- Custom CSS: You can use this stylesheet to override any Bootstrap styles and/or apply your own styles -->
  <link href="css/custom.css" rel="stylesheet" type="text/css" />
  <link href="css/style.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
  <script src="javascript/jquery-1.11.2.min.js"></script>
  <script src="javascript/bootstrap.min.js"></script>
  <script src="javascript/typeahead.min.js"></script>
  <script src="javascript/sweetalert2.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/sweetalert2.css">

  <script type="text/javascript">
    $(document).ready(function() {
      //very first we read the saved variable and set then as window variable
      processStoredData();

      //show table elements
      populateTable();

      //bind pagination 1,2,3,4.. etc links
      var isEditMode = "<?php echo($is_edit_mode); ?>";
      var type = 'view';
      if(isEditMode) type = 'edit';
    	//executes code below when user click on pagination links
    	$("#results").on( "click", ".custom-pagination a", function (e){
    		e.preventDefault();
        showLoading(true);
    		var page = $(this).attr("data-page"); //get page number from link
    		$("#results").load("./api/get.php",{"page":page, "perpage":window.savedPerPage, "type":type, 'searchfield': $('#searchfield').val()}, function(){ //get content from PHP page
    			showLoading(false);
    		});
    	});

      //bind edit/delete button
      $('#results').on('click', 'td a.linkdelete', deleteUser);
      //$('#results').on('click', 'td a.linkedit', editUser);

      //bind typehead
      $('input.typeahead').typeahead({
        name: 'typeahead',
        remote: './api/search.php?key=%QUERY',
        limit: 10
      });

      /*---------bind search button------*/
      // Variable to hold request
      var request;

      // Bind to the submit event of our form
      $("#search").submit(function(event){

          // Abort any pending request
          if (request) {
              request.abort();
          }
          // setup some local variables
          var $form = $(this);
          // Let's select and cache all the fields
          var $inputs = $form.find("input, select, button, textarea");

          // Serialize the data in the form
          var serializedData = $form.serialize();

          // Let's disable the inputs for the duration of the Ajax request.
          // Note: we disable elements AFTER the form data has been serialized.
          // Disabled form elements will not be serialized.
          $inputs.prop("disabled", true);

          // Fire off the request
          var isEditMode = "<?php echo($is_edit_mode); ?>";
          var type = 'view';
          if(isEditMode) type = 'edit';
          showLoading(true);
          request =   $.post('./api/get.php', {
              'type': type,
              "perpage":window.savedPerPage,
              'searchfield': $('#searchfield').val()
            }, function(response, status) {
              showLoading(false);
              if (response == -1) { //load json data from server and output message
                alert('Error:' + response);
              } else { //success
                console.log("success");
                $('#results').html(response);
              }
            });
          // Callback handler that will be called regardless
          // if the request failed or succeeded
          request.always(function () {
              // Reenable the inputs
              $inputs.prop("disabled", false);
          });

          // Prevent default posting of form
          event.preventDefault();
      });

    });

    //bind per-page select box
    //$( "#rowCount" ).change(function() {
    function rowCountSelectionChanged(selection){
      console.log( "Handler for .change() called." );
      //var rct = $("#rowCount");
      //var selectedValue = rct.options[rct.selectedIndex].value;
      //if (selectedValue != window.savedPerPage){
        window.savedPerPage = selection.value;
        if(localStorage)
            localStorage['perpage'] = selection.value;
        showLoading(true);
        var isEditMode = "<?php echo($is_edit_mode); ?>";
        var type = 'view';
        if(isEditMode) type = 'edit';
        $("#results").load("./api/get.php",{"page":window.currentPage,"perpage":selection.value, "type":type, 'searchfield': $('#searchfield').val()}, function(){ //get content from PHP page
          showLoading(false);
        });

    }
    //});

    function processStoredData(){
      var localStorage = isStorageAvailable('localStorage');
      if(localStorage)
        window.savedPerPage = localStorage['perpage'] ? localStorage['perpage'] : 30;
    }

    function isStorageAvailable(type) {
      try {
        var storage = window[type],
          x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return storage;
      } catch (e) {
        return null;
      }
}

    function populateTable() {
      var isEditMode = "<?php echo($is_edit_mode); ?>";
      var type = 'view';
      if(isEditMode) type = 'edit';
      showLoading(true);
      $("#results").load("./api/get.php",{"type":type, "perpage":window.savedPerPage}, function(){ //get content from PHP page
        showLoading(false);
      });
      /*$.post('./api/get.php', {'type': type}, function(response, status) {if (response == -1) { //load json data from server and output message
          alert('Error:' + response);} else { //success
          console.log("success");$('#results').html(response);}});*/
    }

    /*function editUser(event) {var confirmation = confirm('Are You Sure you want toggle the status of this request?');if(confirmation === true){var val = $(this).attr('val');
        var newVal = (val == 0 ? 1 : 0); // if status is 0 then 1, if 1 then 0
        var postData = {'type':'toggle','id': $(this).attr('rel'), 'value': newVal};$.post('admin/edit.php', postData, function(response, status){
            if(status != 'success'){ //load json data from server and output message
                alert('Error:'+response);}else{ //success
              console.log(response+' status:'+status);populateTable();}});}}*/

    function deleteUser(event) {
      var rand = "dada"+getRandomInt(10,99);
      var row = $(this);
      var val = '';//prompt('Are You Sure you want delete this request? If so enter "'+rand +'" below...');
      swal({
          title: 'Delete this file?',
          //text: "If the file is deleted You won't ",
          html:
            '<p>If you are sure, please enter <b>'+rand +'</b> below...' +
            '<br><br>' +
            '<input id="input-field"></p>',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'No, cancel!',
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm) {
          if (isConfirm) {
              val =  $('#input-field').val();
              console.log(val);
              if(val == rand){
                var country = row.attr('country');
                var filename = row.attr('filename');
                var id = row.attr('rel');
                var postData = {'type':'delete','id': id, 'country': country, 'filename':filename};
                $.post('admin/edit.php', postData, function(response, status){
                  console.log(JSON.stringify(response + " success:" + status));
                    if(status != 'success'){ //load json data from server and output message
                      swal('Error!',response,'error');
                    }else{ //success
                      var data = JSON.parse(response);
                      if(data.success == true){
                        console.log(data.msg);
                        //document.getElementById(id).style.display = "none";
                        $('#'+id).html("<td  colspan='8'>" + data.msg + "</td>").css({'text-align': 'center', 'color': 'blue'}).delay(3500).hide(1000);
                        swal({
                          title: 'Deleted successfully!',
                          html: "The file <b>" + filename + "</b> from <b>" + country + "</b> was deleted!",
                          type: "success",
                          timer: 1500
                        });
                      }
                      else{
                        swal('Error!',data.msg,'error');
                      }
                    }

                });
              }
              else {
                swal(
                  "Can't delete",
                  'Looks like you are not sober enough ;)',
                  'error'
                );
            }
          }
          else{
            console.log('User canceled this request');
          }
      });


    }

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function showLoading(show)
    {
      if(show == true){
        $(".loading-div").height($('html').height()); //show loading element
        $(".loading-div").show(); //show loading element
      }
      else{
        $(".loading-div").hide();
      }

    }

    function showLoginPanel(redirectLink) {
      swal({
        title: 'Enter your credentials',
        html: "<p>Username <input name='myusername' type='text' placeholder='Username' id='myusername'>"+
        '<br><br>' +
        "Password <input name='mypassword' type='password' placeholder='Password' id='mypassword'></p>",
        showCancelButton: true,
        confirmButtonText: 'Submit',
        closeOnConfirm: false,
        allowOutsideClick: false
      },
      function() {
        swal.disableButtons();
        console.log('submit!');
        var postData = {
            'myusername'     : $('#myusername').val(),
            'mypassword'    : $('#mypassword').val()
        };
        $.post('admin/checklogin.php', postData, function(response, status){
            if(status != 'success'){
                swal('Error!',response,'error');
            }else{ //success
              if(response == '0')
                swal('Wrong username/password!','Please try again!','error');
              else{
                if(redirectLink == '')
                  window.location="index.php";
                else
                    window.location=redirectLink;


              }
            }

          });
      });

    }

  </script>

</head>

<body>

  <!-- Navigation starts -->
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
          <!-- Logo and responsive toggle -->
          <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>

              <a class="navbar-brand" href="#">
                <img style="max-width:29px; margin-top: -4px;" src="images/w-logo.png">
              </a>
              <a class="navbar-brand" href="#">FileCatalog</a>
          </div>
          <!-- Navbar links -->
          <div class="collapse navbar-collapse" id="navbar">
              <ul class="nav navbar-nav">
                  <li class="active">
                      <a href="index.php">Home</a>
                  </li>
<?
  if(!$is_logged_in){
?>
                  <li>
                      <a href="#" onclick="showLoginPanel('upload.php');return false;">Upload</a>
                  </li>
                  <li>
                      <!-- <a href="./admin/index.php">Login</a> -->
                      <a href="#" onclick="showLoginPanel('index.php');return false;">Login</a>
                  </li>
<?
  }else {
?>
        <li>
            <a href="upload.php">Upload</a>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><? echo $username ?> <span class="caret"></span></a>
          <ul class="dropdown-menu" aria-labelledby="about-us">
<?
  if($is_edit_mode){
?>
            <li><a href="index.php?editmode=view">View Mode</a></li>
<?
 }else{
?>
            <li><a href="index.php?editmode=edit">Edit Mode</a></li>
<?}?>
            <li><a href="./admin/logout.php">Logout</a></li>

          </ul>
        </li>
<?
}
?>
              </ul>

      <!-- Search -->
      <form id="search" class="navbar-form navbar-right" role="search" method="post" action="checklogin.php">
        <div class="form-group">
          <!-- <input type="text" class="form-control"> -->
          <input id="searchfield" type="text" name="typeahead" class="typeahead tt-query" autocomplete="off" spellcheck="false" placeholder="Type your Query">
        </div>
        <button type="submit" class="btn btn-default">Search</button>
      </form>

          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container -->
  </nav>
<!-- Navigation ends -->

<div class="container">

  <div class="loading-div"><img src="images/ajax-loader.gif" ></div>
    <!--List Row -->
    <div class="row">
      <div id="results">
      </div>
    </div>
    <!-- List -->

</div>
<!-- /.container -->

<footer>
      <div class="small-print">
        <div class="container">
          <p><a href="#">Terms &amp; Conditions</a> | <a href="#">Privacy Policy</a> | <a href="http://www.widespace.com">Contact</a></p>
          <p>Copyright &copy; Info, 2016. With love from <a href="https://github.com/benzamin">Ben</a>.  </p>
        </div>
      </div>
</footer>


</body>

</html>

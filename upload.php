<?php
@ob_start();
session_start();
if(!isset($_SESSION['myusername'])){
  header("location:admin/index.php");
}
require_once("config/uploadConfig.php");
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
  <link href="css/custom.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet" type="text/css" />
  <link href="uploader/css/styles.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
  <script src="javascript/jquery-1.11.2.min.js"></script>
  <script src="javascript/bootstrap.min.js"></script>
  <script src="uploader/javascript/SimpleAjaxUploader.js"></script>
  <script src="javascript/sweetalert2.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/sweetalert2.css">

  <script type="text/javascript">
    $(document).ready(function() {
      $("#errBox").hide();
      $("#msgBox").hide();

      $('.file-well input').on('drop', function(e) {
          console.log('change!');
          $(e.target).parent().removeClass('hover');
          $(e.target).parent().addClass('filled');
      });
      $('.file-well input').on('dragover', function(e) {
          console.log('dragover');
          $(e.target).parent().addClass('hover');
      });
      $('.file-well input').on('dragleave', function(e) {
          console.log('dragleave');
          $(e.target).parent().removeClass('hover');
      });

    });

    window.onload = function() {

      var btn = document.getElementById('uploadBtn'),
          progressBar = document.getElementById('progressBar'),
          msgBox = document.getElementById('msgBox');

      var uploadedFiles = '';
      var failedFiles = '';

      var uploader = new ss.SimpleUpload({
            button: [btn, dragBoxInput],
            dropzone: 'dragbox',
            // form: 'submit',
            url: 'uploader/php/file_upload.php',
            progressUrl: 'uploader/php/uploadProgress.php', // enables cross-browser progress support (more info below)
            name: 'uploadfile',
            method:'POST',
            multipart: true,
            multiple: true,
            //autoSubmit: false,
            debug: true,
            allowedExtensions: ['rar', 'tar.gz', 'tar', 'zip'], // for example, if we were uploading pics
            hoverClass: 'ui-state-hover',
            focusClass: 'ui-state-focus',
            disabledClass: 'ui-state-disabled',
            responseType: 'json',
            onChange: function(filename, extension, uploadBtn, fileSize, file ){
                uploadedFiles = '';
                failedFiles = '';
            },
            onFileSelectDone: function( filenames, uploadBtn, totalFiles ) {
              console.log(filenames + " Total:" + totalFiles);
              var ddl = document.getElementById("selectCountry");
              var selectedValue = ddl.options[ddl.selectedIndex].value;
              if (selectedValue == "select"){
                  showError("Please select a country First!");
                  swal(
                    'Ooops!',
                    'Please select Country First!',
                    'warning'
                  );
                  showMessage('');
                  $('#selectCountry').focus();
                  return false;
              }
              else{
                showError('');
                showMessage('');
                $('#selectCountry').prop('disabled', 'disabled');
                $('#remarks').prop('disabled', 'disabled');
              }
            },
            onExtError: function( filename, extension ) {
                showError('Extension Not supported for file: <strong>' + filename + ' </strong> which has extension: <strong>' + extension + '</strong>');
            },
            onSizeError: function( filename, fileSize ) {
                showError('File-Size error file: <strong>' + filename + ' </strong> which has fileSize: <strong>' + fileSize + '</strong>');
            },
            // onProgress: function( pct ) {
            //   console.log('Progress:'+pct);
            // },
            onSubmit: function(filename, extension) {
                //set custom data
                var ddl = document.getElementById("selectCountry");
                var selectedValue = ddl.options[ddl.selectedIndex].value;
                var self = this;
                self.setData({
                  country : selectedValue, // $_REQUEST['myParam']
                  remarks : $('#remarks').val()
                });

                // Create the elements of our progress bar
                var progress = document.createElement('div'), // container for progress bar
                bar = document.createElement('div'), // actual progress bar
                fileSize = document.createElement('div'), // container for upload file size
                wrapper = document.createElement('div'), // container for this progress bar
                progressBox = document.getElementById('progressBox'); // on page container for progress bars

                // Assign each element its corresponding class
                progress.className = 'progress progress-striped';
                bar.className = 'progress-bar progress-bar-success';
                // progress.className = 'progress';
                // bar.className = 'bar';
                fileSize.className = 'size';
                wrapper.className = 'wrapper';

                // Assemble the progress bar and add it to the page
                progress.appendChild(bar);
                wrapper.innerHTML = '<div class="name">'+filename+'</div>'; // filename is passed to onSubmit()
                wrapper.appendChild(fileSize);
                wrapper.appendChild(progress);
                progressBox.appendChild(wrapper); // just an element on the page to hold the progress bars

                // Assign roles to the elements of the progress bar
                this.setProgressBar(bar); // will serve as the actual progress bar
                this.setFileSizeBox(fileSize); // display file size beside progress bar
                this.setProgressContainer(wrapper); // designate the containing div to be removed after upload

                //showMessage("Uploading files: <strong>" + selectedFiles + "</strong>");
                btn.innerHTML = 'Uploading...'; // change button text to "Uploading..."
              },
            onComplete: function( filename, response ) {
                btn.innerHTML = 'Choose Another File(s)';
                console.log('Uploaded :' +filename);

                if ( !response ) {
                    //msgBox.innerHTML = 'Unable to upload file';
                    failedFiles = failedFiles + 'Unable to upload file ' + filename + '<br>';
                    showError(failedFiles);
                    return;
                }

                if ( response.success === true ) {
                    var ddl = document.getElementById("selectCountry");
                    var country = ddl.options[ddl.selectedIndex].value;
                    if(country != 'select') country = ' inside <strong>' + country + '</strong>!';
                    else country = '!';
                    //msgBox.innerHTML = '<strong>' + escapeTags( filename ) + '</strong>' + ' successfully uploaded.';
                    uploadedFiles = uploadedFiles + '<strong>' + escapeTags( filename ) + '</strong>' + ' successfully uploaded'+ country +'<br>';
                    showMessage(uploadedFiles);

                } else {
                    if ( response.msg )  {
                        //msgBox.innerHTML = escapeTags( response.msg );
                        failedFiles = failedFiles + 'Unable to upload file ' + escapeTags( filename ) + ', '+ escapeTags( response.msg ) + '<br>';

                    } else {
                        //msgBox.innerHTML = 'An error occurred and the upload failed.';
                        failedFiles = failedFiles + 'Unknown error while uploading file '+ escapeTags( filename ) + '<br>';
                    }
                    showError(failedFiles);
                }
                //msgBox.innerHTML = uploadedFiles;
              },
            onAllComplete: function() {
              console.log('All upload Done!');
              $('#selectCountry').prop('selectedIndex', 0).prop('disabled', false);
              $('#remarks').val("").prop('disabled', false);
              $('#selectCountry').focus();
            },
            onError: function( filename, type, status, statusText, response, uploadBtn, size ) {
                showError('Unable to upload file:' + filename);
              }
      });

      $('#submit').on('click', function( e ) {
        e.preventDefault();
        uploader.submit(); // Submit upload when #my_submit_btn is clicked
      });


    };

    function escapeTags( str ) {
      return String( str )
               .replace( /&/g, '&amp;' )
               .replace( /"/g, '&quot;' )
               .replace( /'/g, '&#39;' )
               .replace( /</g, '&lt;' )
               .replace( />/g, '&gt;' );
    }

    function showMessage(msg){
      if(msg != ''){
        $("#msgBox").show();
        $("#msgBox").html(msg);
      }
      else {
        $("#msgBox").hide();
      }
    }
    function showError(msg){
      if(msg != ''){
        $("#errBox").show();
        $("#errBox").html(msg);
      }
      else {
        $("#errBox").hide();
      }
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
              <a class="navbar-brand" href="#">CMTool</a>
          </div>
          <!-- Navbar links -->
          <div class="collapse navbar-collapse" id="navbar">
              <ul class="nav navbar-nav">
                  <li>
                      <a href="index.php">Home</a>
                  </li>
                  <li class="active">
                      <a href="upload.php">Upload</a>
                  </li>
<?
  if(!$is_logged_in){
?>
                                    <li>
                                        <a href="./admin/index.php">Login</a>
                                    </li>
<?
  }else {
?>
                          <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><? echo $username ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu" aria-labelledby="about-us">
                              <li><a href="index.php">View Mode</a></li>
                              <li><a href="index.php?editmode=edit">Edit Mode</a></li>
                              <li><a href="./admin/logout.php">Logout</a></li>

                            </ul>
                          </li>
<?
}
?>
              </ul>

          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container -->
  </nav>
<!-- Navigation ends -->

<div class="container">
  <div class="row">
    <h4>Upload New &nbsp;&nbsp;|&nbsp;&nbsp;<small>CMTool</small> </h4>
    <br>
        <div class="form-group">
          <label for="selectCountry">Country:</label>
          <select class="form-control" id="selectCountry">
            <option value='select'>Select Country</option>
<?php
          foreach($COUNTRY_LIST as $key => $value):
          echo '<option value="'.$value.'">'.$value.'</option>'; //close your tags!!
          endforeach;
?>
          </select>
        </div>
        <div class="form-group">
            <label for="remarks">Remarks (Optional):</label>
            <input id="remarks" type="text" class="form-control" placeholder="Any comment about this file?">
          </div>




      <div class="form-group">
        <label for="dragbox">Drag File(s) to upload:</label>
        <div style="margin-bottom: 1em;">
            <div id = "dragbox" class="file-well">
              <input id='dragBoxInput'type="file"/>
            </div>
        </div>

          <div class="form-group">
            <!-- <label for="uploadBtn">Or</label><br> -->
              <button id="uploadBtn" class="btn btn-large btn-primary">Choose File(s)</button>
          </div>

        </div>

</div>

<div class="row">
  <div id="progressBox">
  </div>
</div>

<div class="row">
          <div id="msgBox" class="alert alert-info" role="alert">

        </div>
        <div id="errBox" class="alert alert-danger" role="alert">

      </div>


</div>
      <!-- <div>
          <button id = "submit" class="btn btn-default">Upload</button>
      </div> -->
    <!-- </form> -->



</div>
<!-- /.container -->

<footer>
      <div class="small-print">
        <div class="container">
          <p><a href="#">Terms &amp; Conditions</a> | <a href="#">Privacy Policy</a> | <a href="http://www.widespace.com">Contact</a></p>
          <p>Copyright &copy; Widespace.com 2016. With love from <a href="https://github.com/benzamin">Ben</a>.  </p>
        </div>
      </div>
</footer>


</body>

</html>

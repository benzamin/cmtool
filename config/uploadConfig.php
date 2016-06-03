<?php
/*
 * Upload folder Settings
 */

//Relative to root directory, like "/filecatalog/UPLOADED_FILES"
define('UPLOAD_ROOT_DIRECTORY' , 'UPLOADED_FILES');

$ALLOWED_FILE_TYPES=array(
  'zip',
  'rar',
  'tar.gz',
  '7z',
  'png',
  'jpg',

);

//Category: IE: Country list, which will be a subfolder to the root directory, like CMtTool/UPLOADED_FILES/Sweden
$CATEGORY_LIST=array(
'Sweden',
'Denmark',
'Norway',
'Germany',
'France',
'UK',
'Italy',
'Spain',
'Finland',
'Netherlands',
'Belgium',
'USA'
 );
//How much to show per page, select box list
 $ROW_COUNT_LIST=array(
   10,
   30,
   50,
   100,
   200,
   500
 );
?>

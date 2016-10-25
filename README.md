# filecatalog
Upload any file, see list of them by category, and search any file with predictive search. Written BE in PHP with MySQL, FE with html-css and javascript. 

![Browse your files](https://github.com/benzamin/filecatalog/blob/master/Screens/View.png?raw=true)

![Upload files](https://github.com/benzamin/filecatalog/blob/master/Screens/Upload.png?raw=true)

## Install and Setup
* Download or clone the repo in your htdocs folder, I used MAMP, you can use your favourite.
* Execute the SQL/fileCatalog-db.sql in the database control panel/phpmyadmin. Add login info in the user table.
* Modify the config/dbConfig.php for database connection info and config/uploadConfig.php file for upload category and stuff.

## Remarks
> Used SimpleAjaxUploader for uploading and SweetAlert library for interactive alerts.
> Used Bootstrap, jQuery, and other utility/libraries.

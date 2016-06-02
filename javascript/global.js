//userlist data array for filling the nfo box
var userListData = [];

//DOM ready
$(document).ready(function() {
    //populate the user table on initial page load
    populateTable();

    //Username link click
    $('#userList table tbody').on('click', 'td a.linkshowuser', showUserInfo);

    //Add user Button click
    $('#btnAddUser').on('click', addUser);

    //delete user
    $('#userList table tbody').on('click', 'td a.linkdeleteuser', deleteUser);
});

//Utility Functions

//fill table with data
function populateTable() {
  var tableContent = '';


  $.ajax({
  dataType: "jsonp",
  url: "http://localhost:3000/users/userlist",
  data: '',
  success: function (data) {

    userListData = data;
    console.log(data);
    //for each item in our JSON, add a table row and cells to the content string
    $.each(data, function(){
      tableContent += '<tr>';
      tableContent += '<td><a href="#" class="linkshowuser" rel="' + this.username + '">' + this.username + '</a></td>';
      tableContent += '<td>' + this.email + '</td>';
      tableContent += '<td><a href ="#" class="linkdeleteuser" rel="' + this._id + '">delete</a></td>';
      tableContent += '</tr>';
    });
    //inject the whole content string into our existing HTML table
    $('#userList table tbody').html(tableContent);
  },
  error: function(error) {alert(JSON.stringify(error))}
});
  //jQuery AJAX call fro userlist JSON
  /*$.getJSON('http://localhost:3000/users/userlist', function (data) {

    userListData = data;
    //for each item in our JSON, add a table row and cells to the content string
    $.each(data, function(){
      tableContent += '<tr>';
      tableContent += '<td><a href="#" class="linkshowuser" rel="' + this.username + '">' + this.username + '</a></td>';
      tableContent += '<td>' + this.email + '</td>';
      tableContent += '<td><a href ="#" class="linkdeleteuser" rel="' + this._id + '">delete</a></td>';
      tableContent += '</tr>';
    });
    //inject the whole content string into our existing HTML table
    $('#userList table tbody').html(tableContent);
  });*/
}

function showUserInfo(event) {
  //prevent default action, that is going to the link user just clicked
  event.preventDefault();

  //retrive the username from link rel attribute
  var thisUserName = $(this).attr('rel');

  var arrayPosition = userListData.map(function(arrayItem) {
    return arrayItem.username;
  }).indexOf(thisUserName);

  //get the user object
  var thisUserObject = userListData[arrayPosition];

  //populate info box
  $('#userInfoName').text(thisUserObject.fullname);
  $('#userInfoAge').text(thisUserObject.age);
  $('#userInfoGender').text(thisUserObject.gender);
  $('#userInfoLocation').text(thisUserObject.location);

}

function addUser(event) {
    event.preventDefault();
    //Basic validation
    var errorCount = 0;
    $('#adduser input').each(function(index, val) {
        if($(this).val() === ''){
          errorCount++;
        }
    });

    //check errorCount
    if(errorCount === 0){
      var newUser = {
        'username':$('#addUser fieldset input#inputUserName').val(),
        'email':$('#addUser fieldset input#inputUserEmail').val(),
        'fullname':$('#addUser fieldset input#inputUserFullname').val(),
        'age':$('#addUser fieldset input#inputUserAge').val(),
        'location':$('#addUser fieldset input#inputUserLocation').val(),
        'gender':$('#addUser fieldset input#inputUserGender').val()
      };

      $.ajax(
        {
          type:'POST',
          data:newUser,
          crossDomain: true,
          url:'http://localhost:3000/users/adduser',
          dataType: 'jsonp',
        }
      ).done(function(response) {
        if(response.msg ===''){
          //clear form inputs
          $('#addUser fieldset input').val('');
          populateTable();
        }
        else{
          //if something went wrong, alert the error message
          alert('Error: '+response.msg);
        }
      });


      }
      else{
        //some fields where empty
        alert('Please fill all the fields');
        return false;
      }
}

function deleteUser(event) {
  event.preventDefault();

  var confirmation = confirm('Are You Sure you want ot delete this user?');

  if(confirmation === true){
    $.ajax(
      {
        type:'DELETE',
        crossDomain: true,
        dataType: 'jsonp',
        url:'http://localhost:3000/users/deleteuser/' + $(this).attr('rel')
      }
    ).done(function(response) {
      (response.msg === '') ? alert('User Deteted!') : alert('Error:' + response.msg);

      populateTable();
    });
  }
  else {
    //user cancalled the deletion
    return false;
  }

}

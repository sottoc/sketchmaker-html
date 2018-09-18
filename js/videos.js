var templates = new Templates();

function createRandomString( length ) {
  var str = "";
  for ( ; str.length < length; str += Math.random().toString( 36 ).substr( 2 ) );
  return str.substr( 0, length );
}

$(function () {
  //$(document).on('click', '#addUserButton', addUserButtonClick);
  //$(document).on('click', '#addUserAccount', addUserAccountClick);
  //$(document).on('click', '#editUserAccount', editUserAccountClick);
  //$(document).on('click', '.editUserButton', function editUserButtonClick(event) {
  //  var elem = event.target
  //    , userId = $(elem).attr('data-user')
  //    , email = $(elem).attr('data-email')
  //  ;
  //
  //  templates.insert('#sink', 'addUsersForm', [{
  //    action: 'editUserAccount',
  //    title: 'Edit User',
  //    id: userId,
  //    email: email,
  //    password: createRandomString(8)
  //  }]);
  //
  //  $('#addUserModal').modal('show');
  //});
  
  

  
  //
  //$.get('api/listUsers.php', processUsersLists, 'json');
  //
  //function addUserButtonClick(event) {
  //  templates.insert('#sink', 'addUsersForm', [{
  //    action: 'addUserAccount',
  //    title: 'Add User',
  //    password: createRandomString(8)
  //  }]);
  //  $('#addUserModal').modal('show');
  //}
  //
  //function addUserAccountClick(event) {
  //  var accountData = $('#accountData').serialize();
  //
  //  $.post('api/addUser.php', accountData, processAddUserResult, 'json');
  //}
  //
  //function editUserAccountClick(event) {
  //  var accountData = $('#accountData').serialize();
  //  console.log(accountData);
  //
  //  $.post('api/editUser.php', accountData, processAddUserResult, 'json');
  //}
  //
  //function processAddUserResult(data) {
  //  if (data.result) {
  //    $('#addUserModal').modal('hide');
  //    window.location.reload(true);
  //  }
  //}

  
  $(document).on('click', '.deleteButton', function deleteButtonClick(event) {
    var elem = event.target , id = $(elem).attr('data-vid');
  
    if (confirm("Are you sure you want to delete this video?")) {
      $.post('api/deleteVideo.php', {vid: id}, function (data) {
        if (data.error) {
            alert(data.error);
        } else {
            elem.closest('tr').remove();
        }
      }, 'json');
    }
  });
  
  //$.get('api/videos.php', processVideosLists, 'json');
  
  function processVideosLists(data) {
    templates.insert('#videosTable', 'videosTableRow', data.result);
  }
});
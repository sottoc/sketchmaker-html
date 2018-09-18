var templates = new Templates();

function createRandomString( length ) {
  var str = "";
  for ( ; str.length < length; str += Math.random().toString( 36 ).substr( 2 ) );
  return str.substr( 0, length );
}

$(function () {
    var overlay_full = $('#overlay_full');
    
  $(document).on('click', '#addUserButton', addUserButtonClick);
  $(document).on('click', '#addUserAccount', addUserAccountClick);
  $(document).on('click', '#editUserAccount', editUserAccountClick);
  $(document).on('click', '.editUserButton', function editUserButtonClick(event) {
    var elem = event.target
      , userId = $(elem).attr('data-user')
      , email = $(elem).attr('data-email')
      , license = $(elem).attr('data-license')
    ;

    templates.insert('#sink', 'addUsersForm', [{
      action: 'editUserAccount',
      title: 'Edit User',
      id: userId,
      email: email,
      license: license,
      password: createRandomString(8)
    }]);

    $('#addUserModal').modal('show');
  });
  $(document).on('click', '.deleteUserButton', function editUserButtonClick(event) {
    var elem = event.target
      , id = $(elem).attr('data-user')
      ;

    if (confirm("Delete this user?")) {
      $.post('api/deleteUser.php', {id: id}, function (data) {
        if (data.result) {
          window.location.reload(true);
        } else {
          alert("Error");
        }
      }, 'json');
    }
  });
  
  

  $.get('api/listUsers.php', processUsersLists, 'json');

  function addUserButtonClick(event) {
    templates.insert('#sink', 'addUsersForm', [{
      action: 'addUserAccount',
      title: 'Add User',
      password: createRandomString(8)
    }]);
    $('#addUserModal').modal('show');
  }

  function addUserAccountClick(event) {
    var accountData = $('#accountData').serialize();

    $.post('api/addUser.php', accountData, processAddUserResult, 'json');
  }

  function editUserAccountClick(event) {
    var accountData = $('#accountData').serialize();
    console.log(accountData);

    $.post('api/editUser.php', accountData, processAddUserResult, 'json');
  }

  function processAddUserResult(data) {
    if (data.result) {
      $('#addUserModal').modal('hide');
      window.location.reload(true);
    }
  }

  function processUsersLists(data) {
    for(i in data.result)
    {
        data.result[i].isAdmin = '<input type="checkbox" name="is_admin" class="user-toggle-admin" value="'+data.result[i].id+'"  '+( data.result[i].isAdmin=='1' ? 'checked' : '' )+' />';
    }
    templates.insert('#usersTable', 'usersTableRow', data.result);
  }
  
    //Send license  
    $(document).on('click','.emailLicenseButton', function(event){
        event.preventDefault();
        
        if(!confirm('Are you sure you want to send license to this user?'))
        {
            return false;
        }
        
        overlay_full.show();
        $.post('api/emailLicense.php', {id: $(this).data('user')},function(data){
            overlay_full.hide();
            if('error' in data )
            {
                if(jQuery().growl)
                {
                    jQuery.growl(
                        {
                            location: 'tc', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                            style: 'error', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                            message: data.error
                        }
                    );
                }
                console.error(data.error);
            }else{
                jQuery.growl(
                    {
                        title: 'Email has been sent',
                        location: 'br', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                        style: 'notice', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                        message: 'Email has been sent'
                    }
                );                
            }
        }, 'json').fail(function(response){
            console.error(response);
            overlay_full.hide();
        });
    });
  
  
  //Search
    $('#search_form').on('submit',function(event){
        overlay_full.show();
        $.post('api/listUsers.php',$(this).serialize(),function(data){
            overlay_full.hide();
            processUsersLists(data);
        },'json').fail(function(){
            overlay_full.hide();
        });
        return false;
    });
  
    //Toggle admin
    $(document).on('click','.user-toggle-admin', function(event){
        
        if(!confirm('Are you sure you want to toggle admin privileges for this user?'))
        {
            return false;
        }
        
        overlay_full.show();
        $.post('api/toggleAdmin.php', {id: $(this).val()},function(data){
            overlay_full.hide();
            if('error' in data )
            {
                if(jQuery().growl)
                {
                    jQuery.growl(
                        {
                            location: 'tc', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                            style: 'error', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                            message: data.error
                        }
                    );
                }
                console.error(data.error);
            }else{
                jQuery.growl(
                    {
                        title: 'Success!',
                        location: 'br', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                        style: 'notice', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                        message: 'Admin privileges has been toggled'
                    }
                );                
            }
        }, 'json').fail(function(response){
            console.error(response);
            overlay_full.hide();
        });
    });
  
});
$(function() {
    var overlay_full = $('#overlay_full');
    
  $('#loginButton').on('click', function (event) {
    var form = $('#loginForm')
      , url = form.attr('action')
      , formData = form.serialize()
      ;
    event.preventDefault();

    $.post(url, formData, processData, 'json');
  });

  function processData(data) {
    if (data.result) {
      window.location.href = '/';
    } else {
      alert("Couldn't log you in");
    }
  }
  
  $('#forgotForm').on('submit', function(event){
    event.preventDefault();
    overlay_full.show();
    $.post('api/forgot.php',$(this).serialize(),function(data){
        $('.form-control').removeClass('is-invalid');
        if('error' in data)
        {
            $('#inputEmail').addClass('is-invalid');
            $('#error_text').html(data.error);   
        }else{
            var form = $('#forgotForm');
            var notify = $('<div style="display:none;" id="login_success">Please check your email for further instructions</div>').insertAfter(form);
            form.fadeOut('fast',function(){
                notify.fadeIn();
            });
        }
        overlay_full.hide();
    },'json').fail(function(error){
        console.error(error);
        overlay_full.hide();
    });
    return false;
  });
  
  $('#resetForm').on('submit', function(event){
    event.preventDefault();
    overlay_full.show();
    $.post('api/reset.php',$(this).serialize(),function(data){
        $('.form-control').removeClass('is-invalid');
        if('error' in data)
        {
            $('#inputPassword').addClass('is-invalid');
            $('#error_text').html(data.error);   
        }else{
            var form = $('#resetForm');
            var notify = $('<div style="display:none;" id="login_success"><h4>Your password has been changed!</h4><p><a href="/login.php">Go to Login</a></p></div>').insertAfter(form);
            form.fadeOut('fast',function(){
                notify.fadeIn();
            });
        }
        overlay_full.hide();
    },'json').fail(function(error){
        console.error(error);
        overlay_full.hide();
    });
    return false;
  });  
  
});
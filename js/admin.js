function createRandomString( length ) {
  var str = "";
  for ( ; str.length < length; str += Math.random().toString( 36 ).substr( 2 ) );
  return str.substr( 0, length );
}

$(function () {
    var overlay_full = $('#overlay_full');
    $('#btnOpenSamplesDlg').on('click',function(){
        if(window.samplesUploader){
            samplesUploader.reset();
        }
        
        $('#modalLoadSamples').modal('show');
         
    });
    
    $('#form_settings').on('submit',function(event){
        var $form = $(this);
        event.preventDefault();
                
        overlay_full.show();
        $.post( $form.attr('action'), $form.serialize(),function(data){
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
                        message: 'Settings has been saved!'
                    }
                );                
            }
        }, 'json').fail(function(response){
            console.error(response);
            overlay_full.hide();
        });
        
        //return false;
    });
});
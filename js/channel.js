var channel = {
    timestamp: 0,
    currentTick:null,
    interval: 5000,
    page: '',
    init: function(page){
        this.setTick();
        this.page = page;
        this.timestamp = Math.floor( new Date().getTime()  / 1000);
    },
    setTick:function(){
        this.currentTick = setInterval(channel.tick,channel.interval);  
    },
    clearTick: function()
    {
        clearInterval(channel.interval);
    },
    
    tick: function()
    {
        jQuery.get('/api/channel.php',{t:channel.timestamp},function(data){
            
            if(data.length == 0)
            {
                return;
            }
            
            if('error' in data)
            {
                channel._showError(data.error);
                return false;
            }
           
            var i = 0;
            for(i; i < data.length; i++)
            {
                var message = data[i];
                var action = '_action_'+message.type;
                if(channel[action]!== undefined)
                {
                    channel[action](message.meta);
                }
                channel.timestamp = message.time;
            }

            
        },'json').fail(function(e){
                channel._showError(e.responseText,e.status + ' :: ' + e.statusText);
                return false;
            });
    },
    _showError:function(msg)
    {
        if(jQuery().growl)
        {
            jQuery.growl(
                {
                    location: 'tc', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                    style: 'error', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                    message: msg
                }
            );
        }
        console.error(msg);
    },
    _action_render_done : function(video)
    {
        //console.log(video);
        jQuery('#video_progress_'+video.id+' .progress-bar').animate({width:'100%'});
        jQuery.growl(
            {
                title: 'Video has been rendered',
                location: 'br', // ('tl' | 'tr' | 'bl' | 'br' | 'tc' | 'bc' - default: 'tr')
                style: 'notice', // ('default' | 'error' | 'notice' | 'warning' - default: 'default')
                message: 'Video "'+ (video.project_name ? video.project_name : video.id) + '" has been rendered'
            }
        );
        if(channel.page == 'videos')
        {
            setTimeout(function(){document.location.reload(true); },2000);
        }
    },
    _action_update_progress : function(data)
    {

        if(channel.page == 'videos')
        {
            jQuery('#video_progress_'+data.video+' .progress-bar').stop().animate({width:data.progress+'%'},400,'linear');
        }
    }
}
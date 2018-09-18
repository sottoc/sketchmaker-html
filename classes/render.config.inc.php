<?php
//Read config file
$cfg = parse_ini_file("config.ini",true);
if(!empty($cfg['render']) && !empty($cfg['render']['max_render_process']))
{
    defined('MAX_RENDER_PROCESS') || define('MAX_RENDER_PROCESS', $cfg['render']['max_render_process']);
}else{
    defined('MAX_RENDER_PROCESS') || define('MAX_RENDER_PROCESS', 4);
}
defined('RENDER_ROOT_DIR') || define('RENDER_ROOT_DIR', APP_PATH.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR);
defined('RENDER_PROFILES_DIR') || define('RENDER_PROFILES_DIR', APP_PATH.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'profiles'.DIRECTORY_SEPARATOR);
defined('RENDER_RENDERED_DIR') || define('RENDER_RENDERED_DIR', APP_PATH.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'rendered'.DIRECTORY_SEPARATOR);
defined('RENDER_CONVERTED_DIR') || define('RENDER_CONVERTED_DIR', APP_PATH.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'converted'.DIRECTORY_SEPARATOR);
defined('RENDER_LOCKS_DIRECTORY') || define('RENDER_LOCKS_DIRECTORY', APP_PATH.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'locks'.DIRECTORY_SEPARATOR);
defined('RENDER_LOCKS_FILENAME') || define('RENDER_LOCKS_FILENAME', 'renderer.lock');
defined('RENDER_DOWNLOAD_DIR') || define('RENDER_DOWNLOAD_DIR', '/tmp/Downloads/');
defined('RENDER_AUDIO_DIR') || define('RENDER_AUDIO_DIR', APP_PATH.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'audio'.DIRECTORY_SEPARATOR);
defined('RENDER_ENTRY_POINT_URL') || define('RENDER_ENTRY_POINT_URL', 'http://127.0.0.1/');
defined('RENDER_FPS') || define('RENDER_FPS' , 30);
defined('RENDER_BASE_URL') || define('RENDER_BASE_URL' , 'http://members.sketchmakerpro.com');
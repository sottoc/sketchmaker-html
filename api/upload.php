<?php
require '../classes/bootstrap.php';

$folder  = isset($_POST['isBack']) ? 'backgrounds' : 'user_images';
$user_dir = $user->getUserDir().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$folder;



if(isset($_POST['attachAudio']))
{
    $user_dir = $user->getUserDir().DIRECTORY_SEPARATOR.'projects'.DIRECTORY_SEPARATOR.(int)$_POST['projectId'].DIRECTORY_SEPARATOR.'mp3'.DIRECTORY_SEPARATOR;
}

if(isset($_POST['addUserAudio']))
{
    $user_dir = $user->getUserDir().DIRECTORY_SEPARATOR.'audio';
}

\Sart\FileHelper::createDirIfNotExist($user_dir);

$uploader = new Sart\UploadHandler();

// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
if(isset($_POST['attachAudio']) || isset($_POST['addUserAudio']))
{
    $uploader->allowedExtensions = array('mp3','wav'); // all files types allowed by default
}else{
    $uploader->allowedExtensions = array('jpeg','jpg','png','gif','svg'); // all files types allowed by default    
}
// Specify max file size in bytes.
$uploader->sizeLimit = null;

// Specify the input name set in the javascript.
$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default

// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
$uploader->chunksFolder = APP_PATH."/temp/chunks";

$method = get_request_method();

// This will retrieve the "intended" request method.  Normally, this is the
// actual method of the request.  Sometimes, though, the intended request method
// must be hidden in the parameters of the request.  For example, when attempting to
// delete a file using a POST request. In that case, "DELETE" will be sent along with
// the request in a "_method" parameter.
function get_request_method() {
    global $HTTP_RAW_POST_DATA;

    if(isset($HTTP_RAW_POST_DATA)) {
    	parse_str($HTTP_RAW_POST_DATA, $_POST);
    }

    if (isset($_POST["_method"]) && $_POST["_method"] != null) {
        return $_POST["_method"];
    }

    return $_SERVER["REQUEST_METHOD"];
}

if ($method == "POST") {
    header("Content-Type: text/plain");

    // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
    // For example: /myserver/handlers/endpoint.php?done
    if (isset($_GET["done"])) {
        $result = $uploader->combineChunks($user_dir);
    }
    // Handles upload requests
    else {
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $name = null;
        if(isset($_POST['audioItemId']))
        {
            $ext = '.mp3';
            if (isset($_POST['qqfilename']))
            {
                if ('misc_data' == \Sart\FileHelper::sanitize_file_name($_POST['qqfilename'])) {
                    $ext = '.wav';
                }
            }
            $name = \Sart\FileHelper::sanitize_file_name($_POST['audioItemId']).$ext;
        }
        
        $result = $uploader->handleUpload($user_dir,$name);

        // To return a name used for uploaded file you can use the following line.
        $result["uploadName"] = $uploader->getUploadName();
        if(isset($_POST['attachAudio']))
        {
            $result['url'] = $user->getUserUrl().'/projects/'.(int)$_POST['projectId'].'/mp3/'.$result['uploadName'];
            $result['duration'] = \spir1donov\RenderQueue::getVideoDuration($user->getUserDir().'/projects/'.(int)$_POST['projectId'].'/mp3/'.$result['uploadName']);
        }else{        
            $result['url'] =  $user->getUserUrl().'/images/'.$folder.'/'.$result['uploadName'];
        }
    }

    echo json_encode($result);
}
// for delete file requests
else if ($method == "DELETE") {
    //$result = $uploader->handleDelete($user_dir);
    echo json_encode($result);
}
else {
    header("HTTP/1.0 405 Method Not Allowed");
}

?>

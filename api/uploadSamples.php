<?php
require '../classes/bootstrap.php';
if(!$user->isAdmin){
    header('Location: /');
    die();
}


$dir  =APP_PATH.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'uploads';
//Clean up Dir
exec('rm -r '.$dir);
\Sart\FileHelper::createDirIfNotExist($dir);


$uploader = new Sart\UploadHandler();

$uploader->allowedExtensions = array('zip'); // all files types allowed by default


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
        $result = $uploader->combineChunks($dir);
    }
    // Handles upload requests
    else {
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $name = 'samples.zip';
        
        $result = $uploader->handleUpload($dir,$name);

        // To return a name used for uploaded file you can use the following line.
        $result["uploadName"] = $uploader->getUploadName();
        //now we need to extract file
        
        // assuming file.zip is in the same directory as the executing script.
        $file = $dir.DIRECTORY_SEPARATOR.$name;
        
        // get the absolute path to $file
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
        
        $zip = new ZipArchive;
        $res = $zip->open($file);
        if ($res === TRUE) {
            // extract it to the path we determined above
            $zip->extractTo($path);
            $zip->close();
            
            //Process hands. We do not add incremental number to file for hands
            $souce_dir = $dir.DIRECTORY_SEPARATOR.'hands';
            if(is_dir($souce_dir))
            {
                $dest_dir = APP_PATH.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'hand';
                xcopy($souce_dir,$dest_dir);
            }
            
            //Process backgrounds
            $souce_dir = $dir.DIRECTORY_SEPARATOR.'backgrounds';
            if(is_dir($souce_dir))
            {
                $dest_dir = APP_PATH.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'backgrounds';
                xcopy($souce_dir,$dest_dir,0755,false);            
            }
            
            //Process images
            $souce_dir = $dir.DIRECTORY_SEPARATOR.'images';
            if(is_dir($souce_dir))
            {            
                $dest_dir = APP_PATH.DIRECTORY_SEPARATOR.'images';
                xcopy($souce_dir,$dest_dir,0755,false);
            }
            
            //Process audio
            $souce_dir = $dir.DIRECTORY_SEPARATOR.'music';
            if(is_dir($souce_dir))
            {     
                $dest_dir = APP_PATH.DIRECTORY_SEPARATOR.'audio';
                xcopy($souce_dir,$dest_dir,0755,false);
            }

            
        } else {
            $result = [
                'error'=> 'Error during unzip of file: '.$res
            ];            
        }        
    }

    echo json_encode($result);
}
// for delete file requests
else if ($method == "DELETE") {
    $result = $uploader->handleDelete($user_dir);
    echo json_encode($result);
}
else {
    header("HTTP/1.0 405 Method Not Allowed");
}




?>

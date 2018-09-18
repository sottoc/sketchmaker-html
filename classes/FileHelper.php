<?php
namespace Sart;

class FileHelper {
    
    /**
     * Create dir if not exist
     */
    static function createDirIfNotExist($dir){
        if( !is_dir( $dir) ) {
            mkdir( $dir, 0774, true );
            chmod ( $dir , 0774 );
            //throw new CHttpException(500, "{$this->path} does not exists.");
        }
        return $dir;
    }
    
    
	/**
	 * Download file from url to local dir
	 */
	static function download_file($url,$local_dir)
	{
		$local_dir  = rtrim($local_dir,'/');
		$basename = sanitize_file_name(urldecode(basename($url)));
		$local_file = $local_dir.'/'.$basename;
		if(!file_put_contents($local_file, fopen($url, 'r')))
			return false;
		return $local_file;
	}
	
    /**
     * Show download file dialog
     * @param string file name to read
     */
    static function get_file($file)
    {
        return self::_file_force_download($file);
    }
    
    
    static function load_file($file)
    {
        if(pathinfo($file,PATHINFO_EXTENSION) == 'mp4')
        {
            return self::_file_force_download($file,'application/octet-stream',true);
        }
            return self::_file_force_download($file,false,false);
    }
    
    /**
     * Read pdf to display
     * @param string file name to read
     */
    static function file_get_pdf($file) {
        self::_file_force_download($file,'application/pdf',false);
    }
	
    
    /**
     *  Sanitize filename
     *  @param string $name
     *  @return string
     */
    static function sanitize_file_name($name)
    {
        $name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '_', $name);
        // Remove any runs of periods (thanks falstro!)
        $name = mb_ereg_replace("([\.]{2,})", '_', $name);
        return $name;
    }
    
    
    private static function _file_force_download($file, $mime = 'application/octet-stream',$download = true)
    {
        if (file_exists($file)) {
            if(!$mime)
            {
                $mime = mime_content_type($file);
            }
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // force browser to show download dialog
            if($download)
            {
                header('Content-Description: File Transfer');
            }
            header('Content-Type: '.$mime);
            if($download)
            {
                header('Content-Disposition: attachment; filename=' . basename($file));
            }
            //header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // read the file and send to the browser
            if ($fd = fopen($file, 'rb')) {
              while (!feof($fd)) {
                print fread($fd, 1024);
              }
              fclose($fd);
            }
            exit();
        }        
    }

    
    
}

<?php
require '../classes/bootstrap.php';
ob_start();
$userDir = $user->getUserDir().'/audio/';
$fileDataUser = [];
if(is_dir($userDir)){
    $fileDataUser =  fillArrayWithFileNodes( new DirectoryIterator( $userDir), $user->getUserUrl().'/audio/', true );    
}

$fileDataCommon = fillArrayWithFileNodes( new DirectoryIterator( dirname(__FILE__) .'/../audio/' ) );


function fillArrayWithFileNodes( DirectoryIterator $dir ,$base_url = '/audio/',$allow_delete = false)
{
    static $path;
    $data = array();
    $path = $base_url;
    foreach ( $dir as $node )
    {
      if (!preg_match('#^\.#', $node->getFilename())) {
        if ( $node->isDir() && !$node->isDot() )
        {
          $dir = $node->getFilename();
          $path = $base_url.$dir;
          $id = str_replace(['/','\\',' '],['_'],$path);
          //No need in subdirectories for now
          //$data['directories'][] = ['directoryName'=>ucfirst(str_replace(['_'],' ',$dir)),'path'=>$path,'id'=>$id];
          //$data[] = 
          fillArrayWithFileNodes( new DirectoryIterator( $node->getPathname() ),$base_url ,$allow_delete);
        }
        else if ( $node->isFile() )
        {
          $data[] = ['file'=>$node->getFilename(), 'path'=>$path, 'name'=> trim(str_replace(['-','.mp3'],' ',$node->getFilename()))];
        }
      }
    }
    return $data;
}

$debug = ob_get_clean();
echo json_encode([
  'result' => array_merge_recursive($fileDataUser,$fileDataCommon),
  'debug' => $debug
]);
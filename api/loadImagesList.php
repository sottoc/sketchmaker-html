<?php
include_once('../vendor/james-heinrich/phpthumb/phpThumb.config.php');
require '../classes/bootstrap.php';
ob_start();
$userDir = $user->getUserDir().'/images/';
$fileDataUser = [];
if(is_dir($userDir)){
    $fileDataUser =  fillArrayWithFileNodes( new DirectoryIterator( $userDir), $user->getUserUrl().'/images/', true );    
}

//$userBgDir = $user->getUserDir().'/backgrounds/';
//$fileDataUserBgs = [];
//if(is_dir($userBgDir)){
//    $fileDataUserBgs =  fillArrayWithFileNodes( new DirectoryIterator( $userBgDir), $user->getUserUrl().'/backgrounds/', true );    
//}

$fileDataCommon = fillArrayWithFileNodes( new DirectoryIterator( dirname(__FILE__) .'/../images/' ) );


function fillArrayWithFileNodes( DirectoryIterator $dir ,$base_url = '/images/',$allow_delete = false)
{
    static $path;
    $data = array();
    foreach ( $dir as $node )
    {
      if (!preg_match('#^\.#', $node->getFilename())) {
        if ( $node->isDir() && !$node->isDot() )
        {
          $dir = $node->getFilename();
          $path = $base_url.$dir;
          $id = str_replace(['/','\\',' '],['_'],$path);
          $data['directories'][$dir] = ['directoryName'=>ucfirst(str_replace(['_'],' ',$dir)),'path'=>$path,'id'=>$dir];
          $data['files'][$dir] = fillArrayWithFileNodes( new DirectoryIterator( $node->getPathname() ),$base_url ,$allow_delete);
        }
        else if ( $node->isFile() )
        {
          $data[] = ['thumb'=>htmlspecialchars(phpThumbURL('src='.$node->getPathname().'&w=300&bg=FFFFFF&f=png', '/vendor/james-heinrich/phpthumb/phpThumb.php')), 'file'=>$node->getFilename(), 'ext'=>strtolower($node->getExtension()), 'path'=>$path,'d'=>$allow_delete ? 'allowDelete' : ''];
        }
      }
    }
    return $data;
}

$debug = ob_get_clean();
$result = array_merge_recursive($fileDataUser,$fileDataCommon);
foreach($result['directories'] as &$dir)
{
    if(is_array($dir['directoryName']))
    {
        $dir['directoryName'] = $dir['directoryName'][0];
    }
    if(is_array($dir['id']))
    {
        $dir['id'] = $dir['id'][0];
    }    
}

//var_dump($result);

echo json_encode([
  'result' => $result,
  'debug' => $debug
]);
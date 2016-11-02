<?php
/**
 * Created by PhpStorm.
 * User: peak
 * Date: 2016/10/20
 * Time: 9:39
 */
$file = $_FILES['theFile'];
$extendName =pathinfo($_REQUEST['fileName'],PATHINFO_EXTENSION);
$fileName = md5($_REQUEST['fileName']).'.'.$extendName;
$isLastChunk = $_REQUEST['isLastChunk'];
$isFirstUpload = $_REQUEST['isFirstUpload'];
$error = $file['error'];
$fileSize = $_REQUEST['fileSize'];
$uploadPath = '../upload/tmp/';
if($error > 0){
    echo json_encode(['status'=>500]);
    exit;
}
if(!is_dir($uploadPath)){
    mkdir($uploadPath,0777);
}
switch (checkMore($fileName,$uploadPath,$fileSize)){
    case 'first':
    case 'ing':
        file_put_contents($uploadPath.$fileName,file_get_contents($_FILES['theFile']['tmp_name']),FILE_APPEND);
        break;
    case 'complete':
        exit( json_encode(['status'=>501,'msg'=>'以上传']));
        break;
    case 'error':
    default:
        $file = $filePath.$fileName;
        exit(json_encode(['status'=>502,'msg'=>'文件错误','size' => stat($file)['size']]));
}
if($isLastChunk && checkMore($fileName,$uploadPath,$fileSize) === 'complete' ){
    checkFile();
    exit(json_encode(['status'=>200]));
}
exit(json_encode(['status'=>100]));
//echo json_encode($_FILES['theFile']);
//echo file_put_contents('upload/'.$fileName, file_get_contents($_FILES['theFile']['tmp_name']), FILE_APPEND);
function checkFile(){

}

function checkMore($fileName,$filePath,$fileSize){
    clearstatcache(true,$filePath);
    $file = $filePath.$fileName;
    $size = stat($file)['size'];
    if(file_exists($filePath.$fileName)){
        if( $size == $fileSize){
            return 'complete';
        }elseif($size < $fileSize){
            return 'ing';
        }elseif($size > $fileSize){
            return 'error';
        }
    }else{
        return 'first';
    }
}
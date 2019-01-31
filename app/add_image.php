<?php
require_once('./common.php');
$token = $_GET['token'];
$secretid = $_GET['secretid'];

$status = 0;
$token = mysqli_real_escape_string($db, $token);
$secretid = mysqli_real_escape_string($db, $secretid);
$checktoken_query = mysqli_query($db,"SELECT obs_token FROM obs_list WHERE obs_token='".$token."' LIMIT 1");
#$checktoken_query = mysqli_query($db,"SELECT obs_token FROM obs_list WHERE obs_token='".$token."' AND obs_secretid='".$secretid."' LIMIT 1");

if(mysqli_num_rows($checktoken_query) == 1) {
  $data = file_get_contents('php://input');

  $filename = preg_replace('/[^A-Za-z0-9]/', '', $token);

  if (!(file_put_contents('images/'.$filename.'.jpg',$data))) {
    error_log('ADD_IMAGE : Error uploading image');
    $status = 1;
  }
  else {
    $allowedTypes = array(IMAGETYPE_JPEG);
    $detectedType = exif_imagetype('images/'.$filename.'.jpg');
      if(!array($detectedType, $allowedTypes)) {
        unlink('images/'.$filename.'.jpg');
        error_log('ADD_IMAGE : File type not supported : '. $detectedType);
        $status = 1;
      }
   }
}
else {
    error_log("ADD_IMAGE : Token : ".$token." and/or secretid : ".$secretid." do not exist.");
    $status = 1;

}
if($status != 0) {
    http_response_code(500);
}
echo json_encode(array('status'=>$status));

?>

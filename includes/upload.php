<?php 

$filename = $_SERVER["DOCUMENT_ROOT"]."/wp-load.php";
if (file_exists($filename)) {
    include_once($filename);
} else {
    include_once("../../../../wp-load.php");
}
$image_file = $_FILES['gf_aviary_file'];


//Add the allowed mime-type files to an 'allowed' array 
$allowed = array('image/x-windows-bmp', 'image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/pict', 'image/png');

//Check uploaded file type is in the above array (therefore valid)  
if(in_array($_FILES['gf_aviary_file']['type'], $allowed)){

    if($image_file['name']!=''){
         $max_file_size =  4*1024*1024;
         $file_size = intval($image_file['size']);
         if( $file_size > $max_file_size ){
             $msg = "File Size is too big.";
             $error_flag = true;
         }
         $extension = strtolower(end(explode('.', $image_file['name'])));
         $aa_options = get_option('gf_aa_options');
         $supported_files = $aa_options['supported_file_format'];
         $supported_files = strtolower($supported_files);
         if(!$error_flag && $supported_files != '' ){
           $supported_files = explode (',', $supported_files);
           if(!in_array($extension, $supported_files)){
              $msg = "No Supported file.";
              $error_flag = true;
           }
         }
         if(!$error_flag){
            $wp_upload_dir = wp_upload_dir();	  	  
            if(!is_dir($wp_upload_dir['basedir'].'/gform_aviary')){			  
                 mkdir($wp_upload_dir['basedir'].'/gform_aviary');		  
            }		  
            $upload_dir = $wp_upload_dir['basedir'].'/gform_aviary/';
            $upload_url = $wp_upload_dir['baseurl'].'/gform_aviary/';	
            $file_name = $upload_dir.$_POST['gf_aviary_field_id'].'_'.$image_file['name'];
            if(move_uploaded_file($image_file['tmp_name'], $file_name)){
                $file_url = $upload_url.$_POST['gf_aviary_field_id'].'_'.$image_file['name'];
            }
        }
        $return_obj = array('status' => 'success', 'message' => $file_url);
        echo json_encode($return_obj);
     }
} else {
    
    $return_obj = array('status' => 'error', 'message' => 'Unsupported File Type. Supported files');
    echo json_encode($return_obj);
}
?>
<?php
include_once ($_SERVER["DOCUMENT_ROOT"]."/wp-load.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/wp-admin/admin.php");
$image_file = $_FILES['gf_aa_file'];
if($image_file['name']!=''){
     $max_file_size =  4*1024*1024;
     $file_size = intval($image_file['size']);
     if( $file_size > $max_file_size ){
         $msg = "File Size is too big.";
         $error_flag = true;
     }
     $extension = end(explode('.', $image_file['name']));
     $aa_options = get_option('gf_aa_options');
     $supported_files = $aa_options['supported_file_format'];
     if(!$error_flag && $supported_files != '' ){
       $supported_files = explode (',', $supported_files);
       if(!in_array($extension, $supported_files)){
          $msg = "No Supported file.";
          $error_flag = true;
       }
     }
     if(!$error_flag){
      $uplad_dir = wp_upload_dir();
      if(!is_dir($uplad_dir['basedir'].'/gform_aviary')){
          mkdir($uplad_dir['basedir'].'/gform_aviary');
      }
      $file_name = $uplad_dir['basedir'].'/gform_aviary/'.$_POST['gf_aa_field_id'].'_'.$image_file['name'];
      if(move_uploaded_file($image_file['tmp_name'], $file_name)){
        $file_url = $uplad_dir['baseurl'].'/gform_aviary/'.$_POST['gf_aa_field_id'].'_'.$image_file['name'];
      }
    }
 }
?>
<script language="javascript">
 parent.document.getElementById('ajax_waiting_message_div').style.display='none';
<?php
if( $error_flag ){
  echo "parent.document.getElementById('gf_file_upload_error').style.display='block';";
  echo "parent.document.getElementById('btn_gf_aa_edit').style.display='none';";
  echo "parent.document.getElementById('gf_file_upload_error').innerText='".$msg."';";
}else{
    $input_field = explode('_', $_POST['gf_aa_field_id']);
    echo "parent.document.getElementById('gf_file_upload_error').style.display='none';";
    echo "parent.document.getElementById('btn_gf_aa_edit').style.display='block';";
    echo "parent.document.getElementById('aa_preview_container').innerHTML='<img id=\"gf_aa_img_preview\" width=\"'+parent.gf_aa_settings['preview_width']+'\" height=\"'+parent.gf_aa_settings['preview_height']+'\" id=\"aa_preview_image\" src=\"".$file_url."\">';";
    echo "parent.document.getElementById('".$input_field[1]."').value='".$file_url."';";
    echo "parent.launchEditor();";
}
?>
</script>
<?php
exit;
?>
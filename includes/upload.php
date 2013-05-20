<?php
include_once ($_SERVER["DOCUMENT_ROOT"]."/wp-load.php");

$image_file = $_FILES['gf_aa_file'];
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
 parent.jQuery('#ajax_waiting_message_div').hide();
 var gf_aa_settings_height = parent.gf_aa_settings['preview_height'];
 var gf_aa_settings_width = parent.gf_aa_settings['preview_width'];
 if(gf_aa_settings_height !== '') { gf_aa_settings_height = ' height="' + parent.gf_aa_settings['preview_height'] + '"'; }
 if(gf_aa_settings_width !== '') { gf_aa_settings_width = ' width="' + parent.gf_aa_settings['preview_width'] + '"'; }
<?php
if( $error_flag ){
  echo "parent.jQuery('li#field_".$_POST['gf_aa_field_id']." #gf_file_upload_error').show();";
  echo "parent.jQuery('li#field_".$_POST['gf_aa_field_id']." #btn_gf_aa_edit').hide();";
  echo "parent.jQuery('li#field_".$_POST['gf_aa_field_id']." #gf_file_upload_error').html('".$msg."');";
}else{
    echo "parent.jQuery('li#field_".$_POST['gf_aa_field_id']." #gf_file_upload_error').hide();";
    echo "parent.jQuery('li#field_".$_POST['gf_aa_field_id']." #btn_gf_aa_edit').show();";
    echo "parent.jQuery('li#field_".$_POST['gf_aa_field_id']." #aa_preview_container').html('<img id=\"gf_aa_img_preview\" '+ gf_aa_settings_width + gf_aa_settings_height + ' id=\"aa_preview_image\" src=\"".$file_url."\">');";
    echo "parent.jQuery('li#field_".$_POST['gf_aa_field_id']." #input_".$_POST['gf_aa_field_id']."').val('".$file_url."');";
    echo "parent.launchEditor();";
    echo "parent.jQuery('.gform_button').removeAttr('disabled');";
}
?>
</script>
<?php
exit;
?>
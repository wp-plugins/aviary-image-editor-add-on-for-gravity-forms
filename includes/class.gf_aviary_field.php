<?php
include_once 'sdk/instagram.class.php';
class GFAviaryField {
    var $_ig_obj = null;
    var $_gf_aa_options = null;
    var $_gf_aa_script = null;
    public function __construct(){
      $this->_gf_aa_options = get_option('gf_aa_options');
      $this->_ig_obj = new Instagram( 
        array(
          'apiKey' => $this->_gf_aa_options['ins_client_id'],
          'apiSecret' => $this->_gf_aa_options['ins_client_secret'],
          'apiCallback' => $this->_gf_aa_options['ins_redirect_uri'],
        )
      );
      $this->_settings = array(
            'preview_width' => '',
            'preview_height' => ''
        );
        add_filter( 'gform_add_field_buttons', array(&$this,'wps_add_aviary_field') );
        // Adds title to GF custom field
        add_filter( 'gform_field_type_title' , array(&$this,'wps_aviary_title') );
        // Adds the input area to the external side
        add_action( "gform_field_input" , array(&$this,"wps_aviary_field_input"), 10, 5 );
        // Now we execute some javascript technicalitites for the field to load correctly
        add_action( "gform_editor_js", array(&$this,"wps_gform_editor_js") );
        
        // Add a script to the display of the particular form only if aviary field is being used
        add_action( 'gform_enqueue_scripts' , array(&$this,'wps_gform_enqueue_scripts') , 10 , 2 );
        
        add_action('wp_enqueue_scripts', array(&$this, 'gf_aa_scripts'),20);
        
        // Add a custom class to the field li
        add_action("gform_field_css_class", array(&$this,"custom_class"), 10, 3);
        
        add_action('wp_footer', array(&$this,'gf_aa_footer'));        
        //all instagram ajax
        add_action("wp_ajax_aa_ig_ajax", array(&$this,'ig_ajax_request_handle'));
        add_action("wp_ajax_nopriv_aa_ig_ajax",  array(&$this,'ig_ajax_request_handle'));
        
        //entry-list
        add_filter("gform_entries_field_value", array(&$this, "gf_aa_entries_field_value"),10,4);
        //entry-detail
        add_filter("gform_entry_field_value", array(&$this, "gf_aa_entry_field_value"),10,4);
        
        add_filter("gform_merge_tag_filter", array(&$this, "gf_aa_merge_tag_filter"),10,5);
        
    }
    
    function gf_aa_footer(){
      ?>
      <div id="fb-root"></div>
      <?php
    }
    
    function wps_add_aviary_field( $field_groups ) {
        foreach( $field_groups as &$group ){
            if( $group["name"] == "advanced_fields" ){ // to add to the Advanced Fields
            //if( $group["name"] == "standard_fields" ){ // to add to the Standard Fields
            //if( $group["name"] == "post_fields" ){ // to add to the Standard Fields
                $group["fields"][] = array(
                    "class"=>"button",
                    "value" => __("Aviary Editor", "gravityforms"),
                    "onclick" => "StartAddField('aviary');"
                );
                break;
            }
        }
        return $field_groups;
    }


    function wps_aviary_title( $type ) {
        if ( $type == 'aviary' )
            return __( 'Aviary Editor' , 'gravityforms' );
    }


    function wps_aviary_field_input ( $input, $field, $value, $lead_id, $form_id ){
        if ( $field["type"] == "aviary" ) {
            $max_chars = "";
            if(!IS_ADMIN && !empty($field["maxLength"]) && is_numeric($field["maxLength"]))
                $max_chars = self::get_counter_script($form_id, $field_id, $field["maxLength"]);

            $input_name = $form_id .'_' . $field["id"];
            $tabindex = GFCommon::get_tabindex();
            $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
            //return sprintf("<div id='input_%s' class='%s' $tabindex></div>", $form_id.'_'.$field["id"], $css );
            return sprintf("<input name='input_%d' id='input_%s' type='hidden' value='%s' class='%s' $tabindex />", $field["id"],$form_id.'_'.$field["id"], $value, $css );
        }

        return $input;
    }


    function wps_gform_editor_js(){
    ?>
   <script type='text/javascript'>
       jQuery(document).ready(function($) {
            // from forms.js; can add custom "aviary_setting" as well
            //fieldSettings["aviary"] = ".label_setting, .description_setting, .rules_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .prepopulate_field_setting, .visibility_setting, .aviary_setting";
            fieldSettings["aviary"] = ".label_setting, .description_setting, .rules_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .conditional_logic_field_setting, .prepopulate_field_setting, .visibility_setting, .aviary_setting";
            //binding to the load field settings event to initialize the checkbox            
        });

    </script><?php
    }

    function wps_gform_enqueue_scripts( $form, $ajax ) {
        // cycle through fields to see if aviary is being used
        foreach ( $form['fields'] as $field ) {
            if ( $field['type'] == 'aviary' ) {
              if(!$this->_gf_aa_options['preview_width'] || (int)$this->_gf_aa_options['preview_width']<50 || (int)$this->_gf_aa_options['preview_width']>500)$this->_gf_aa_options['preview_width']=$this->_settings['preview_width'];
              if(!$this->_gf_aa_options['preview_height'] || (int)$this->_gf_aa_options['preview_height']<50 || (int)$this->_gf_aa_options['preview_height']>500)$this->_gf_aa_options['preview_height']=$this->_settings['preview_height'];
              $this->_gf_aa_script = "
              <script type='text/javascript'>
                gf_aa_settings = new Array();
                gf_aa_settings['id'] = '".$field['formId']."_".$field['id']."';
                gf_aa_settings['api_key'] = '".$this->_gf_aa_options['api_key']."';
                gf_aa_settings['language'] = '".$this->_gf_aa_options['language']."';
                gf_aa_settings['file_format'] = '".$this->_gf_aa_options['file_format']."';
                gf_aa_settings['supported_file_format'] = '".$this->_gf_aa_options['supported_file_format']."';
                gf_aa_settings['fb_app_id'] = '".$this->_gf_aa_options['fb_app_id']."';
                gf_aa_settings['fb_app_secret'] = '".$this->_gf_aa_options['fb_app_secret']."';
                gf_aa_settings['ig_login_url'] = '".$this->_ig_obj->getLoginUrl()."';
                gf_aa_settings['ins_client_secret'] = '".$this->_gf_aa_options['ins_client_secret']."';
                gf_aa_settings['plugin_url'] = '".plugin_dir_url(__FILE__)."';
                gf_aa_settings['ajax_url'] = '".admin_url("admin-ajax.php")."';
                gf_aa_settings['preview_disable'] = '".$this->_gf_aa_options['preview_disable']."';
                gf_aa_settings['preview_width'] = '".$this->_gf_aa_options['preview_width']."';
                gf_aa_settings['preview_height'] = '".$this->_gf_aa_options['preview_height']."';
              </script>";
              break;
            }
        }

    }

    function gf_aa_scripts(){
      wp_enqueue_script( "gform_aviary_script", plugin_dir_url(__FILE__).'js/gform-aviary.js');
      if($this->_gf_aa_script){
        wp_enqueue_style( "gform_aviary_style", plugin_dir_url(__FILE__).'css/style.css');
        wp_enqueue_style( "fancybox", plugin_dir_url(__FILE__).'fancybox/jquery.fancybox-1.3.4.css');
        wp_enqueue_script( "jquery", plugin_dir_url(__FILE__).'js/jquery.js');
        wp_enqueue_script( "aviary_script", plugin_dir_url(__FILE__).'js/feather.js');
        wp_enqueue_script( "mousewheel", plugin_dir_url(__FILE__).'fancybox/jquery.mousewheel-3.0.4.pack.js');
        wp_enqueue_script( "fancybox", plugin_dir_url(__FILE__).'fancybox/jquery.fancybox-1.3.4.pack.js');
        echo $this->_gf_aa_script;
        ?>
        <script type="text/javascript">
          gf_aa_ajax_url = '<?php echo admin_url("admin-ajax.php");?>';
          <?php
          if(isset($_GET['code'])){
          ?>
          gf_aa_auth_data = '<?php echo json_encode($this->_ig_obj->getOAuthToken($_GET['code']));?>';          
          <?php }?>
          gf_aa_set_sesion_url = '<?php echo plugin_dir_url(__FILE__); ?>'
          window.fbAsyncInit = function() {
            FB.init({
              appId      : '<?php echo $this->_gf_aa_options['fb_app_id'];?>', // App ID
              channelUrl : '', // Channel File
              status     : true, // check login status
              cookie     : true, // enable cookies to allow the server to access the session
              xfbml      : true  // parse XFBML
            });          
          };
          (function(d){
              var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
              if (d.getElementById(id)) {return;}
              js = d.createElement('script'); js.id = id; js.async = true;
              js.src = "//connect.facebook.net/en_US/all.js";
              ref.parentNode.insertBefore(js, ref);
            }(document));        
        </script>
        <?php
      }else if(isset($_GET['code'])){
        ?>
        <script type="text/javascript">
          gf_aa_ajax_url = '<?php echo admin_url("admin-ajax.php");?>';
          gf_aa_auth_data = '<?php echo json_encode($this->_ig_obj->getOAuthToken($_GET['code']));?>';
          gf_aa_set_sesion_url = '<?php echo plugin_dir_url(__FILE__); ?>'
        </script>
        <?php
      }
    }
    function custom_class($classes, $field, $form){
        if( $field["type"] == "aviary" ){
            $classes .= " gform_aviary";
        }
        return $classes;
    }
    
    function ig_ajax_request_handle(){
      switch($_POST['view']){
        case 'save_img':
            $uplad_dir_array = wp_upload_dir();
            $uplad_dir = $uplad_dir_array['basedir'].'/gform_aviary';
            if(!is_dir($uplad_dir)){
                mkdir($uplad_dir);
            }
            $extension = strtolower(end(explode('.', $_POST['url'])));
            $file_name = time().'.'.$extension;
            $file_path = $uplad_dir.'/'.$file_name;
            file_put_contents($file_path, file_get_contents($_POST['url']));
            echo json_encode(
                array(
                    'code' => 'OK',
                    'url' => $uplad_dir_array['baseurl'].'/gform_aviary/'.$file_name
                    )
            );
            break;
        case 'check_login':
          session_start();
          $data = json_decode($_SESSION['ig_user']);
          if(empty($data->user)){
            echo json_encode( array( 
              'code' => 'faild'
            ) );
          }else{
            echo json_encode( array( 
              'code' => 'OK'
            ) );
          }
          break;
        case 'get_images':
          session_start();
          $data = json_decode($_SESSION['ig_user']);
          if(isset($data->user)){
            $this->_ig_obj->setAccessToken($data);
            echo '<div class="header"><div class="profile_thumb"><img src="'.$data->user->profile_picture.'"></div>';
            echo '<div class="ig_user_name">'.$data->user->full_name.'</div><div class="right_menu" onclick="gf_ig_logout();">Log Out</div></div>';
            echo '<div class="box">';
            $medias = $this->_ig_obj->getUserMedia();
            if(count($medias->data)){
              foreach($medias->data as $image){
                echo "<div class='photo item' onclick='set_aa_editor_photo(\"".$image->images->standard_resolution->url."\");'><img src='".$image->images->thumbnail->url."'/></div>";
              }
              echo "</div>";
            }else{
              echo "<div>No Uploaded Image;</div>";
            }
            echo '</div>';
          }
          break;
        }
        exit;
    }
    
    function gf_aa_entries_field_value($value, $form_id, $field_id, $lead){
        $columns = RGFormsModel::get_grid_columns($form_id, true);
        $input_type = !empty($columns[$field_id]["inputType"]) ? $columns[$field_id]["inputType"] : $columns[$field_id]["type"];
        if($input_type=='aviary'){
            list($url, $title, $caption, $description) = rgexplode("|:|", $value, 4);
            if(!empty($url)){
                //displaying thumbnail (if file is an image) or an icon based on the extension
                $thumb = plugin_dir_url(__FILE__).'/imgs/icon_image.gif';
                $value = "<a href='" . esc_attr($url) . "' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
            }
        }
        return $value;
    }
    
    function gf_aa_entry_field_value($display_value, $field, $lead, $form){
      $input_type = !empty($field["inputType"]) ? $field["inputType"] : $field["type"];
       if($input_type=='aviary'){
            list($url, $title, $caption, $description) = rgexplode("|:|", $display_value, 4);
            if(!empty($url)){
                $display_value = "<a href='" . esc_attr($url) . "' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$url' width='100'/></a>";
            }
        }
      return $display_value;
    }
    
    function gf_aa_merge_tag_filter($value, $input_id, $match, $field, $raw_value){
      $input_type = !empty($field["inputType"]) ? $field["inputType"] : $field["type"];
      
      if($input_type=='aviary'){
      list($url, $title, $caption, $description) = rgexplode("|:|", $value, 4);
          if(!empty($url)){
              $value = "<a href='" . esc_attr($url) . "' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$url' width='100'/></a>";
          }          
      }
      return $value;
    }
} 
?>
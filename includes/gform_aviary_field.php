<?php
class GFAviaryField {
    public function __construct() {
        $this->_aviary_options = get_option('gf_aa_options');
        $this->_aviary_settings = array(
            'apiKey' => $this->_aviary_options['api_key'],
            'uploadDirectory' => $this->_aviary_options['upload_dir'],
            'theme' => $this->_aviary_options['theme'],
            'language' => $this->_aviary_options['language'],
            'fileFormat' => $this->_aviary_options['file_format'],
            'supportedFiles' => $this->_aviary_options['supported_file_format'],
            'fbAppId' => $this->_aviary_options['fb_app_id'],
            'fbAppSecret' => $this->_aviary_options['fb_app_secret'],
            'igLoginUrl' => $this->_aviary_options[''],
            'igClientSecret' => $this->_aviary_options['fb_app_secret'],
            'pluginUrl' => plugin_dir_url(__FILE__),
            'ajaxUrl' => admin_url("admin-ajax.php"),
            'previewDisabled' => $this->_aviary_options['preview_disable'],
            'previewWidth' => $this->_aviary_options['preview_width'],
            'previewHeight' => $this->_aviary_options['preview_height']
        );
        
        // Filters
        add_filter('gform_entries_field_value', array(&$this, 'gf_aviary_entries_field_value'),10,4); // Entry List
        add_filter('gform_entry_field_value', array(&$this, 'gf_aviary_entry_field_value'),10,4); // Entry Detail
        add_filter('gform_merge_tag_filter', array(&$this, 'gf_aviary_merge_tag_filter'),10,5);
        add_filter('gform_add_field_buttons', array(&$this,'wps_add_aviary_field') );
        add_filter('gform_field_type_title' , array(&$this,'wps_aviary_title') ); // Adds title to GF custom field 
        if(is_admin() && (current_user_can('editor') || current_user_can('administrator'))) {
            add_action('gform_field_input' , array(&$this,'wps_aviary_field_input'), 10, 5 );
        } else {
            add_filter('gform_field_content', array(&$this,'gf_add_field_template'), 10, 2 );
        }
            
        // Actions
        //add_action('gform_field_input' , array(&$this,'wps_aviary_field_input'), 10, 5 ); // Adds the input area to the external side
        add_action('gform_editor_js', array(&$this,'wps_gform_editor_js') ); // Now we execute some javascript technicalitites for the field to load correctly
        add_action('gform_enqueue_scripts' , array(&$this,'wps_gform_enqueue_scripts') , 10 , 2 ); // Add a script to the display of the particular form only if aviary field is being used
        add_action('gform_field_css_class', array(&$this,'gf_custom_class'), 10, 3); // Add a custom class to the field li
        add_action('wp_enqueue_scripts', array(&$this, 'wps_enqueue_scripts'),20);
        add_action('wp_footer', array(&$this,'gf_aviary_footer'));
		add_action('wp_ajax_gform_aviary_ajax', array(&$this,'gf_handle_ajax_request') );
        add_action('wp_ajax_nopriv_gform_aviary_ajax', array(&$this,'gf_handle_ajax_request') );
        //add_action('wp_ajax_aa_ig_ajax', array(&$this,'ig_ajax_request_handle')); //all instagram ajax
        //add_action('wp_ajax_nopriv_aa_ig_ajax',  array(&$this,'ig_ajax_request_handle'));
    }
    public function init_ajax_function() {
        
    }
    // Functions
    function wps_gform_enqueue_scripts( $form, $ajax ) {
        foreach ( $form['fields'] as $field ) {
            if ( $field['type'] == 'aviary' ) {
                $this->_gform_aviary = true;
                if(!$this->_aviary_options['preview_width'] || 
                   (int)$this->_aviary_options['preview_width']<50 || 
                   (int)$this->_aviary_options['preview_width']>500)
                    $this->_aviary_settings['previewWidth']=$this->_aviary_options['preview_width'] || 150;
                if(!$this->_aviary_options['preview_height'] || 
                   (int)$this->_aviary_options['preview_height']<50 || 
                   (int)$this->_aviary_options['preview_height']>500)
                    $this->_aviary_settings['previewHeight']=$this->_aviary_options['preview_height'] || 150;
              $this->_aviary_settings_script = "
              <script type='text/javascript'>
                var aviarySettings = {
                    formId: '".$field['formId']."',
                    apiKey: '".$this->_aviary_settings['apiKey']."',
                    uploadDirectory: '".$this->_aviary_settings['uploadDirectory']."',
                    theme: '".$this->_aviary_settings['theme']."',
                    language: '".$this->_aviary_settings['language']."',
                    fileFormat: '".$this->_aviary_settings['fileFormat']."',
                    supportedFiles: '".$this->_aviary_settings['supportedFiles']."',
                    fbAppId: '".$this->_aviary_settings['fbAppId']."',
                    fbAppSecret: '".$this->_aviary_settings['fbAppSecret']."',
                    igLoginUrl: '".$this->_aviary_settings['igLoginUrl']."',
                    igClientSecret: '".$this->_aviary_settings['igClientSecret']."',
                    pluginUrl: '".$this->_aviary_settings['pluginUrl']."',
                    ajaxUrl: '".$this->_aviary_settings['ajaxUrl']."',
                    previewDisabled: '".$this->_aviary_settings['previewDisabled']."',
                    previewWidth: '".$this->_aviary_settings['previewWidth']."',
                    previewHeight: '".$this->_aviary_settings['previewHeight']."'
                };
                                
			 </script>";
              break;
            }
        }
    }
    function wps_enqueue_scripts() {
        wp_enqueue_script( "gform_aviary_script", plugin_dir_url(__FILE__).'js/gform-aviary.js');
        if($this->_gform_aviary) {
            wp_enqueue_style( "gform_aviary_style", plugin_dir_url(__FILE__).'css/style.css');
            wp_enqueue_style( "fancybox", plugin_dir_url(__FILE__).'fancybox/jquery.fancybox.css');
            wp_enqueue_script( "jquery", 'http://code.jquery.com/jquery-1.11.2.min.js');
            wp_enqueue_script( "aviary_script", 'http://feather.aviary.com/imaging/v1/editor.js');
            wp_enqueue_script( "mousewheel", plugin_dir_url(__FILE__).'js/jquery.mousewheel-3.0.6.pack.js');
            wp_enqueue_script( "fancybox", plugin_dir_url(__FILE__).'fancybox/jquery.fancybox.pack.js');
            wp_enqueue_script( "lodash", plugin_dir_url(__FILE__).'js/lodash.min.js');
            echo $this->_aviary_settings_script;
        }
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
    function wps_aviary_field_input( $input, $field, $value, $lead_id, $form_id ) {
        if ( $field["type"] == "aviary" ) {
            $max_chars = "";
            if(!IS_ADMIN && !empty($field["maxLength"]) && is_numeric($field["maxLength"])){
                $max_chars = self::get_counter_script($form_id, $field_id, $field["maxLength"]);
            }
            
            $input_name = $form_id .'_'. $field["id"];
            $tabindex = GFCommon::get_tabindex();
            $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
            //return sprintf("<div id='input_%s' class='%s' $tabindex></div>", $form_id.'_'.$field["id"], $css );
            return sprintf("<input name='input_%d' id='input_%s' type='hidden' value='%s' class='%s' $tabindex />", $field["id"],$form_id.'_'.$field["id"], $value, $css );
        }

        return $input;
    }
    function gf_add_field_template($field_content, $field) {

        if ( $field['type'] == 'aviary' ) {
            $tabindex = GFCommon::get_tabindex();
            $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
            $form_field_id = '_'.$field['formId'].'_'.$field['id'];
            $required_div = $field['isRequired'] ? "*" : "";
            $post_value = 'input_' . $field['id'];
            $field_value = $_POST[$post_value];
            $field_content = '
            <div id="gf_aviary_editor' . $form_field_id . '">
                <div id="gf_file_upload_error' . $form_field_id . '"></div>
                <ul id="local-upload' . $form_field_id . '">
                    <li class="gfield">
                        <label class="gfield_label" for="input' . $form_field_id . '">' . $field['label'] . '
                        <span class="gfield_required">' . $required_div . '</span>
                        </label>
                        <input type="hidden" name="input_' . $field['id'] . '" id="input' . $form_field_id . '" class="' . $css . '" value="' . $field_value . '"/>
                        <div class="ginput_container">
                            <input type="file" class="medium ' . $css . '" name="gf_aviary_file" id="gf_aviary_file' . $form_field_id . '" data-form-field="' . $form_field_id . '" data-field-id="_' . $field['id'] . '" />
                            <div class="gfield_description">' . $field['description'] . '</div>
                        </div>
                    </li>
                    <li>
                        <div id="ajax_waiting_message_div' . $form_field_id . '" style="display: none;">
                            <img src="' . $this->_aviary_settings['pluginUrl'] . '/imgs/loading.gif" align="left" /> 
                            <label id="ajax_waiting_message' . $form_field_id . '">Loading Editor...</label> 
                        </div>
                    </li>
                    <li>
                        <div id="facebook-upload' . $form_field_id . '">
                            <div id="facebook-open' . $form_field_id . '" onclick="gf_facebook_login();"></div>
                        </div>
                        <div id="instagram-upload' . $form_field_id . '">
                            <div onclick="gf_instagram_login();" id="instagram-open' . $form_field_id . '"></div>
                        </div>
                    </li>
                    <li>
                        <ul id="aviary_preview_container' . $form_field_id . '" style="display: none;">
                            <li>
                                <img id="aviary_image' . $form_field_id . '" src="' . $field['value'] . '"/>
                            </li>
                            <li>
                                <div id="btn_gf_aviary_edit' . $form_field_id . '" class="aviary_edit_btn"> 
                                    <input type="image" data-image-id="aviary_image' . $form_field_id . '" data-image-src="#input_' . $field['id'] . '" src="' . $this->_aviary_settings['pluginUrl'] . '/imgs/edit-photo.png" value="Edit photo"/>
                                </div>                            
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>   
            ';
        }

        return $field_content;
    }
    function gf_aviary_entries_field_value($value, $form_id, $field_id, $lead) {
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
    function gf_aviary_entry_field_value($display_value, $field, $lead, $form) {
      $input_type = !empty($field["inputType"]) ? $field["inputType"] : $field["type"];
       if($input_type=='aviary'){
            list($url, $title, $caption, $description) = rgexplode("|:|", $display_value, 4);
            if(!empty($url)){
                $display_value = "<a href='" . esc_attr($url) . "' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$url' width='100'/></a>";
            }
        }
      return $display_value;
    }
    function gf_aviary_merge_tag_filter($value, $input_id, $match, $field, $raw_value) {
      $input_type = !empty($field["inputType"]) ? $field["inputType"] : $field["type"];
      
      if($input_type=='aviary'){
      list($url, $title, $caption, $description) = rgexplode("|:|", $value, 4);
          if(!empty($url)){
              $value = "<a href='" . esc_attr($url) . "' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$url' width='100'/></a>";
          }          
      }
      return $value;
    }
    function gf_custom_class($classes, $field, $form) {
        if( $field["type"] == "aviary" ){
            $classes .= " gform_aviary";
        }
        return $classes;
    }
    function gf_handle_ajax_request() {
      switch($_POST['view']){
        case 'save_image': 
          $upload_dir_array = wp_upload_dir(); 
          $custom_dir = $this->_aviary_settings['uploadDirectory'];
          if($custom_dir) { 
              $upload_dir = $upload_dir_array['basedir'].'/'.$custom_dir;
              $upload_url = $upload_dir_array['baseurl'].'/'.$custom_dir;
          } else { 
              $upload_dir = $upload_dir_array['basedir'].'/gform_aviary';
              $upload_url = $upload_dir_array['baseurl'].'/gform_aviary';
          } 
          if(!is_dir($upload_dir)) {
              mkdir($upload_dir); 
          } 
          $extension = strtolower(end(explode('.', $_POST['url'])));
          $file_name = time().'.'.$extension; 
          $file_path = $upload_dir.'/'.$file_name;
          file_put_contents($file_path, file_get_contents($_POST['url']));
          echo json_encode(
            array(
                'code' => 'OK',
                'url' => $upload_url.'/'.$file_name
                )
            );
          break;
        case 'check_login':
          session_start();
          $data = json_decode($_SESSION['ig_user']);
          if(empty($data->user)){
            echo json_encode( array( 
              'code' => 'failed'
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
    // DOM Output
    function wps_gform_editor_js(){
        ?>
        <script type='text/javascript'>
           jQuery(document).ready(function($) {
               // from forms.js; can add custom "aviary_setting" as well
               //fieldSettings["aviary"] = ".label_setting, .description_setting, .rules_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .prepopulate_field_setting, .visibility_setting, .aviary_setting";
               fieldSettings["aviary"] = ".label_setting, .description_setting, .rules_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .conditional_logic_field_setting, .prepopulate_field_setting, .visibility_setting, .aviary_setting";
               //binding to the load field settings event to initialize the checkbox
            });
        </script>
        <?php
    }
    function gf_aviary_footer(){
        ?>
        <div id="fb-root"></div>
        <?php
    }
}
?>
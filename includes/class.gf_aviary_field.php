<?php

class GFAviaryField {
    public function __construct() {
      $this->_settings = array(
            'preview_width' => '100',
            'preview_height' => '100'
        );
        add_filter( 'gform_add_field_buttons', array(&$this,'wps_add_aviary_field') );
        // Adds title to GF custom field
        add_filter( 'gform_field_type_title' , array(&$this,'wps_aviary_title') );
        // Adds the input area to the external side
        add_action( "gform_field_input" , array(&$this,"wps_aviary_field_input"), 10, 5 );
        // Now we execute some javascript technicalitites for the field to load correctly
        add_action( "gform_editor_js", array(&$this,"wps_gform_editor_js") );
        // Add a custom setting to the aviary advanced field
        add_action( "gform_field_advanced_settings" , array(&$this,"wps_aviary_settings") , 10, 2 );
        //Filter to add a new tooltip
        add_filter('gform_tooltips', array(&$this,'wps_add_aviary_tooltips'));
        // Add a script to the display of the particular form only if aviary field is being used
        add_action( 'gform_enqueue_scripts' , array(&$this,'wps_gform_enqueue_scripts') , 10 , 2 );
        // Add a custom class to the field li
        add_action("gform_field_css_class", array(&$this,"custom_class"), 10, 3);
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
            return sprintf("<input name='input_%d' id='%s' type='hidden' value='%s' class='%s' $tabindex />", $field["id"],$field["id"], $value, 'aviary-' . $css );
        }

        return $input;
    }


    function wps_gform_editor_js(){
    ?>
    <style>	 
      /* aviary styles */
      .avpw .avpw_text_input {
        -moz-box-sizing: inherit;
        -webkit-box-sizing: inherit;
        box-sizing: inherit;    
      }    
      .gf_aviary_preview_size{
        margin-left: 20px;            
      }
      #gf_aviary_api_key{
        margin-bottom: 10px;
      }
    </style>
    <script type='text/javascript'>

        jQuery(document).ready(function($) {
            //Add all textarea settings to the "aviary" field plus custom "aviary_setting"
            // fieldSettings["aviary"] = fieldSettings["textarea"] + ", .aviary_setting"; // this will show all fields that shows plus my custom setting

            // from forms.js; can add custom "aviary_setting" as well
            fieldSettings["aviary"] = ".label_setting, .description_setting, .rules_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .visibility_setting, .aviary_setting";
            //binding to the load field settings event to initialize the checkbox
            $(document).bind("gform_load_field_settings", function(event, field, form){
                var preview_width = field['aa_preview_width'];
                var preview_height = field['aa_preview_height'];
                if(!preview_width || preview_width*1<50)preview_width="<?php echo $this->_settings['preview_width']?>";
                if(!preview_height || preview_height*1<50)preview_height="<?php echo $this->_settings['preview_height']?>";
                jQuery('#gf_aviary_preview_width').val(preview_width);
                jQuery('#gf_aviary_preview_height').val(preview_height);
                jQuery('#gf_aa_preview_disable').attr('checked', field['aa_preview_disable']==true);
            });

        });

    </script><?php
    }

    function wps_aviary_settings( $position, $form_id ){
        // Create settings on position 50 (right after Field Label)
        if( $position == 50 ){
        ?>

        <li class="aviary_setting field_setting">
            <input type="checkbox" id="gf_aa_preview_disable" onclick="SetFieldProperty('aa_preview_disable', this.checked);">           
            <label for="gf_aa_preview_disable" style="display:inline;">
              <?php _e("Disable Preview", "gravityforms"); ?>
              <?php gform_tooltip("form_field_gf_aa_preview_disable"); ?>
            </label>
            <div style="margin-top: 20px;">
              <label>Preview Image Size</label>
              <div class="gf_aviary_preview_size">
                <label>width(px)</label><input type="text" id="gf_aviary_preview_width" onkeyup="SetFieldProperty('aa_preview_width', this.value);">
                <label>height(px)</label><input type="text" id="gf_aviary_preview_height" onkeyup="SetFieldProperty('aa_preview_height', this.value);">
              </div>
            </div>
        </li>
        <?php
        }
    }


    function wps_add_aviary_tooltips($tooltips){
      $tooltips["form_field_gf_aa_preview_disable"] = "<h6>Disable Preview</h6>Please check if you do not want to display preview in form.";
      return $tooltips;
    }


    function wps_gform_enqueue_scripts( $form, $ajax ) {
        // cycle through fields to see if aviary is being used
        foreach ( $form['fields'] as $field ) {
            if ( $field['type'] == 'aviary' ) {
              $aviaryUrl = plugins_url( 'feather.js', __FILE__ );
              $url = plugins_url( 'gform-aviary.js' , __FILE__ );
              if(!$field['aa_preview_width'] || $field['aa_preview_width']<50 || $field['aa_preview_width']>500)$field['aa_preview_width']=$this->_settings['preview_width'];
              if(!$field['aa_preview_height'] || $field['aa_preview_height']<50 || $field['aa_preview_height']>500)$field['aa_preview_height']=$this->_settings['preview_height'];
              $gf_aa_options = get_option('gf_aa_options');
              ?>
              <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>style.css" type="text/css">
              <script type='text/javascript' src='//code.jquery.com/jquery.min.js'></script>
              <script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
              <script type='text/javascript' src='<?php echo $url;?>'></script>
              <script type="text/javascript">
                var gf_aa_settings = new Array();
                gf_aa_settings['id'] = '<?php echo $field['formId']?>_<?php echo $field['id']?>';
                gf_aa_settings['api_key'] = '<?php echo $gf_aa_options['api_key']?>';
                gf_aa_settings['language'] = '<?php echo $gf_aa_options['language']?>';
                gf_aa_settings['file_format'] = '<?php echo $gf_aa_options['file_format']?>';
                gf_aa_settings['supported_file_format'] = '<?php echo $gf_aa_options['supported_file_format']?>';
                gf_aa_settings['plugin_url'] = '<?php echo plugin_dir_url(__FILE__);?>';
                gf_aa_settings['preview_disable'] = '<?php echo $field['aa_preview_disable']?>';
                gf_aa_settings['preview_width'] = '<?php echo $field['aa_preview_width']?>';
                gf_aa_settings['preview_height'] = '<?php echo $field['aa_preview_height']?>';
              </script>
              
              <?php
              break;
            }
        }

    }

    function custom_class($classes, $field, $form){
        if( $field["type"] == "aviary" ){
            $classes .= " gform_aviary";
        }
        return $classes;
    }
}    
?>
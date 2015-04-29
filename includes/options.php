<?php
if($_POST['commit']=='save'){
    update_option('gf_aa_options', $_POST['gf_aa_options']);
}
$options = get_option('gf_aa_options');

?>
<h2><?php _e('Aviary Settings');?></h2>
<div>
    
    <form action="" method="POST">
        <div style="width: 50%;">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e('Avairy API Key', 'gravityforms');?></th>
                        <td><input type="text" name="gf_aa_options[api_key]" value="<?php echo $options['api_key'];?>" size="40"> (To get your api key simply <a href="http://www.aviary.com/web-key" target="_blank">register here</a> for free)</td>
                    </tr>					
                    <tr valign="top">                        
                        <th scope="row"><?php _e('Custom Upload Directory', 'gravityforms');?></th> 
                        <td><input type="text" name="gf_aa_options[upload_dir]" value="<?php echo $options['upload_dir'];?>" size="40"> Add a custom upload directory here. Or leave blank for default (uploads/gform_aviary).</td>                    
                    </tr>
                    <tr valign="top">                        
                        <th scope="row"><?php _e('Editor Theme', 'gravityforms');?></th> 
                        <td>
                        <?php
                            $themes = array(
                              'dark' => 'Dark (Default)',
                              'light' => 'Light'
                            );

                            echo "<select name='gf_aa_options[theme]'>";
                            foreach ($themes as $key => $val) {
                                $selected = ( $options['theme'] === $key ) ? 'selected = "selected"' : '';
                                echo "<option value='$key' $selected>$val</option>";
                            }
                            echo "</select>";
                          ?>
                        </td>                    
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Editor Language', 'gravityforms');?></th>
                        <td>
                          <?php
                            $items = array(
                              'en' => 'English (default)',
                              'ca' => ' Catalan',
                              'zh_HANS' => 'Chinese (simplified)',
                              'zh_HANT' => 'Chinese (traditional)',
                              'nl' => 'Dutch',
                              'fr' => 'French',
                              'de' => 'German',
                              'he' => 'Hebrew',
                              'id' => 'Indonesian',
                              'it' => 'Italian',
                              'ja' => 'Japanese',
                              'ko' => 'Korean',
                              'lv' => 'Latvian',
                              'lt' => 'Lithuanian',
                              'pl' => 'Polish',
                              'pt' => ' Portuguese',
                              'pt_BR' => 'Portuguese (Brazilian)',
                              'ru' => 'Russian',
                              'es' => 'Spanish',
                              'vi' => 'Vietnamese'
                            );

                            echo "<select name='gf_aa_options[language]'>";
                            foreach ($items as $key => $val) {
                                $selected = ( $options['language'] === $key ) ? 'selected = "selected"' : '';
                                echo "<option value='$key' $selected>$val</option>";
                            }
                            echo "</select>";
                          ?>
                        </td>
                    </tr>
                     <tr valign="top">
                        <th scope="row"><?php _e('Saved File Format', 'gravityforms');?></th>
                        <td>
                          <?php
                            $items = array(
                                'original' => 'original',
                                'png' => 'PNG',
                                'jpg' => 'JPG'                                
                              );

                            echo "<select name='gf_aa_options[file_format]'>";
                            foreach ($items as $key => $val) {
                                $selected = ( $options['file_format'] === $key ) ? 'selected = "selected"' : '';
                                echo "<option value='$key' $selected>$val</option>";
                            }
                            echo "</select>";
                          ?>
                        </td>                        
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Supported File Format', 'gravityforms');?></th>
                        <td><input type="text" name="gf_aa_options[supported_file_format]" value="<?php echo $options['supported_file_format'];?>" size="40"> (Separated with comma. ie: jpg,png,gif)</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Disable Preview', 'gravityforms');?></th>
                        <td><input type="checkbox" name="gf_aa_options[preview_disable]" value="1" <?php if($options['preview_disable'])echo "checked";?>> (Please check if you do not want to display preview in form.)</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Preview Width', 'gravityforms');?></th>
                        <td><input type="text" name="gf_aa_options[preview_width]" value="<?php echo $options['preview_width'];?>" > add px/%</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Preview Height', 'gravityforms');?></th>
                        <td><input type="text" name="gf_aa_options[preview_height]" value="<?php echo $options['preview_height'];?>" >add px/%</td>
                    </tr>
                    <tr valign="top"><td colspan="2">
                        <strong>IMPORTANT: This plugin has be heavily refactored since version 2.3 and the following options were not included in the refactoring process or tested. They may not work as expected or at all at this point. I'll be refactoring these soon.</strong>
                        </td></tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Facebook Application ID');?></th>
                        <td><input type="text" name="gf_aa_options[fb_app_id]" value="<?php echo $options['fb_app_id'];?>" size="40"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Facebook Application Secret');?></th>
                        <td><input type="text" name="gf_aa_options[fb_app_secret]" value="<?php echo $options['fb_app_secret'];?>" size="40"></td>
                    </tr>
                    <tr valign="top"><td colspan="2"></td></tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Instagram Client ID');?></th>
                        <td><input type="text" name="gf_aa_options[ins_client_id]" value="<?php echo $options['ins_client_id'];?>" size="40"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Instagram Client Secret');?></th>
                        <td><input type="text" name="gf_aa_options[ins_client_secret]" value="<?php echo $options['ins_client_secret'];?>" size="40"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Instagram REDIRECT URI');?></th>
                        <td><input type="text" name="gf_aa_options[ins_redirect_uri]" value="<?php echo $options['ins_redirect_uri'];?>" size="40"></td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" style="margin-top: 50px;float:right;" class="button-primary" name="commit" value="save" />
        </div>
        </form>
</div>

<div style="clear: both;">
<h3>This plugin is proudly presented to you by:</h3>
	<a href="http://netherworks.com">
		<img align="left" title="Imagination In The Works!" alt="NetherWorks, LLC - Official Site" src="http://netherworks.com/wp-content/uploads/2012/10/NetherWorks_Logo_150x150.jpg">
	</a>
	<div style="width: 225px; float: left;">
		<center>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="Q2P8VVQ2U54DY">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</center>
		<h4 style="margin: 10px; text-align: center;"><em>"Many Hands Make Light Work".</em> <br><strong>Please Consider Donating!</strong></h4>
	</div>
	<div style="width: 225px; float: left;"><a href="http://netherworks.com/gravityforms" target="_blank">Upgrade License</a>
	<a href="http://netherworks.com/gravityforms" title="Gravity Forms Plugin for WordPress" target="_blank"><img src="http://gravityforms.s3.amazonaws.com/banners/140x140.gif" alt="Gravity Forms Contact Form Plugin for WordPress" width="140" height="140" style="border:none;" /></a>
	</div>
</div>
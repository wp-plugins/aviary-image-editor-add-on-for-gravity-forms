<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if($_POST['commit']=='save settings'){
    update_option('gf_aa_options', $_POST['gf_aa_options']);
}
$options = get_option('gf_aa_options');
?>
<h2><?php _e('Aviary Settings');?></h2>
<div>
    <p><?php _e('please select your country. This is needed for correct displaying google map.');?></p>
    <form action="" method="POST">
        <div style="width: 50%;">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e('Avairy API Key');?></th>
                        <td><input type="text" name="gf_aa_options[api_key]" value="<?php echo $options['api_key'];?>" size="40"> (To get your api key simply <a href="http://www.aviary.com/web-key" target="_blank">register here</a> for free)</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Editor Language');?></th>
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
                        <th scope="row"><?php _e('Saved File Format');?></th>
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
                        <th scope="row"><?php _e('Supported File Format');?></th>
                        <td><input type="text" name="gf_aa_options[supported_file_format]" value="<?php echo $options['supported_file_format'];?>" size="40"> (Separated with comma. ie: jpg,png,gif)</td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" style="margin-top: 50px;float:right;" class="button-primary" name="commit" value="save settings" />
        </div>
        </form>
</div>


<?php

?>
























































































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


var gf_featherEditor = null;
jQuery(document).ready(function(){
  draw_aa_editor();
  jQuery('#gf_aa_file').on('change',function(){
    jQuery('#ajax_waiting_message_div').show();
    jQuery('#gf_aa_editor form').submit();
  });
  
  
  gf_featherEditor = new Aviary.Feather({
    apiKey: gf_aa_settings['api_key'],
    apiVersion: 3,
    tools: 'all',
    appendTo: '',
    fileFormat: gf_aa_settings['file_format']!=''? gf_aa_settings['file_format'] : 'original',
    language: gf_aa_settings['language']!=''? gf_aa_settings['language'] : 'en',
    onSave: function(imageID, newURL) {
        var img = document.getElementById(imageID);
        img.src = newURL;
        var field_id = gf_aa_settings['id'].split('_');
        var src = jQuery('input#'+field_id[1]).val(newURL);
    },
    onError: function(errorObj) {
          alert(errorObj.message);
    }
});

});
function draw_aa_editor(){
  var aa_editor = jQuery('<div id="gf_aa_editor"></div>');
  aa_editor.append('<form action="'+gf_aa_settings['plugin_url']+'upload.php" target="file_upload_target" enctype="multipart/form-data" method="post"></form><iframe id="file_upload_target" name="file_upload_target"></iframe>');
  aa_editor.find('form').append('<input type="file" name="gf_aa_file" id="gf_aa_file"><input type="hidden" name="gf_aa_field_id" value="'+gf_aa_settings['id']+'">');
  aa_editor.find('form').append('<div id="ajax_waiting_message_div"><img src="'+gf_aa_settings['plugin_url']+'loading.gif"><label id="ajax_waiting_message">Wait a second...</label></div>');
  aa_editor.find('form').before('<div id="gf_file_upload_error"></div>');
  aa_editor.append("<div id='aa_preview_container'></div><div id='btn_gf_aa_edit'><input type='image' onclick='launchEditor();return false;' src='"+gf_aa_settings['plugin_url']+"edit-photo.png' value='Edit photo'/></div>");
  if(!jQuery('#gf_aa_editor').size()){
   jQuery('#field_'+gf_aa_settings['id']).append(aa_editor);   
  }  
  if(gf_aa_settings['preview_disable']){
    jQuery('#aa_preview_container').hide();
  }
}

function launchEditor() {
    var field_id = gf_aa_settings['id'].split('_');
    var id = 'gf_aa_img_preview';
    var src = jQuery('input#'+field_id[1]).val()
    gf_featherEditor.launch({
        image: id,
        url: src
    });
    return false;
}


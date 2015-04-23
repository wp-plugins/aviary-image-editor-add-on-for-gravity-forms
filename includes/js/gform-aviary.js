var gf_featherEditor = null;
var gf_aa_settings;
var gf_aa_fbuser;
var gf_aa_fb_access_token;
var gf_aa_ajax_url;
var gf_aa_auth_data;
var gf_submit_btn_id;

jQuery(document).ready(function(){
  if(gf_aa_auth_data && gf_aa_auth_data!==''){
    set_ig_auth_data();
    return;
  }
  if(gf_aa_settings){
    draw_aa_editor();
    jQuery('#gf_aa_file').on('change',function(){
      jQuery('#ajax_waiting_message_div').show();
      var form_id = gf_aa_settings['id'].split('_');
      gf_submit_btn_id = jQuery('#gform_'+form_id[0]+' :submit').attr('id');
      jQuery('#gform_'+form_id[0]+' :submit').attr('id','');
      jQuery('#gf_aa_editor #local-upload form').submit();      
    });

    gf_featherEditor = new Aviary.Feather({
      apiKey: gf_aa_settings['api_key'],
      apiVersion: 3,
      tools: 'all',
      appendTo: '',
      fileFormat: gf_aa_settings['file_format']!==''? gf_aa_settings['file_format'] : 'original',
      language: gf_aa_settings['language']!==''? gf_aa_settings['language'] : 'en',
      onSave: function(imageID, newURL){
          var img = document.getElementById(imageID), data = "action=aa_ig_ajax&view=save_img&url="+newURL;
          jQuery.ajax({
                url: gf_aa_ajax_url, 
                type: 'POST',
                data: data, 
                dataType: 'json',
                success: function( response ){
                    if(response.code==='OK'){
                        img.src = response.url;
                        jQuery('#input_'+gf_aa_settings['id']).val(response.url);
                    }
                }						
          });
         
      },
      onError: function(errorObj){
            alert(errorObj.message);
      }
  });
  }
 
  
});
function draw_aa_editor(){

  var aa_editor = jQuery('<div id="gf_aa_editor"></div>');
  var local_editor = jQuery('<div id="local-upload"><div id="tab_from_local"></div>');
  local_editor.append('<form action="'+gf_aa_settings['plugin_url']+'upload.php" target="gf_aa_target_iframe" enctype="multipart/form-data" method="post"></form><iframe id="gf_aa_target_iframe" name="gf_aa_target_iframe"></iframe>');
  local_editor.find('form').append('<input type="file" name="gf_aa_file" id="gf_aa_file"><input type="hidden" name="gf_aa_field_id" value="'+gf_aa_settings['id']+'">');
  local_editor.find('form').append('<div id="ajax_waiting_message_div"><img src="'+gf_aa_settings['plugin_url']+'/imgs/loading.gif"><label id="ajax_waiting_message">Wait a second...</label></div></div>');
  local_editor.find('form').before('<div id="gf_file_upload_error"></div>');
  var facebook_editor = jQuery('<div id="facebook-upload"><div id="facebook-open" onclick="gf_facebook_login();"></div></div>');
  var instagram_editor = jQuery('<div id="instagram-upload"><div onclick="gf_instagram_login();" id="instagram-open"></div></div>');
  
  // aa_editor.append(tab);
  aa_editor.append(local_editor);
  if(gf_aa_settings['fb_app_id']){
  aa_editor.append(facebook_editor);
  }
  if(gf_aa_settings['ins_client_secret']){
  aa_editor.append(instagram_editor);
  }
  aa_editor.append("<div id='aa_preview_container'></div><div id='btn_gf_aa_edit'><input type='image' onclick='launchEditor();return false;' src='"+gf_aa_settings['plugin_url']+"/imgs/edit-photo.png' value='Edit photo'/></div>");
  aa_editor.append('<div style="display:none;" id="gf_aa_images"></div>');
  if(!jQuery('#gf_aa_editor').size()){
   jQuery('#field_'+gf_aa_settings['id']).append(aa_editor);   
  }  
  if(gf_aa_settings['preview_disable']){
    jQuery('#aa_preview_container').hide();
  }
}

function launchEditor(){
    var form_id = gf_aa_settings['id'].split('_');
    jQuery('#gform_'+form_id[0]+' :submit').attr('id',gf_submit_btn_id);
    var id = 'gf_aa_img_preview';
    var src = jQuery('#input_'+gf_aa_settings['id']).val();
    gf_featherEditor.launch({
        image: id,
        url: src
    });
    return false;
}


function gf_facebook_login(){
    FB.login(function(response){
        if (response.status==="connected"){
          gf_aa_fb_access_token = response.authResponse.accessToken;
          FB.api('/'+FB.getUserID(), function(response){
            if(response){
              gf_aa_fbuser = response;
              show_fbimage_window();              
            }            
          });            
        }
    },{scope: 'user_photos,friends_photos'});
}
function show_fbimage_window(){
  var $container = jQuery('#gf_aa_images');
  $container.html('<div id="fb_img_container"><div class="header"></div><div class="box"></div></div>');
  jQuery.fancybox($container.html());
  jQuery('#fb_img_container .header').append('<div class="profile_thumb"><img src="http://graph.facebook.com/'+gf_aa_fbuser.id+'/picture"></div>');
  FB.api('/'+gf_aa_fbuser.id+'/friends',function(response){
    if(response['data'].length>0){
      var friends = '<div class="fb_friends"><select onchange="get_fb_albums();">';
      friends += '<option value="'+gf_aa_fbuser.id+'" profile_thunb="http://graph.facebook.com/'+gf_aa_fbuser.id+'/picture">'+gf_aa_fbuser.name+'(me)</option>';
      friends +="<optgroup label='My Friends'>";
      for(var i=0; i<response.data.length; i++){
         friends += '<option value="'+response.data[i].id+'" profile_thunb="http://graph.facebook.com/'+response.data[i].id+'/picture">'+response.data[i].name+'</option>';
      }
      friends += '</optgroup></select></div>';
      friends += '<div class="right_menu"><div class="go_to_album" onclick="get_fb_albums()">&#8592;&nbsp;Go To Albums</div><div onclick="gf_fb_logout();" class="gf_fb_logout">Log Out</div></div>';
      jQuery('#fb_img_container .header').append(friends);
    }
    get_fb_albums();
  });  
}
function gf_instagram_login(){
  var data = "action=aa_ig_ajax&view=check_login";
  jQuery.ajax({
      url: gf_aa_ajax_url, 
      type: 'POST',
      data: data, 
      dataType: 'json',
      success: function( response ){
          if(response.code==='OK'){
              show_igimage_window();
          }else{
              var x = screen.width/2 - 700/2;
              var y = screen.height/2 - 450/2;
              window.open(gf_aa_settings['ig_login_url'], "ig_login_window", "location=1,status=0,scrollbars=1, width=700,height=450,left="+x+",top="+y);
          }
      }						
  });   
}
function get_fb_albums(){
  if(jQuery('#fancybox-content .fb_friends select option:selected').attr('profile_thunb')){
    jQuery('#fancybox-content .profile_thumb img').attr('src', jQuery('#fancybox-content .fb_friends select option:selected').attr('profile_thunb'));
  }
  FB.api('/'+jQuery('#fancybox-content #fb_img_container .fb_friends select').val()+'/albums', function(response){
    var albums = '';
    for(var i=0; i<response.data.length; i++){
        albums += "<div class='album item' onclick='get_fb_photos("+response.data[i].id+");'><div class='cover_image'><img src='https://graph.facebook.com/"+response.data[i].cover_photo+"/picture?type=album&access_token="+gf_aa_fb_access_token+"'/></div><div class='title'>"+response.data[i].name+"("+response.data[i].count+")</div></div>";
    }
    jQuery('#fb_img_container .box').html(albums);    
  });
  jQuery('.go_to_album').hide();
}

function get_fb_photos(album){
  FB.api('/'+album+'/photos?limit=1000', function(response){
    var albums = '';
    for(var i=0; i<response.data.length; i++){
        albums += "<div class='photo item' onclick='set_aa_editor_photo(\""+response.data[i].source+"\");'><img src='"+response.data[i].picture+"'/></div>";
    }
    jQuery('#fb_img_container .box').html(albums);
  });
  jQuery('.go_to_album').show();
}


function set_aa_editor_photo(image){
  var gf_aa_settings_height = gf_aa_settings['preview_height'];
  var gf_aa_settings_width = gf_aa_settings['preview_width'];
  if(gf_aa_settings_height !== '' && parseInt(preview_height)> 10){ gf_aa_settings_height = ' height="' + gf_aa_settings['preview_height'] + '"'; }
  if(gf_aa_settings_width !== ''){ gf_aa_settings_width = ' width="' + gf_aa_settings['preview_width'] + '"'; }
  jQuery('li#field_'+gf_aa_settings['id']+' #aa_preview_container').html('<img id="gf_aa_img_preview"' + gf_aa_settings_width + gf_aa_settings_height + ' src="'+image+'">');
  jQuery('li#field_'+gf_aa_settings['id']+' #input_'+gf_aa_settings['id']).val(image);
  jQuery('li#field_'+gf_aa_settings['id']+' #btn_gf_aa_edit').show();
  jQuery.fancybox.close();
  launchEditor();
}

function gf_fb_logout(){
   window.location = "https://www.facebook.com/logout.php?confirm=1&api_key="+gf_aa_settings['fb_app_id']+"&next=" +
                         location.href +
                         "&access_token=" + gf_aa_fb_access_token;
}
function check_ig_loginstatus(){
  var data = "action=aa_ig_ajax&view=check_login";
  jQuery.ajax({
      url: gf_aa_ajax_url, 
      type: 'POST',
      data: data, 
      dataType: 'html',
      success: function( response ){
          if(response.code==='OK'){
              return true;
          }else{
             return false;
          }
      }						
  });
}

function set_ig_auth_data(){
  jQuery.ajax({
      url: gf_aa_set_sesion_url+'set_session.php', 
      type: 'POST',
      data: 'action=set&ig_user='+gf_aa_auth_data, 
      dataType: 'json',
      success: function( response ){
        window.opener.show_igimage_window();
        window.close();
      }						
  });  
}
function show_igimage_window(){
  var $container = jQuery('#gf_aa_images');
  $container.html('<div id="ig_img_container"></div>');
  jQuery.ajax({
    url: gf_aa_ajax_url, 
      type: 'POST',
      data: 'action=aa_ig_ajax&view=get_images', 
      dataType: 'html',
      success: function( response ){
        console.log(response);
          jQuery('#ig_img_container').append(response);
          jQuery.fancybox($container.html());  
      }	
  });
 
}

function gf_ig_logout(){
  jQuery.ajax({
      url: gf_aa_set_sesion_url+'set_session.php', 
      type: 'POST',
      data: 'action=del', 
      dataType: 'json',
      success: function( response ){
        if(response.code==='OK'){
          window.open(gf_aa_set_sesion_url+'ig_logout.php');
          jQuery.fancybox.close();          
        }
      }						
  });
}


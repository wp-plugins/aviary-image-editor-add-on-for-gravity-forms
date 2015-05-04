(function () {
    'use strict';
    jQuery(document).ready(function ($) {
        var fieldId, formFieldId, gf_featherEditor;

        function initializeEditor() {
            gf_featherEditor = new Aviary.Feather({
                apiKey: aviarySettings.apiKey,
                theme: aviarySettings.theme,
                tools: 'all',
                appendTo: '',
                fileFormat: aviarySettings.fileFormat !== '' ? aviarySettings.fileFormat : 'original',
                language: aviarySettings.language !== '' ? aviarySettings.language : 'en',
                onSave: function (imageID, newURL) {
                    var img = imageID,
                        data = "action=gform_aviary_ajax&view=save_image&url=" + newURL;
                    $.ajax({
                        url: aviarySettings.ajaxUrl,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            $('#ajax_waiting_message_div' + formFieldId).hide();
                            if (response.code === 'OK') {
                                $('#aviary_image' + formFieldId).attr('src', response.url).css({
                                    'maxHeight': aviarySettings.previewHeight,
                                    'maxWidth': aviarySettings.previewWidth
                                });
                                $('#input' + formFieldId).val(response.url);
                            }
                        }
                    });
                },
                onError: function (errorObj) {
                    alert(errorObj.message);
                }
            });
        }

        function launchEditor(imageId, imageSrc) {
            gf_featherEditor.launch({
                image: imageId,
                url: imageSrc
            });
            return false;
        }

        function refreshPreviews() {
            $('.gform_aviary div ul li>input[type=hidden]').each(function () {
                formFieldId = $(this).parent().find('.ginput_container input[type=file]').attr('data-form-field');
                if ($(this).val()) {
                    $('#aviary_preview_container' + formFieldId).show();
                    $('#btn_gf_aviary_edit' + formFieldId).hide();
                    $('#aviary_image' + formFieldId).attr('src', $(this).val()).css({
                        'maxHeight': aviarySettings.previewHeight,
                        'maxWidth': aviarySettings.previewWidth
                    });
                }
                if (aviarySettings.previewDisabled === true) {
                    $('#aviary_preview_container' + formFieldId).hide();
                }
            });
        }

        function detectChanges() {
            if (aviarySettings) {
                $('.gform_aviary input[type=file]').on('change', function (event) {
                    fieldId = $(this).attr('data-field-id');
                    formFieldId = $(this).attr('data-form-field');
                    $('#ajax_waiting_message_div' + formFieldId).show();
                    $('#aviary_preview_container' + formFieldId).show();
                    var imageId = 'gf_aviary_img_preview' + formFieldId;
                    var imageSrc = $('#gf_aviary_file' + formFieldId);
                    var data = new FormData();
                    data.append('gf_aviary_file', $(this).prop('files')[0]);
                    $.ajax({
                        url: aviarySettings.pluginUrl + 'upload.php',
                        type: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (data) {
                            var response = $.parseJSON(data);
                            $('#ajax_waiting_message_div' + formFieldId).hide();
                            if (response['status'] === 'success') {
                                $('#aviary_image' + formFieldId).attr('src', response.message).css({
                                    'maxHeight': aviarySettings.previewHeight,
                                    'maxWidth': aviarySettings.previewWidth
                                });
                                $('#input' + formFieldId).val(response.message);
                                launchEditor('aviary_image' + formFieldId, response.message);
                            }
                        }
                    });
                });
            }
            $('.aviary_edit_btn input').on('click', function (e) {
                e.preventDefault();
                var imgId = $(this).attr('data-image-id');
                var imgSrc = $(this).attr('data-image-src');
                var imgUrl = $(imgSrc).val();
                return launchEditor(imgId, imgUrl);
            });
        }
        $(document).bind('gform_post_render', function (event, form_id, current_page) {
            refreshPreviews();
            initializeEditor();
            detectChanges();
        });
    });
    // Old stuff that needs refactoring
    function gf_facebook_login() {
        FB.login(function (response) {
            if (response.status === "connected") {
                gf_aa_fb_access_token = response.authResponse.accessToken;
                FB.api('/' + FB.getUserID(), function (response) {
                    if (response) {
                        gf_aa_fbuser = response;
                        show_fbimage_window();
                    }
                });
            }
        }, {
            scope: 'user_photos,friends_photos'
        });
    }

    function show_fbimage_window() {
        var $container = jQuery('#gf_aa_images');
        $container.html('<div id="fb_img_container"><div class="header"></div><div class="box"></div></div>');
        jQuery.fancybox($container.html());
        jQuery('#fb_img_container .header').append('<div class="profile_thumb"><img src="http://graph.facebook.com/' + gf_aa_fbuser.id + '/picture"></div>');
        FB.api('/' + gf_aa_fbuser.id + '/friends', function (response) {
            if (response['data'].length > 0) {
                var friends = '<div class="fb_friends"><select onchange="get_fb_albums();">';
                friends += '<option value="' + gf_aa_fbuser.id + '" profile_thunb="http://graph.facebook.com/' + gf_aa_fbuser.id + '/picture">' + gf_aa_fbuser.name + '(me)</option>';
                friends += "<optgroup label='My Friends'>";
                for (var i = 0; i < response.data.length; i++) {
                    friends += '<option value="' + response.data[i].id + '" profile_thunb="http://graph.facebook.com/' + response.data[i].id + '/picture">' + response.data[i].name + '</option>';
                }
                friends += '</optgroup></select></div>';
                friends += '<div class="right_menu"><div class="go_to_album" onclick="get_fb_albums()">&#8592;&nbsp;Go To Albums</div><div onclick="gf_fb_logout();" class="gf_fb_logout">Log Out</div></div>';
                jQuery('#fb_img_container .header').append(friends);
            }
            get_fb_albums();
        });
    }

    function gf_instagram_login() {
        var data = "action=aa_ig_ajax&view=check_login";
        jQuery.ajax({
            url: gf_aa_ajax_url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.code === 'OK') {
                    show_igimage_window();
                } else {
                    var x = screen.width / 2 - 700 / 2;
                    var y = screen.height / 2 - 450 / 2;
                    window.open(gf_aa_settings['ig_login_url'], "ig_login_window", "location=1,status=0,scrollbars=1, width=700,height=450,left=" + x + ",top=" + y);
                }
            }
        });
    }

    function get_fb_albums() {
        if (jQuery('#fancybox-content .fb_friends select option:selected').attr('profile_thunb')) {
            jQuery('#fancybox-content .profile_thumb img').attr('src', jQuery('#fancybox-content .fb_friends select option:selected').attr('profile_thunb'));
        }
        FB.api('/' + jQuery('#fancybox-content #fb_img_container .fb_friends select').val() + '/albums', function (response) {
            var albums = '';
            for (var i = 0; i < response.data.length; i++) {
                albums += "<div class='album item' onclick='get_fb_photos(" + response.data[i].id + ");'><div class='cover_image'><img src='https://graph.facebook.com/" + response.data[i].cover_photo + "/picture?type=album&access_token=" + gf_aa_fb_access_token + "'/></div><div class='title'>" + response.data[i].name + "(" + response.data[i].count + ")</div></div>";
            }
            jQuery('#fb_img_container .box').html(albums);
        });
        jQuery('.go_to_album').hide();
    }

    function get_fb_photos(album) {
        FB.api('/' + album + '/photos?limit=1000', function (response) {
            var albums = '';
            for (var i = 0; i < response.data.length; i++) {
                albums += "<div class='photo item' onclick='set_aa_editor_photo(\"" + response.data[i].source + "\");'><img src='" + response.data[i].picture + "'/></div>";
            }
            jQuery('#fb_img_container .box').html(albums);
        });
        jQuery('.go_to_album').show();
    }

    function set_aa_editor_photo(image) {
        var gf_aa_settings_height = gf_aa_settings['preview_height'];
        var gf_aa_settings_width = gf_aa_settings['preview_width'];
        if (gf_aa_settings_height !== '' && parseInt(preview_height) > 10) {
            gf_aa_settings_height = ' height="' + gf_aa_settings['preview_height'] + '"';
        }
        if (gf_aa_settings_width !== '') {
            gf_aa_settings_width = ' width="' + gf_aa_settings['preview_width'] + '"';
        }
        jQuery('li#field_' + gf_aa_settings['id'] + ' #aa_preview_container').html('<img id="gf_aa_img_preview"' + gf_aa_settings_width + gf_aa_settings_height + ' src="' + image + '">');
        jQuery('li#field_' + gf_aa_settings['id'] + ' #input_' + gf_aa_settings['id']).val(image);
        jQuery('li#field_' + gf_aa_settings['id'] + ' #btn_gf_aa_edit').show();
        jQuery.fancybox.close();
        launchEditor();
    }

    function gf_fb_logout() {
        window.location = "https://www.facebook.com/logout.php?confirm=1&api_key=" + gf_aa_settings['fb_app_id'] + "&next=" + location.href + "&access_token=" + gf_aa_fb_access_token;
    }

    function check_ig_loginstatus() {
        var data = "action=aa_ig_ajax&view=check_login";
        jQuery.ajax({
            url: gf_aa_ajax_url,
            type: 'POST',
            data: data,
            dataType: 'html',
            success: function (response) {
                if (response.code === 'OK') {
                    return true;
                } else {
                    return false;
                }
            }
        });
    }

    function set_ig_auth_data() {
        jQuery.ajax({
            url: gf_aa_set_sesion_url + 'set_session.php',
            type: 'POST',
            data: 'action=set&ig_user=' + gf_aa_auth_data,
            dataType: 'json',
            success: function (response) {
                window.opener.show_igimage_window();
                window.close();
            }
        });
    }

    function show_igimage_window() {
        var $container = jQuery('#gf_aa_images');
        $container.html('<div id="ig_img_container"></div>');
        jQuery.ajax({
            url: gf_aa_ajax_url,
            type: 'POST',
            data: 'action=aa_ig_ajax&view=get_images',
            dataType: 'html',
            success: function (response) {
                console.log(response);
                jQuery('#ig_img_container').append(response);
                jQuery.fancybox($container.html());
            }
        });
    }

    function gf_ig_logout() {
        jQuery.ajax({
            url: gf_aa_set_sesion_url + 'set_session.php',
            type: 'POST',
            data: 'action=del',
            dataType: 'json',
            success: function (response) {
                if (response.code === 'OK') {
                    window.open(gf_aa_set_sesion_url + 'ig_logout.php');
                    jQuery.fancybox.close();
                }
            }
        });
    }
}());
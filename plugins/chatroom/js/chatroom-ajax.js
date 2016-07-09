
/*jshint browser:true */
/*!
* FitVids 1.1
*
* Copyright 2013, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
* Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
* Released under the WTFPL license - http://sam.zoy.org/wtfpl/
*
*/

;(function( $ ){

  'use strict';

  $.fn.fitVids = function( options ) {
    var settings = {
      customSelector: null,
      ignore: null
    };

    if(!document.getElementById('fit-vids-style')) {
      // appendStyles: https://github.com/toddmotto/fluidvids/blob/master/dist/fluidvids.js
      var head = document.head || document.getElementsByTagName('head')[0];
      var css = '.fluid-width-video-wrapper{width:100%;position:relative;padding:0;}.fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100%;height:100%;}';
      var div = document.createElement("div");
      div.innerHTML = '<p>x</p><style id="fit-vids-style">' + css + '</style>';
      head.appendChild(div.childNodes[1]);
    }

    if ( options ) {
      $.extend( settings, options );
    }

    return this.each(function(){
      var selectors = [
        'iframe[src*="player.vimeo.com"]',
        'iframe[src*="youtube.com"]',
        'iframe[src*="youtube-nocookie.com"]',
        'iframe[src*="kickstarter.com"][src*="video.html"]',
        'object',
        'embed'
      ];

      if (settings.customSelector) {
        selectors.push(settings.customSelector);
      }

      var ignoreList = '.fitvidsignore';

      if(settings.ignore) {
        ignoreList = ignoreList + ', ' + settings.ignore;
      }

      var $allVideos = $(this).find(selectors.join(','));
      $allVideos = $allVideos.not('object object'); // SwfObj conflict patch
      $allVideos = $allVideos.not(ignoreList); // Disable FitVids on this video.

      $allVideos.each(function(count){
        var $this = $(this);
        if($this.parents(ignoreList).length > 0) {
          return; // Disable FitVids on this video.
        }
        if (this.tagName.toLowerCase() === 'embed' && $this.parent('object').length || $this.parent('.fluid-width-video-wrapper').length) { return; }
        if ((!$this.css('height') && !$this.css('width')) && (isNaN($this.attr('height')) || isNaN($this.attr('width'))))
        {
          $this.attr('height', 9);
          $this.attr('width', 16);
        }
        var height = ( this.tagName.toLowerCase() === 'object' || ($this.attr('height') && !isNaN(parseInt($this.attr('height'), 10))) ) ? parseInt($this.attr('height'), 10) : $this.height(),
            width = !isNaN(parseInt($this.attr('width'), 10)) ? parseInt($this.attr('width'), 10) : $this.width(),
            aspectRatio = height / width;
        if(!$this.attr('id')){
          var videoID = 'fitvid' + count;
          $this.attr('id', videoID);
        }
        $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css('padding-top', (aspectRatio * 100)+'%');
        $this.removeAttr('height').removeAttr('width');
      });
    });
  };
// Works with either jQuery or Zepto
})( window.jQuery || window.Zepto );


;(function ($) {
	$(document).ready(function () {
		
		var ChatStored = [];
		var ChatIdStored = [];
		var NotifyUserID = [];
		var ChatCached = "";
		var RequestState1 = true;
		var RequestState2 = true;
		var RequestState3 = true;
		var notifyInterval, showNotifyInterval, friendListRefresh;
		var winNo = [], oldGpMsgsNo = {}, newGpMsgsNo = {}, oldPvMsgsNo = {}, newPvMsgsNo = {}, newMessageInterval, blinkImgColor, upload_type, window_type, upload_id;
		
		Array.prototype.remove = function(value) {
			if (this.indexOf(value)!== -1) {
				this.splice(this.indexOf(value), 1);
				return true;
			} else {
				return false;
			};
		}
		
		var AjaxChatroom = {
			
			chatroomInit: function () {
				var self = $(this);
				this.loadSmileys();
				this.initSmileys();
				this.eventHandler();
				this.loadCommonChatRow();
				this.submitMessage();
				this.searchFriends();
				this.inputFileEvent();
				this.notificationAtTitle();	
			},

			searchFriends: function () {
				$("body").on("keyup", "#chatroomSearchFriends", function(e) {
					if(e.keyCode == 13) {
						$('#cr-private-userlist').prepend('<div class="cr_center"><i class="cr_spinnerx16"></i></div>');
						var searchValue = $(this).val();
						//clearTimeout(friendListRefresh);
						$.ajax({
							url: chatroom_conf.ajaxURL,
							type: "POST",
							dataType: "JSON",
							data: { 
								cr_searchData: searchValue,
								action : chatroom_conf.ajaxActions.cr_search_friends.action,
								nonce : chatroom_conf.ajaxNonce
							 },
							success: function(data) {
								$('#cr-private-userlist').html(data.FriendsRow);
							},
							complete: function() {
								//...
							}
						});
						$(this).val("");
					}
				});
			},
						
			refreshFriendsList: function () {
				$('#cr-private-userlist').prepend('<div class="cr_center"><i class="cr_spinnerx16"></i></div>');
				//clearTimeout(friendListRefresh);
				$.ajax({
					url: chatroom_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : chatroom_conf.ajaxActions.cr_refresh_friends.action,
						nonce : chatroom_conf.ajaxNonce
					},
					success: function(data) {
						$('#cr-private-userlist').html(data.FriendsRow);
					},
					complete: function() {
						//...
					}
				});
			},
			
			loadFriendsOnline: function () {
				$('#cr-private-userlist').prepend('<div class="cr_center"><i class="cr_spinnerx16"></i></div>');
				//clearTimeout(friendListRefresh);
				$.ajax({
					url: chatroom_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : chatroom_conf.ajaxActions.cr_online_friends.action,
						nonce : chatroom_conf.ajaxNonce
					},
					success: function(data) {
						$('#cr-private-userlist').html(data.FriendsRow);
					},
					complete: function() {
						//...
					}
				});
			},
			loadbpFriendsOnline: function () {
				$(".bpchatFriendsBody").prepend('<div class="bpchatFriendsBodyLoading"></div>');
				clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: chatroom_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : chatroom_conf.ajaxActions.bp_online_friends.action,
						nonce : chatroom_conf.ajaxNonce
					},
					success: function(data) {
						
						$(".bpchatFriendsBody").html(data.FriendsRow);
					},
					complete: function() {
						//friendListRefresh = setTimeout(AjaxChatroom.loadFriends, 30000);
						RequestState2 = true;
					}
				});
			},
			
			loadbpGroupList: function () {
				$('#cr-private-userlist').prepend('<div class="cr_center"><i class="cr_spinnerx16"></i></div>');
				//clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: chatroom_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : chatroom_conf.ajaxActions.cr_bp_group_list.action,
						nonce : chatroom_conf.ajaxNonce
					},
					success: function(data) {
						//$(".bpchatFriendsBodyLoading").remove();
						$('#cr-private-userlist').html(data.FriendsRow);
					},
					complete: function() {
						//RequestState2 = true;
					}
				});
			},
			
			loadMemberByGroupID: function (groupid) {
				$('#cr-private-userlist').prepend('<div class="cr_center"><i class="cr_spinnerx16"></i></div>');
				//clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: chatroom_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						GroupID : groupid,
						action : chatroom_conf.ajaxActions.cr_bp_group_member_list.action,
						nonce : chatroom_conf.ajaxNonce
					},
					success: function(data) {
						//$(".bpchatFriendsBodyLoading").remove();
						$('#cr-private-userlist').html(data.FriendsRow);
					},
					complete: function() {
						//friendListRefresh = setTimeout(AjaxChatroom.loadFriends, 30000);
						//RequestState2 = true;
					}
				});
			},

			
			loadSmileys: function () {
				$.get(chatroom_conf.templateURL + "smiley.html", function(data) {
					$("body").append(data);
				});
			},
			
			eventHandler: function () {
				$("body").on("click", "[data-event]", function (event){		
					event.preventDefault();
					event.stopPropagation();			
					var Event = $(this).attr("data-event");
					switch(Event) {						
						case "cr-create-group":
							var userid = $(this).attr("data-userid");
							AjaxChatroom.createNewGroup(userid);
						break;
						
						case "cr-open-group-chat":
							var groupid = $(this).attr('data-groupid');
							var groupname = $(this).html();
							AjaxChatroom.CreateGroupWindow(groupid, groupname);
						break;
						
						case "cr-private-chat-init":
							var userid = $(this).attr('data-cr-userid');
							var username = $(this).attr('data-cr-username');
							var userimage = $(this).children('img').attr('src');
							AjaxChatroom.CreatePrivateWindow(userid, username, userimage);
						break;
						
						case "open-chatroom-register":
							$(".cr_login_wrap").slideUp("slow");
							$(".open-register").slideUp("slow");
							$(".open-log-in").slideDown("slow");
							$(".cr_register_wrap").slideDown("slow");							
						break;
						case "open-chatroom-login":
							$(".cr_register_wrap").slideUp("slow");
							$(".open-log-in").slideUp("slow");
							$(".open-register").slideDown("slow");
							$(".cr_login_wrap").slideDown("slow");			
						break;
						
						case "chatroom-register-user":
							AjaxChatroom.registerNewUser();
						break;
						
						case "cr_refresh_friends":
							AjaxChatroom.refreshFriendsList();
							
						break;
						
						case "cr_online_friends":
							AjaxChatroom.loadFriendsOnline();
							
						break;
						
						case "cr_bp_online_friends":
							AjaxChatroom.loadbpFriendsOnline();
							
						break;
						
						case "cr_bp_group_list":
							AjaxChatroom.loadbpGroupList();
							
						break;
						
						case "cr_bp_group_member_list":
							var GroupID = $(this).attr("data-parameter-group-id");
							var GroupName = $(this).attr("data-parameter-group-name");
							AjaxChatroom.loadMemberByGroupID(GroupID);
						break;
						
						case "cr-head-tabs":
							$(this).addClass('cr-current').siblings().removeClass('cr-current');
							$('div.chatroomBody').find('div.cr-chat-box').eq($(this).index()).fadeIn(150).siblings('div.cr-chat-box').hide();
						break;
						
						case "cr-group-chat-tabs":
							$(this).addClass('cr-left-current').siblings().removeClass('cr-left-current');
							$(this).attr("data-tab-status", "1").siblings().attr("data-tab-status", "0");
							$('div#cr_group_chat_box').find('div.cr-group-chat-box').eq($(this).index()).fadeIn(150).siblings('div.cr-group-chat-box').hide();
						break;
						
						case "cr-close-group-tabs":
							var groupid = $(this).attr('data-groupid');
							$('[data-group-tabs="gt-'+groupid+'"]').remove();
							$('[data-group-chat-box="gcb-'+groupid+'"').remove();
							
							$('#cr_group_chat_tab li:first').addClass('cr-left-current').siblings().removeClass('cr-left-current');
							$('#cr_group_chat_tab li:first').attr("data-tab-status", "1").siblings().attr("data-tab-status", "0");
							$('div#cr_group_chat_box').find('div.cr-group-chat-box').eq($('#cr_group_chat_tab li:first').index()).fadeIn(150).siblings('div.cr-group-chat-box').hide();
							
						break;
						
						case "cr-private-chat-tabs":
							$(this).addClass('cr-left-current').siblings().removeClass('cr-left-current');
							$(this).attr("data-tab-status", "1").siblings().attr("data-tab-status", "0");
							$('div#cr_private_chat_box').find('div.cr-private-chat-box').eq($(this).index()).fadeIn(150).siblings('div.cr-private-chat-box').hide();
						break;
						
						case "cr-close-private-tabs":
							var userid = $(this).attr('data-userid');
							$('[data-private-tabs="pt-'+userid+'"]').remove();
							$('[data-private-chat-box="pcb-'+userid+'"').remove();
							
							$('#cr_private_chat_tab li:first').addClass('cr-left-current').siblings().removeClass('cr-left-current');
							$('#cr_private_chat_tab li:first').attr("data-tab-status", "1").siblings().attr("data-tab-status", "0");
							$('div#cr_private_chat_box').find('div.cr-private-chat-box').eq($('#cr_private_chat_tab li:first').index()).fadeIn(150).siblings('div.cr-private-chat-box').hide();
						break;
												
						
						case "cr-open-member-list":
							var groupid = $(this).attr('data-groupid');
							var state = $('#mgroup_id_'+groupid).attr('data-mlist-state');
							
							if(state == "0"){
								$(this).html("<span>&and;</span>");
								$('#mgroup_id_'+groupid).slideDown(200);
								$('#mgroup_id_'+groupid).attr("data-mlist-state", "1")
							}else{
								$(this).html("<span>&or;</span>");
								$('#mgroup_id_'+groupid).slideUp(200);
								$('#mgroup_id_'+groupid).attr("data-mlist-state", "0")
							}
						break;
						
						case "cr-open-res-tab":
							var tabid = $(this).attr('data-tab')+'-tab',
							isopen = $(this).attr('data-visibility');
							if(isopen == 'cr-close'){
								$('#'+tabid).animate({width: 'toggle'}, 500);
								$(this).attr('data-visibility', 'cr-open');
							}else{
								$('#'+tabid).animate({width: 'toggle'}, 500);
								$(this).attr('data-visibility', 'cr-close');
							}
						break;
					}
				});
			},

			registerNewUser: function () {
				var error = false,
					userName = $('#cr_signup_username').val(),
					fullName = $('#cr_signup_fullname').val(),
					email = $('#cr_signup_email').val(),
					pass = $('#cr_signup_password').val(),
					confirmPass = $('#cr_signup_password_confirm').val();
				
				if(!userName){
					$('#cr_signup_username').css('border-color','#ff0000');
					error = true;
				}
				if(!fullName){
					$('#cr_signup_fullname').css('border-color','#ff0000');
					error = true;
				}
				if(!email){
					$('#cr_signup_email').css('border-color','#ff0000');
					error = true;
				}
				var e=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
				if(!e.test(email)){
					$('#isp_signup_email').css('border-color','#ff0000');
					error = true;
				}
				if(!pass){
					$('#cr_signup_password').css('border-color','#ff0000');
					error = true;
				}
				if(!confirmPass){
					$('#cr_signup_password_confirm').css('border-color','#ff0000');
					error = true;
				}
				if(pass != confirmPass){
					$('#cr_signup_password').css('border-color','#ff0000');
					$('#cr_signup_password_confirm').css('border-color','#ff0000');
					error = true;
				}
				if(!error){
					$('#cr_signup_submit').after('<i class="cr_spinnerx16 cr_btn_spin"></i>');
					$('#cr_signup_submit').prop('disabled', true);
					$.ajax({
						url: chatroom_conf.ajaxURL,
						type: "POST",
						dataType: "JSON",
						data:{
							action : chatroom_conf.ajaxActions.cr_register_user.action,
							nonce : chatroom_conf.ajaxNonce,
							cr_signup_username : userName,
							cr_signup_email : email,
							cr_signup_fullname : fullName,
							cr_signup_password : pass,
							cr_signup_password_confirm : confirmPass
						},
						success: function(data) {
							var msg = data.cr_message;
							if(data.cr_error == false){
								$("#cr_register_form").each(function(){
									$(this).find(':input').val('');
								});
								$('#cr_register_msg').html(msg).slideToggle('slow')
								.delay(1000, "steps")
								.queue("steps", function(next) {
									$(".cr_register_wrap").slideUp("slow");
									$(".open-log-in").slideUp("slow");
									$(".open-register").slideDown("slow");
									$(".cr_login_wrap").slideDown("slow");
									next();
								})
								.delay(500, "steps")
								.queue("steps", function(next) {
									$('#cr_login_msg')
									.append('<p><i class="cr_spinnerx16 cr_left_spin"></i><span>Auto login. Please wait...</span></p>')
									.slideToggle('slow');
									next();
								})
								.delay(800, "steps")
								.queue("steps", function(next) {
									$("#cr_login_form input[type='text']").val(data.cr_username);
									$("#cr_login_form input[type='password']").val(data.cr_password);
									next();
								})
								.delay(1500, "steps")
								.queue("steps", function(next) {
									$('#cr_login_form').submit();
									next();
								})
								.dequeue( "steps" );  
								
							}else{
								$("#cr_register_msg").html(msg).slideToggle('slow')
								.delay(4000, "steps")
								.queue("steps", function(next) {
									$("#cr_register_msg").slideToggle('slow');
									next();
								})
								.dequeue( "steps" );
							}
							//$('#cr_signup_submit').siblings('i').remove();
							//$('#cr_signup_submit').prop('disabled', false);
						},
						complete: function() {
						}
					});
				}
			},
			
			CreateGroupWindow:function(groupid, groupname){
				
				var gtab = '<li data-groupid="'+groupid+'" data-event="cr-group-chat-tabs" data-tab-status="1" data-group-tabs="gt-'+groupid+'" class="cr-left-current cr-group-chat-tabs">'+groupname.charAt(0)+'</li>';

				var chatwindow = '';
					chatwindow += '<div data-group-chat-box="gcb-'+groupid+'" class="cr-group-chat-box cr_relative cr-visible">';
						chatwindow += '<div class="cr_chat_title">';
						chatwindow += '<strong class="cr_chat_title_name">'+groupname+'</strong>';
						chatwindow += '<i title="Close" data-event="cr-close-group-tabs" data-groupid="'+groupid+'" class="cr_close">&times;</i>';
						chatwindow += '</div>';
						chatwindow += '<div class="cr_chat_body" data-groupid="'+groupid+'" data-location="groupchat-body-'+groupid+'"></div>';
						chatwindow += '<div class="cr_chat_footer">';
							chatwindow += '<input type="text" data-event="submit-group-msg" placeholder="Start chat" data-groupid="'+groupid+'" />';
							chatwindow += '<span class="chatroomSmiley cr_smiley" data-event="group_smiley_open" data-groupid="'+groupid+'"></span>';
							chatwindow += '<span class="chatroomSmiley cr_image" data-event="cr_chatroom_image_open" data-type="group" data-window-id="'+groupid+'"></span>';
							chatwindow += '<span class="chatroomSmiley cr_video" data-event="cr_video_open" data-type="group" data-window-id="'+groupid+'"></span>';
							chatwindow += '<span class="chatroomSmiley cr_clip" data-event="cr_chatroom_file_open" data-type="group" data-window-id="'+groupid+'">';
						chatwindow += '</div>';
					chatwindow += '</div>';
					
				$("#cr_group_chat_tab").children('li').removeClass('cr-left-current');
				$("#cr_group_chat_tab").append(gtab);
				
				$("#cr_group_chat_box").children('div').removeClass('cr-visible');
				$("#cr_group_chat_box").append(chatwindow);
				if(RequestState1 == true) {
					RequestState1 = false;
					$.ajax({
						url: chatroom_conf.ajaxURL,
						type: "POST",
						dataType: "JSON",
						data: { 
							cr_groupid: groupid,
							action : chatroom_conf.ajaxActions.cr_get_group_chat_row.action,
							nonce : chatroom_conf.ajaxNonce
						 },
						success: function(data) {
							var chatdata = data.cr_group_chat_row;
							if(chatdata){
								jQuery.each(chatdata, function(i, object) {
									var chatID = i;
									var senderID = object.senderid;
									var senderName = object.senderName;
									var pmessage = object.message;
									var message = pmessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
									var chatTime = object.chat_time;
									var avatar = object.avatar;
									var is_user = object.is_user;
									var groupid = object.groupid;
									
									var Container = $("[data-location=\"groupchat-body-" + groupid + "\"]");
									if(is_user){	
										var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow cr_clear" data-msgtime="'+chatTime+'"><div class="chatroomMessageUserImage rightImage"><img src="'+avatar+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent">'+message+'</div></div></div>';
									}else{
										var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow OthersGroupChat cr_clear" data-msgtime="'+chatTime+'"><div class="chatroomMessageUserImage leftImage"><img src="'+avatar+'" /></div><div class="chatroomMessage leftMessage"><div class="chatroomContent"><strong>'+senderName+': </strong>'+message+'</div></div></div>';
									}
									
									$(Container).append(msg);
									$(Container).scrollTop($(Container).prop("scrollHeight"));
								});
							}
						},
						complete: function() {
							RequestState1 = true;
							if($(".cr_video").length > 0){
								$(".cr_video").fitVids();
							}
						}
					});
				}
			},
			
			CreatePrivateWindow:function(userid, username, userimage){
				
				var gtab = '<li id="cr-private-tab-id-'+userid+'" data-userid="'+userid+'" data-tab-status="1" data-event="cr-private-chat-tabs" data-private-tabs="pt-'+userid+'" class="cr-left-current cr-private-chat-tabs"><img class="chatroomFriendsImage" src="'+userimage+'" /></li>';

				var chatwindow = '';
					chatwindow += '<div data-private-chat-box="pcb-'+userid+'" class="cr-private-chat-box cr_relative cr-visible">';
						chatwindow += '<div class="cr_chat_title">';
						chatwindow += '<strong class="cr_chat_title_name">'+username+'</strong>';
						chatwindow += '<i title="Close" data-event="cr-close-private-tabs" data-userid="'+userid+'" class="cr_close">&times;</i>';
						chatwindow += '</div>';
						chatwindow += '<div class="cr_chat_body" data-userid="'+userid+'" data-location="privatechat-body-'+userid+'"></div>';
						chatwindow += '<div class="cr_chat_footer">';
							chatwindow += '<input type="text" data-event="submit-private-msg" placeholder="Start chat" data-userid="'+userid+'" />';
							chatwindow += '<span class="chatroomSmiley cr_smiley" data-event="private_smiley_open" data-userid="'+userid+'"></span>';
							//chatwindow += '<span class="chatroomSmiley cr_smiley" data-event="smiley_open" data-type="chatroom" data-window-id="'+userid+'"></span>';
							chatwindow += '<span class="chatroomSmiley cr_image" data-event="cr_chatroom_image_open" data-type="private" data-window-id="'+userid+'"></span>';
							chatwindow += '<span class="chatroomSmiley cr_video" data-event="cr_video_open" data-type="private" data-window-id="'+userid+'"></span>';
							chatwindow += '<span class="chatroomSmiley cr_clip" data-event="cr_chatroom_file_open" data-type="private" data-window-id="'+userid+'">';
						chatwindow += '</div>';
					chatwindow += '</div>';
					
				$("#cr_private_chat_tab").children('li').removeClass('cr-left-current');
				$("#cr_private_chat_tab").append(gtab);
				
				$("#cr_private_chat_box").children('div').removeClass('cr-visible');
				$("#cr_private_chat_box").append(chatwindow);
				if(RequestState1 == true) {
					RequestState1 = false;
					$.ajax({
						url: chatroom_conf.ajaxURL,
						type: "POST",
						dataType: "JSON",
						data: { 
							cr_userid: userid,
							action : chatroom_conf.ajaxActions.cr_get_private_chat_row.action,
							nonce : chatroom_conf.ajaxNonce
						 },
						success: function(data) {
							var chatdata = data.cr_private_chat_row;
							if(chatdata){
								$.each(chatdata, function(i, object) {
									var chatID = i;
									var senderID = object.senderid;
									var pmessage = object.message;
									var message = pmessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
									var chatTime = object.chat_time;
									var avatar = object.avatar;
									var is_user = object.is_user;
									var receiverID = object.receiverid;
									var window_id = object.window_id;
									
									var Container = $("[data-location=\"privatechat-body-" + window_id + "\"]");
									if(is_user){	
										var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow cr_clear" data-msgtime="'+chatTime+'"><div class="chatroomMessageUserImage rightImage"><img src="'+avatar+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent">'+message+'</div></div></div>';
									}else{
										var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow cr_clear" data-msgtime="'+chatTime+'"><div class="chatroomMessageUserImage leftImage"><img src="'+avatar+'" /></div><div class="chatroomMessage leftMessage"><div class="chatroomContent">'+message+'</div></div></div>';
									}
									
									$(Container).append(msg);
									$(Container).scrollTop($(Container).prop("scrollHeight"));
								});
							}
						},
						complete: function() {
							RequestState1 = true;
							if($(".cr_video").length > 0){
								$(".cr_video").fitVids();
							}
						}
					});
				}
			},
			
			createNewGroup: function(userid){
				var error = false,
					groupName = $('#cr-group-name').val(),
					groupDesc = $('#cr-group-desc').val(),
					privacy = $('input[name=cr-group-status]:checked', '#cr-create-group-form').val(),
					access = $('input[name=cr-group-invite-status]:checked', '#cr-create-group-form').val();
					
				if(!groupName){
					$('#cr-group-name').css('border-color','#ff0000');
					error = true;
				}
				if(!groupDesc){
					$('#cr-group-desc').css('border-color','#ff0000');
					error = true;
				}
				if(error == false){
					$('#cr-creat-group-btn').append('<i class="cr_spinnerx16"></i>');
					$.ajax({
						url: chatroom_conf.ajaxURL,						
						type: "POST",
						dataType: "JSON",
						data: { 
							cr_userid : userid,
							cr_groupName: groupName,
							cr_groupDesc: groupDesc,
							cr_privacy: privacy,
							cr_access: access,
							action : chatroom_conf.ajaxActions.cr_bp_create_group.action,
							nonce : chatroom_conf.ajaxNonce
						 },
						success: function(data) { 
							$('#cr-creat-group-btn').find('i').remove();
							if(data.is_insert){
								$('#cr-creat-group-btn').append('<span class="cr_hide cr_green">Done</span>')
								.delay(500, "steps")
								.queue("steps", function(next) {
									$('#cr-creat-group-btn').find('span').slideToggle('slow');
									next();
								})
								.delay(4000, "steps")
								.queue("steps", function(next) {
									$('#cr-creat-group-btn').find('span').slideToggle('slow');
									next();
								})
								.dequeue( "steps" );
							}else{
								$('#cr-creat-group-btn').append('<span class="cr_hide cr_red">Failed</span>')
								.delay(500, "steps")
								.queue("steps", function(next) {
									$('#cr-creat-group-btn').find('span').slideToggle('slow');
									next();
								})
								.delay(4000, "steps")
								.queue("steps", function(next) {
									$('#cr-creat-group-btn').find('span').slideToggle('slow');
									next();
								})
								.dequeue( "steps" );
							}
						},
						complete: function() {
							$('#cr-group-name').val('');
							$('#cr-group-desc').val('');
							
						}
					});
				}
			},
			
			loadCommonChatRow: function () {
				if(RequestState1 == true) {
					RequestState1 = false;
					$.ajax({
						url: chatroom_conf.ajaxURL,						
						type: "POST",
						dataType: "JSON",
						data: { 
							action : chatroom_conf.ajaxActions.cr_load_commonchat_row.action,
							nonce : chatroom_conf.ajaxNonce
						 },
						success: function (data){
							var chatdata = data.cr_chatinfo;
							var userid = data.cr_userid;
							if(chatdata){
								$.each(chatdata, function(i, object) {
									var chatID = i;
									var senderID = object.senderid;
									var senderName = object.senderName;
									var pmessage = object.message;
									var message = pmessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
									var chatTime = object.chat_time;
									var avatar = object.avatar;
									var is_user = object.is_user;
									
									var Container = $("[data-location=\"commonroom-body-" + userid + "\"]");
									
								if(is_user){	
									var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow cr_clear" data-msgtime="'+chatTime+'"><div class="chatroomMessageUserImage rightImage"><img src="'+avatar+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent"><strong>'+senderName+': </strong>'+message+'</div></div></div>';
								}else{
									var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow OthersCommonChat cr_clear" data-msgtime="'+chatTime+'"><div class="chatroomMessageUserImage leftImage"><img src="'+avatar+'" /></div><div class="chatroomMessage leftMessage"><div class="chatroomContent"><strong>'+senderName+': </strong>'+message+'</div></div></div>';
								}
									
									$(Container).append(msg);
									$(Container).scrollTop($(Container).prop("scrollHeight"));
								});
							}
						},
						complete: function() {
							setTimeout(AjaxChatroom.loadChatRow, chatroom_conf.chatRate);
							RequestState1 = true;
						}
					});
				}
			},
			
			loadChatRow: function () {
				
				var lastCommonChatID = $("#cr_commonchat_body .OthersCommonChat").last().attr("data-cr-chatid");
				if(!lastCommonChatID){
					lastCommonChatID = 0;
				}
				var lastGroupChatID = '';
				$("#cr_group_chat_tab li").each(function(index, element) {
                    var gid = $(this).attr('data-groupid');
					var gcid = $('[data-location="groupchat-body-'+gid+'"] .OthersGroupChat').last().attr("data-cr-chatid");
					lastGroupChatID += gid+','+gcid+';';
                });				
				//lastGroupChatID = lastGroupChatID.replace(/;\s*$/, "");
				if(lastGroupChatID == ''){
					lastGroupChatID =0;
				}
				if(RequestState1 == true) {
					RequestState1 = false;
					$.ajax({
						url: chatroom_conf.ajaxURL,						
						type: "POST",
						dataType: "JSON",
						data: { 
							cr_last_common_chatid : lastCommonChatID,
							cr_last_groupid_chatid : lastGroupChatID,
							action : chatroom_conf.ajaxActions.cr_load_chat_row.action,
							nonce : chatroom_conf.ajaxNonce
						 },
						success: function (data){
							var userid = data.cr_userid;
							var senderdata = data.cr_private_senderinfo;
							var chatdata = data.cr_private_chatinfo;
							var commondata = data.cr_common_chatinfo;
							var groupdata = data.cr_group_chatinfo;
							//private chat
							jQuery.each(senderdata, function(i, object) {
								var userid = i;
								var username = object.SenderName;
								var userimage = object.avatar;
								if($("#cr-private-tab-id-"+userid).length > 0){
									// Do nothing
								}else{
									AjaxChatroom.CreatePrivateWindow(userid, username, userimage)
								}
							});
							jQuery.each(chatdata, function(i, object) {
								var chatID = i;
								var senderID = object.senderid;
								var receiverID = object.receiverid;
								var pmessage = object.message;
								var message = pmessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
								var chatTime = object.chat_time;
								var avatar = object.avatar;
								
								var Container = $('[data-location="privatechat-body-' + senderID + '"]');
								
								var msg = '<div class="chatroomMessageRow cr_clear"><div class="chatroomMessageUserImage leftImage"><img src="'+avatar+'" /></div><div class="chatroomMessage leftMessage"><div data-parameter="'+chatID+'" class="chatroomContent chatroomMessageLocation-'+senderID+'">'+message+'</div></div></div>';
								$(Container).append(msg);
								$(Container).scrollTop($(Container).prop("scrollHeight"));
							});
							
							//common chat
							if(commondata){
								jQuery.each(commondata, function(i, object) {
									var chatID = i;
									var senderID = object.senderid;
									var senderName = object.senderName;
									var receiverID = object.receiverid;
									var pmessage = object.message;
									var message = pmessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
									var chatTime = object.chat_time;
									var avatar = object.avatar;
									
									var Container = $("[data-location=\"commonroom-body-" + userid + "\"]");
									
									var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow OthersCommonChat cr_clear"><div class="chatroomMessageUserImage leftImage"><img src="'+avatar+'" /></div><div class="chatroomMessage leftMessage"><div data-parameter="'+chatID+'" class="chatroomContent"><strong>'+senderName+': </strong>'+message+'</div></div></div>';
									
									$(Container).append(msg);
									$(Container).scrollTop($(Container).prop("scrollHeight"));
								});
							
							}
							// group chat
							if(groupdata){
								jQuery.each(groupdata, function(i, object) {
									var chatID = i;
									var senderID = object.senderid;
									var senderName = object.senderName;
									var receiverID = object.receiverid;
									var pmessage = object.message;
									var message = pmessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
									var chatTime = object.chat_time;
									var avatar = object.avatar;
									var groupid = object.groupid;
									
									var Container = $('[data-location="groupchat-body-' + groupid + '"]');
																		
									var msg = '<div data-cr-chatid="'+chatID+'" class="chatroomMessageRow OthersGroupChat cr_clear" data-msgtime="'+chatTime+'"><div class="chatroomMessageUserImage leftImage"><img src="'+avatar+'" /></div><div class="chatroomMessage leftMessage"><div class="chatroomContent"><strong>'+senderName+': </strong>'+message+'</div></div></div>';
									
									$(Container).append(msg);
									$(Container).scrollTop($(Container).prop("scrollHeight"));
								});
							
							}
							
						},
						complete: function() {
							if($(".cr_video").length > 0){
								$(".cr_video").fitVids();
							}
							setTimeout(AjaxChatroom.loadChatRow, chatroom_conf.chatRate);
							RequestState1 = true;
						}
					});
					
				}
			},

			submitMessage: function () {
				$("body").on("keyup", "[data-event]", function (e){		
					//event.stopPropagation();			
					var Event = $(this).attr("data-event");
					switch(Event) {						
						case "submit-commonroom-msg":
							if(e.keyCode == 13) {
								var userImage = chatroom_conf.avatar;
								var d = new Date();
								var time = d.getTime();
								var Message = $.trim($(this).val());
								var userID = $(this).attr("data-userid");
								var userName = $(this).attr("data-username");
								
								AjaxChatroom.sendCommonRoomMsg(userImage, time, Message, userID, userName);
								$(this).val("");
							}
						break;
						
						case "submit-group-msg":
							if(e.keyCode == 13) {
								var userImage = chatroom_conf.avatar;
								var d = new Date();
								var time = d.getTime();
								var Message = $.trim($(this).val());
								var groupid = $(this).attr("data-groupid");
								
								AjaxChatroom.sendGroupMsg(userImage, time, Message, groupid);
								$(this).val("");
							}
						break;
						
						case "submit-private-msg":
							if(e.keyCode == 13) {
								var userImage = chatroom_conf.avatar;
								var d = new Date();
								var time = d.getTime();
								var Message = $.trim($(this).val());
								var userid = $(this).attr("data-userid");
								
								AjaxChatroom.sendPrivateMsg(userImage, time, Message, userid);
								$(this).val("");
							}
						break;
					}
				})
			},
			
			sendCommonRoomMsg: function(userImage, time, Message, userID, userName){
					var Container = $('[data-location="commonroom-body-' + userID + '"]');
					var pMessage = Message.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
					var msg = '<div class="chatroomMessageRow cr_clear" data-msgtime="'+time+'"><div class="chatroomMessageUserImage rightImage"><img src="'+userImage+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent"><strong>'+userName+': </strong>'+pMessage+'</div></div></div>';
					
					if(Message != '') {
						$(Container).append(msg);
						$(Container).scrollTop($(Container).prop("scrollHeight"));
						$.ajax({
							url: chatroom_conf.ajaxURL,						
							type: "POST",
							dataType: "JSON",
							data: { 
								chatroom_message: Message, 
								cr_msg_time: time,
								action : chatroom_conf.ajaxActions.cr_submit_commonroom_message.action,
								nonce : chatroom_conf.ajaxNonce
							 },
							success: function(data) {
								if(data.is_insert == 1){
									// do nothing
								}else{
									$('[data-msgtime="' + data.row_time + '"]').addClass('cr_error_bg')
								}
							}
						});
					}
			},
			
			sendGroupMsg: function(userImage, time, Message, groupid){
					var Container = $('[data-location="groupchat-body-' + groupid + '"]');
					var pMessage = Message.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');

					var msg = '<div class="chatroomMessageRow cr_clear" data-msgtime="'+time+'"><div class="chatroomMessageUserImage rightImage"><img src="'+userImage+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent">'+pMessage+'</div></div></div>';
					
					if(Message != '') {
						$(Container).append(msg);
						$(Container).scrollTop($(Container).prop("scrollHeight"));
						$.ajax({
							url: chatroom_conf.ajaxURL,						
							type: "POST",
							dataType: "JSON",
							data: { 
								cr_group_message: Message,
								cr_groupid: groupid,
								action : chatroom_conf.ajaxActions.cr_submit_group_message.action,
								nonce : chatroom_conf.ajaxNonce
							 },
							success: function(data) {
								if(data.is_insert == 1){
									// do nothing
								}else{
									$('[data-msgtime="' + data.row_time + '"]').addClass('cr_error_bg')
								}
							}
						});
					}
					
			},
			
			sendPrivateMsg: function(userImage, time, Message, userid){
					var Container = $('[data-location="privatechat-body-' + userid + '"]');
					var pMessage = Message.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
					var msg = '<div class="chatroomMessageRow cr_clear" data-msgtime="'+time+'"><div class="chatroomMessageUserImage rightImage"><img src="'+userImage+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent">'+pMessage+'</div></div></div>';
					
					if(Message != '') {
						$(Container).append(msg);
						$(Container).scrollTop($(Container).prop("scrollHeight"));
						$.ajax({
							url: chatroom_conf.ajaxURL,						
							type: "POST",
							dataType: "JSON",
							data: { 
								cr_private_message: Message,
								cr_receiver: userid,
								action : chatroom_conf.ajaxActions.cr_submit_private_message.action,
								nonce : chatroom_conf.ajaxNonce
							 },
							success: function(data) {
								if(data.is_insert == 1){
									// do nothing
								}else{
									$('[data-msgtime="' + data.row_time + '"]').addClass('cr_error_bg')
								}
							}
						});
					}
					
			},
						
			initSmileys : function(){
				
				var slideCount = $('#chatroom-smiley ul li').length;
				var slideWidth = $('#chatroom-smiley ul li').width();
				var slideHeight = $('#chatroom-smiley ul li').height();
				var sliderUlWidth = slideCount * slideWidth;
				$('#chatroom-smiley').css({ width: slideWidth, height: slideHeight });
				$('#chatroom-smiley ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
				$('#chatroom-smiley ul li:last-child').prependTo('#chatroom-smiley ul');
					
				$("body").on("click", "[data-event]", function (){
					var Event = $(this).attr("data-event");
					switch(Event) {						
						case "smiley_prev":
							AjaxChatroom.smileyMoveLeft();
						break;
						case "smiley_next":
							AjaxChatroom.smileyMoveRight();
						break;
						case "smiley_open":
							var wID = $(this).attr("data-window-id");
							var posX = $(this).offset().left,
								posY = $(this).offset().top - $(window).scrollTop();    
							 $('#chatroom-smiley ul li span').each(function(index, element) {
                                $(this).attr("data-window-id", wID);
								$(this).attr("data-type", "window");
                            });
							if($("#chatroom-smiley").is(":visible")){
								$("#chatroom-smiley").css({"display": "none"});
							}else{
								$("#chatroom-smiley").css({"top":posY-285+"px","left":posX-8+"px", "display": "block"});
							}
							
						break;
						case "group_smiley_open":
							var wID = $(this).attr("data-groupid");
							var posX = $(this).offset().left,
								posY = $(this).offset().top - $(window).scrollTop();    
							 $('#chatroom-smiley ul li span').each(function(index, element) {
                                $(this).attr("data-groupid", wID);
								$(this).attr("data-type", "group");
                            });
							if($("#chatroom-smiley").is(":visible")){
								$("#chatroom-smiley").css({"display": "none"});
							}else{
								$("#chatroom-smiley").css({"top":posY-285+"px","left":posX-8+"px", "display": "block"});
							}
							
						break;
						case "private_smiley_open":
							var wID = $(this).attr("data-userid");
							var posX = $(this).offset().left,
								posY = $(this).offset().top - $(window).scrollTop();    
							 $('#chatroom-smiley ul li span').each(function(index, element) {
                                $(this).attr("data-userid", wID);
								$(this).attr("data-type", "private");
                            });
							if($("#chatroom-smiley").is(":visible")){
								$("#chatroom-smiley").css({"display": "none"});
							}else{
								$("#chatroom-smiley").css({"top":posY-285+"px","left":posX-8+"px", "display": "block"});
							}
							
						break;
						
						case "smiley_close":
							$('#chatroom-smiley').css("display", "none");
						break;
						case "insert_smiley":
							var stype = $(this).attr("data-type");
							if(stype == 'window'){
								var sID = $(this).attr("data-window-id");
								var iElement = $("input[data-window-id='"+sID+"']");
							}else if(stype == 'group'){
								var sID = $(this).attr("data-groupid");
								var iElement = $("input[data-groupid='"+sID+"']");
							}else if(stype == 'private'){
								var sID = $(this).attr("data-userid");
								var iElement = $("input[data-userid='"+sID+"']");
							}
							var sClass = $(this).attr("class");
							var sName = sClass.substr(sClass.indexOf("-")+1);
							var pValue = iElement.val();
							iElement.val(pValue+' '+sName+' ');
							$('#chatroom-smiley').css("display", "none");
							iElement.focus();
						break;
						
						case "cr_chatroom_image_open":
							upload_type = 'image';
							window_type = $(this).attr("data-type");
							upload_id = $(this).attr("data-window-id");
							$('#cr_chatroom_image_file').click();
							
						break;
						
						case "cr_video_open":
							var posX = $(this).offset().left,
								posY = $(this).offset().top - $(window).scrollTop();    
								
							if($("#cr_chatroom_video_upload").is(":visible")){
								$("#cr_chatroom_video_upload").css({"display": "none"});
							}else{
								$("#cr_chatroom_video_upload").css({"bottom":"30px","left":"5px", "display": "block"});
							}
							window_type = $(this).attr("data-type");
							upload_id = $(this).attr("data-window-id");
							
						break;
						
						case "cr_chatroom_file_open":
							upload_type = 'file';
							window_type = $(this).attr("data-type");
							upload_id = $(this).attr("data-window-id");
							
							$('#cr_chatroom_image_file').click();
							
						break;
					}
				})
				
			},
			
			inputFileEvent : function(){
				$('#cr_chatroom_image_upload input[type=file]').on('change', function(event){
					event.stopPropagation(); // Stop stuff happening
					event.preventDefault(); // Totally stop stuff happening
					var files = event.target.files;
					if(upload_type == 'image'){
						AjaxChatroom.uploadImage(window_type, upload_id, files);
					}else{
						AjaxChatroom.uploadFile(window_type, upload_id, files);
					}
					
				})
				$('#cr_chatroom_video_upload').on('submit', function(event){
					event.stopPropagation(); // Stop stuff happening
					event.preventDefault(); // Totally stop stuff happening
					var vidid = $('#cr_chatroom_video_upload input[type=text]').val();
					$("#cr_chatroom_video_upload").css({"display": "none"});
					AjaxChatroom.uploadVideo(window_type, upload_id, vidid);					
				})
								
				$('body').on('click', function (e) { // you don't need the else part to fadeout
				  var el = $('[data-visibility="cr-element"]');
				  if (el.is(":visible") && !$(e.target).is(el) && !$(e.target).is(el.find('*'))) {
					  el.fadeOut(200);
				  }
			   });
			},
			
			uploadImage : function(window_type, upload_id, files) {
				if(RequestState3 == true) {
					RequestState3 = false;
					var userImage = chatroom_conf.avatar;
					var d = new Date();
					var time = d.getTime();
					var Message = '<div class="cr_center"><i class="cr_spinnerx16"></i></div>';
										
					if(window_type == 'chatroom'){
						var Container = $('[data-location="commonroom-body-' + upload_id + '"]');
					}else if(window_type == 'group'){
						var Container = $('[data-location="groupchat-body-' + upload_id + '"]');
					}else if(window_type == 'private'){
						var Container = $('[data-location="privatechat-body-' + upload_id + '"]');
					}
					var msg = '<div class="chatroomMessageRow cr_clear" data-msgtime="'+time+'"><div class="chatroomMessageUserImage rightImage"><img src="'+userImage+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent">'+Message+'</div></div></div>';
					$(Container).append(msg);
					$(Container).scrollTop($(Container).prop("scrollHeight"));
	
					 // Create a formdata object and add the files
					var imagedata = new FormData();
					$.each(files, function(key, value){
						imagedata.append(key, value);
					});
					
					imagedata.append("action", chatroom_conf.ajaxActions.cr_upload_image.action);
					imagedata.append("nonce", chatroom_conf.ajaxNonce);
					imagedata.append("cr_window_type", window_type);
					imagedata.append("cr_upload_id", upload_id);
					imagedata.append("cr_time", time);
				
					$.ajax({
						url: chatroom_conf.ajaxURL,						
						type: "POST",
						dataType: "JSON",
						processData: false, // Don't process the files
						contentType: false, // Set content type to false as jQuery will tell the server its a query string request
						cache: false,
						data: imagedata,
						success: function(data) {
							if(data.cr_error == false){
								var image = '<img src="'+data.cr_insert_image+'" class="cr_image_center" />'
								$('[data-msgtime="' + data.cr_time + '"] .chatroomContent').html(image);
							}else{
								$('[data-msgtime="' + data.cr_time + '"] .chatroomContent').html('<span class="cr_red">'+data.cr_error_data+'</span>');
							}
						},
						complete: function(){
							$('#cr_chatroom_image_upload input[type=file]').val("");
							$('#cr_chatroom_image_upload input[type=file]').focus();
							RequestState3 = true;
						}
					});
				}
			},
			
			uploadFile : function(window_type, upload_id, files) {
				if(RequestState3 == true) {
					RequestState3 = false;
					var userImage = chatroom_conf.avatar;
					var d = new Date();
					var time = d.getTime();
					var Message = '<div class="cr_center"><i class="cr_spinnerx16"></i></div>';
															
					if(window_type == 'chatroom'){
						var Container = $('[data-location="commonroom-body-' + upload_id + '"]');
					}else if(window_type == 'group'){
						var Container = $('[data-location="groupchat-body-' + upload_id + '"]');
					}else if(window_type == 'private'){
						var Container = $('[data-location="privatechat-body-' + upload_id + '"]');
					}
					var msg = '<div class="chatroomMessageRow cr_clear" data-msgtime="'+time+'"><div class="chatroomMessageUserImage rightImage"><img src="'+userImage+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent">'+Message+'</div></div></div>';
					$(Container).append(msg);
					$(Container).scrollTop($(Container).prop("scrollHeight"));
	
					 // Create a formdata object and add the files
					var imagedata = new FormData();
					$.each(files, function(key, value){
						imagedata.append(key, value);
					});
					
					imagedata.append("action", chatroom_conf.ajaxActions.cr_upload_file.action);
					imagedata.append("nonce", chatroom_conf.ajaxNonce);
					imagedata.append("cr_window_type", window_type);
					imagedata.append("cr_upload_id", upload_id);
					imagedata.append("cr_time", time);
				
					$.ajax({
						url: chatroom_conf.ajaxURL,						
						type: "POST",
						dataType: "JSON",
						processData: false, // Don't process the files
						contentType: false, // Set content type to false as jQuery will tell the server its a query string request
						cache: false,
						data: imagedata,
						success: function(data) {
							
							if(data.cr_error == false){
								var file_link = data.cr_insert_file;
								$('[data-msgtime="' + data.cr_time + '"] .chatroomContent').html(file_link);
							}else{
								$('[data-msgtime="' + data.cr_time + '"] .chatroomContent').html('<span class="cr_red">'+data.cr_error_data+'</span>');
							}
						},
						complete: function(){
							$('#cr_chatroom_image_upload input[type=file]').val("");
							$('#cr_chatroom_image_upload input[type=file]').focus();
							RequestState3 = true;
						}
					});
				}
			},

			uploadVideo : function(window_type, upload_id, vidid) {
				if(RequestState3 == true) {
					RequestState3 = false;
					var userImage = chatroom_conf.avatar;
					var d = new Date();
					var time = d.getTime();
					var Message = '<div class="cr_center"><i class="cr_spinnerx16"></i></div>';
					if(window_type == 'chatroom'){
						var Container = $('[data-location="commonroom-body-' + upload_id + '"]');
					}else if(window_type == 'group'){
						var Container = $('[data-location="groupchat-body-' + upload_id + '"]');
					}else if(window_type == 'private'){
						var Container = $('[data-location="privatechat-body-' + upload_id + '"]');
					}
					
					var msg = '<div class="chatroomMessageRow cr_video_row cr_clear" data-msgtime="'+time+'"><div class="chatroomMessageUserImage rightImage"><img src="'+userImage+'" /></div><div class="chatroomMessage rightMessage"><div class="chatroomContent">'+Message+'</div></div></div>';
					
					
					$(Container).append(msg);
					$(Container).scrollTop($(Container).prop("scrollHeight"));
					
					$.ajax({
						url: chatroom_conf.ajaxURL,						
						type: "POST",
						dataType: "JSON",
						data: { 
								cr_videoid: vidid, 
								cr_window_type: window_type,
								cr_upload_id: upload_id,
								cr_time: time,
								action : chatroom_conf.ajaxActions.cr_upload_video.action,
								nonce : chatroom_conf.ajaxNonce
						},
						success: function(data) {
							
							if(data.cr_error == false){
								var video_id = data.cr_videoid;
								var youtube = '<div class="cr_video" style="min-width:300px;min-height:180px; margin:0 auto;"><iframe title="YouTube video player" src="http://www.youtube.com/embed/'+video_id+'" frameborder="0" allowfullscreen></iframe></div>';

								$('[data-msgtime="' + data.cr_time + '"] .chatroomContent').html(youtube);
							}else{
								$('[data-msgtime="' + data.cr_time + '"] .chatroomContent').html('<span class="cr_red">'+data.cr_error_data+'</span>');
							}
						},
						complete: function(){
							$('#cr_chatroom_video_upload input[type=text]').val("");
							RequestState3 = true;
						}
					});
				}
			},
			
			smileyMoveLeft : function() {
				$('#chatroom-smiley ul').animate({
					left: + $('#chatroom-smiley ul li').width()
				}, 200, function () {
					$('#chatroom-smiley ul li:last-child').prependTo('#chatroom-smiley ul');
					$('#chatroom-smiley ul').css('left', '');
				});
			},
		
			smileyMoveRight : function() {
				$('#chatroom-smiley ul').animate({
					left: - $('#chatroom-smiley ul li').width()
				}, 200, function () {
					$('#chatroom-smiley ul li:first-child').appendTo('#chatroom-smiley ul');
					$('#chatroom-smiley ul').css('left', '');
				});
			},
			
			notificationAtTitle : function(){
				var timer=0, newtitle = [], oldtitle = document.title;
				newtitle.push(oldtitle);
				var vis = (function(){
					var stateKey, eventKey, keys = {
						hidden: "visibilitychange",
						webkitHidden: "webkitvisibilitychange",
						mozHidden: "mozvisibilitychange",
						msHidden: "msvisibilitychange"
					};
					for (stateKey in keys) {
						if (stateKey in document) {
							eventKey = keys[stateKey];
							break;
						}
					}
					return function(c) {
						if (c) document.addEventListener(eventKey, c);
						return !document[stateKey];
					}
				})();
				vis(function(){
					var boxname = [], chatno = {};
					var audioplayer = document.getElementById("chatroom_alert");
					$(".cr_chat_body").each(function(i, element) {
						var id = $(this).attr('id');
						chatno[i] = $(this).find('.chatroomMessageRow').length;
					});
					
					if(!vis()){
						notifyInterval = setInterval(function(){ 
							var nchatno = {}, ntitle;							
							$(".cr_chat_body").each(function(i, element) {
								var nid = $(this).attr('id'),
									ntitle =  'New message';
									
								nchatno[i] = $(this).find('.chatroomMessageRow').length;
								
								if(nchatno[i] > chatno[i] && $.inArray(ntitle, newtitle) == -1){
									newtitle.push(ntitle);
								}
							});
						}, 2000);
						
						showNotifyInterval = setInterval(function(){ 
							if(newtitle.length > 1){
								document.title = newtitle[timer];
								timer++
								if (timer >= newtitle.length){
									timer=0;
								}
								audioplayer.play();
							}
						}, 3000);
						
					}else{
						clearInterval(notifyInterval);
						clearInterval(showNotifyInterval);
						document.title = oldtitle;
						newtitle = [];
						newtitle.push(oldtitle);
						audioplayer.pause();
					}
					
				});
				
				newMessageInterval  = setInterval(function(){ 
				
					if($("#cr_group_chat_tab").length > 0){
						$("#cr_group_chat_tab li").each(function(i, element){
							
							newGpMsgsNo[i] = $('div#cr_group_chat_box').find('div.cr-group-chat-box').eq(i).find('.chatroomMessageRow').length;
							
							if($(this).attr('data-tab-status') == 0 && (newGpMsgsNo[i] > oldGpMsgsNo[i])){
								if($(this).hasClass('cr_userimg_grey')){
									$(this).removeClass('cr_userimg_grey').addClass('cr_userimg_green');
								}else if($(this).hasClass('cr_userimg_green')){
									$(this).removeClass('cr_userimg_green').addClass('cr_userimg_grey');
								}else{
									$(this).addClass('cr_userimg_green');
								}
								
							}
							oldGpMsgsNo[i] = $('div#cr_group_chat_box').find('div.cr-group-chat-box').eq(i).find('.chatroomMessageRow').length;
						})
					}
					if($("#cr_private_chat_tab").length > 0){
						$("#cr_private_chat_tab li").each(function(i, element){
							
							newPvMsgsNo[i] = $('div#cr_private_chat_box').find('div.cr-private-chat-box').eq(i).find('.chatroomMessageRow').length;
							
							if($(this).attr('data-tab-status') == 0 && (newPvMsgsNo[i] > oldPvMsgsNo[i])){
								if($(this).hasClass('cr_userimg_grey')){
									$(this).removeClass('cr_userimg_grey').addClass('cr_userimg_green');
								}else if($(this).hasClass('cr_userimg_green')){
									$(this).removeClass('cr_userimg_green').addClass('cr_userimg_grey');
								}else{
									$(this).addClass('cr_userimg_green');
								}
								
							}
							oldPvMsgsNo[i] = $('div#cr_private_chat_box').find('div.cr-private-chat-box').eq(i).find('.chatroomMessageRow').length;
						})
					}
					
				}, 3500);
				
				blinkImgColor  = setInterval(function(){ 
					if($(".cr_userimg_green").length > 0){
						$(".cr_userimg_green").each(function(){
							var randColor = '#'+(Math.random()*0xFFFFFF<<0).toString(16);
							$(this).css('border-color', randColor)
						})
					}
					
				}, 2000);
			}
		}
		
		AjaxChatroom.chatroomInit();
		
	});
}(jQuery));

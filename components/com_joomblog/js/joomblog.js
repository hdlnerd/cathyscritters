/*
* JoomBlog component for Joomla
* @package JoomBlog
* @subpackage joomblog.js
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
var jb_JQuery = jQuery.noConflict();

function closeTag(text1, text2, textarea){
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange){
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

		if (temp_length == 0)
		{
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
		}
		else
			textarea.focus(caretPos);
	}else if (typeof(textarea.selectionStart) != "undefined"){
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange)
		{
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	}else{
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}
}

function getdom(id){
  return document.getElementById(id);
}

function validateComment(f){
	if(!f.name.value){
		alert('"Name" field must not be empty.');
		return false;
	}
	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

	if(!f.email.value){
		alert('Please provide a valid email address.');
		return false;
	}

	if(reg.test(f.email.value)  == false){
		alert('The address \''+f.email.value+'\' does not appear to be a valid email address.');
		return false;
	}
	
	if(!f.comment.value){
		alert('"Comment" field must not be empty.');
		return false;
	}
	
	return true;
}

function Addcomment(f){
	if(validateComment(f)){
		f.submit();
	}
}

function Savecomment(){
	document.commentListForm.task.value = "savecomment";
	document.commentListForm.submit();
}

function Publishedcomment(id,s){
	document.commentListForm.task.value = "publishedcomment";
	document.commentListForm.params.value = s;
	document.commentListForm.id.value = id;
	document.commentListForm.submit();
}

function Editcomment(id){
	var bbcode = '<br/><div id="comment_codes">'+
              '<a href="javascript:void(0);"  title="Bold" onclick="closeTag(\'[b]\', \'[/b]\', getdom(\'editcomment\')); return false;" class="code">'+
                '<span class="code_b">B</span>'+
              '</a>'+
              '<a href="javascript:void(0);"  title="Italicize" onclick="closeTag(\'[i]\', \'[/i]\', getdom(\'editcomment\')); return false;" class="code">'+
                '<span class="code_i">I</span>'+
              '</a>'+
              '<a href="javascript:void(0);"  title="Underline" onclick="closeTag(\'[u]\', \'[/u]\', getdom(\'editcomment\')); return false;" class="code">'+
                '<span class="code_u">U</span>'+
              '</a>'+
              '<a href="javascript:void(0);"  title="Strikethrough" onclick="closeTag(\'[s]\', \'[/s]\', getdom(\'editcomment\')); return false;" class="code">'+
                 '<span class="code_s">S</span>'+
              '</a>'+
              '<a href="javascript:void(0);"  title="URL" onclick="closeTag(\'[url]\', \'[/url]\', getdom(\'editcomment\')); return false;" class="code">'+
                '<span class="code_url">URL</span>'+
              '</a>'+
              '<a href="javascript:void(0);"  title="Image" onclick="closeTag(\'[img]\', \'[/img]\', getdom(\'editcomment\')); return false;" class="code">'+
                '<span class="code_image">Image</span>'+
              '</a>'+
              '<a href="javascript:void(0);"  title="Quote" onclick="closeTag(\'[quote]\', \'[/quote]\', getdom(\'editcomment\')); return false;" class="code">'+
                '<span class="code_quote">Quote</span>'+
              '</a>'+
            '</div>';
	document.getElementById('desc-comment-'+id).innerHTML = bbcode+'<textarea class="inputbox" id="editcomment" name="editcomment['+id+']" cols="40" rows="5" >'+document.getElementById('desc-comment-'+id).innerHTML+'</textarea>';
	document.getElementById('edit-comment-'+id).style['display'] = 'none';
	document.getElementById('save-comment-'+id).style['display'] = 'inline';
}

function sendVote(id,vote){
	jb_JQuery.ajax({
		url: baseurl+'index.php?option=com_joomblog&task=addvote&id='+id+'&format=raw&vote='+vote,
		dataType : "json",                    
		success: function (data, textStatus) {
			if(data.msg){
				alert(data.msg);
			}

			if(data.sumvote !== undefined){
				
				if(data.sumvote>0){
					//sumvote = "+"+data.sumvote;
					sumvote = data.sumvote;
				}else{
					sumvote = data.sumvote;
				}
				
				
				if(data.sumvote>0){
					jb_JQuery("#post-"+id+" .sumvote").addClass("green");
				}else{
					if(data.sumvote<0){
						jb_JQuery("#post-"+id+" .sumvote").addClass("red");
					}else{
						jb_JQuery("#post-"+id+" .sumvote").removeClass("green red");
					}
				}
				
				jb_JQuery("#post-"+id+" .sumvote").text(sumvote);
			}
		}
	});
}

function sendCommentVote(id,vote){
	jb_JQuery.ajax({
		url: baseurl+'index.php?option=com_joomblog&task=addcommentvote&id='+id+'&format=raw&vote='+vote,
		dataType : "json",                    
		success: function (data, textStatus) {
			if(data.msg){
				alert(data.msg);
			}

			if(data.sumcommentvote !== undefined){
				
				if(data.sumcommentvote>0){
					//sumcommentvote = "+"+data.sumcommentvote;
					sumcommentvote = data.sumcommentvote;
				}else{
					sumcommentvote = data.sumcommentvote;
				}
				
				
				if(data.sumcommentvote>0){
					jb_JQuery("#comment"+id+" .sumcommentvote").addClass("green");
				}else{
					if(data.sumcommentvote<0){
						jb_JQuery("#comment"+id+" .sumcommentvote").addClass("red");
					}else{
						jb_JQuery("#comment"+id+" .sumcommentvote").removeClass("green red");
					}
				}

				jb_JQuery("#comment"+id+" .sumcommentvote").text(sumcommentvote);
			}
		}
	});
}



jb_JQuery(document).ready(function(){
	
	var menu = jb_JQuery('#joomBlog-toolbar').width();
	menu = Math.floor(menu);
	//var li_array = new Array(0);
	li_array = jb_JQuery('#joomBlog-toolbar li');
	
	var num = 0;
	var li_sum = 0;
	if (li_array.length)
	for (var i=0; i<li_array.length;i++)
	{
		li_sum = li_sum + li_array[i].offsetWidth;
		if (li_sum + 110 > menu) break;
		num++;
	}

	if (num){
				
		jb_JQuery.each(li_array, function(i, val){
				if ( i == num )
				{
					jb_JQuery(li_array[i-1]).after('<li class="dropdown-menu"><a href="javascript:void(0);" id="dropdown-menu-click"><!--x--></a></li>');
				}
				if ( i >= num ){
					jb_JQuery('.hidden-menu').append(jb_JQuery(li_array[i]));
				}
			
		});		
	}

	
	jb_JQuery('#dropdown-menu-click').bind('click',switchMenu);
	function switchMenu(){
		var hidden = jb_JQuery('.hidden-menu');
		
		if(hidden.css('display') == 'none')
		{
			hidden.fadeIn(100);
			return;
		}
		if(hidden.css('display') == 'block')
		{
			hidden.fadeOut(100);
			return;
		}
	}
		
	jb_JQuery(window).resize(function(){
		var menu = jb_JQuery('#joomBlog-toolbar').width();
		menu = Math.floor(menu);
		var li_array = jb_JQuery('#joomBlog-toolbar li').not('.dropdown-menu');
		var li = li_array[0].offsetWidth + 20;
		var li_sum = (li_array.length * li) + (15 * li_array.length) + 40;
				
		if (menu < li_sum){
			
			var num = Math.floor(menu/li);		
			jb_JQuery.each(li_array, function(i, val){
				if ( i >= num - 1 ){
					jb_JQuery('.hidden-menu').prepend(jb_JQuery(li_array[i]));
				}
			});
			
			var last = jb_JQuery('#joomBlog-toolbar li:last').attr('class');
			if (last != 'dropdown-menu'){
				jb_JQuery('#joomBlog-toolbar li:last').after('<li class="dropdown-menu"><a href="javascript:void(0);" id="dropdown-menu-click"><!--x--></a></li>');
				jb_JQuery('#dropdown-menu-click').bind('click',switchMenu);
			}
			
			return true;
		}	
		
		
		if (menu > (li_sum + li + 10)){
		
			var num = Math.floor(menu/li);
			var menu_li = jb_JQuery('.hidden-menu li');
			
			jb_JQuery.each(li_array, function(i, val){
				
				if (i <= num ){
					jb_JQuery(menu_li[0]).insertBefore('.dropdown-menu');
				}
			});
			if (menu_li.length == 1) jb_JQuery('.dropdown-menu').remove();
			return true;
		}
		
		
	});
});

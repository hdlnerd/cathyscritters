/*  
 * JCE Editor                 2.3.0
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2012 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    06 December 2012
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * NOTE : Javascript files have been compressed for speed and can be uncompressed using http://jsbeautifier.org/
 */
(function($){var $tmp=document.createElement('div');$.support.borderRadius=(function(){return typeof $tmp.style['borderRadius']!=='undefined';})();if(typeof Joomla==='undefined'){Joomla={};}
Joomla.modal=function(el,url,width,height){var o={'handler':'iframe','size':{x:width,y:height},'url':url};if(typeof SqueezeBox.open==='undefined'){return SqueezeBox.fromElement(el,o);}
return SqueezeBox.open(el,o);};$.fn.checkbox=function(){if($.support.borderRadius){if($(this).hasClass('ui-checkbox-element')){return;}
var n=this,css={};$.each(['marginTop','marginRight','marginBottom','marginLeft'],function(i,k){css[k]=$(n).css(k);});$(this).addClass('ui-checkbox-element').wrap('<span class="ui-checkbox" />').click(function(){$(this).parent().toggleClass('checked',this.checked);}).on('check',function(){$(this).parent().toggleClass('checked',this.checked);}).on('disable',function(){$(this).parent().toggleClass('disabled',this.disabled);}).each(function(){$(this).parent().toggleClass('checked',this.checked).toggleClass('disabled',this.disabled).css(css);});}
return this;};$.fn.radio=function(){if($.support.borderRadius){if($(this).hasClass('ui-radio-element')){return;}
var n=this,css={};$.each(['marginTop','marginRight','marginBottom','marginLeft'],function(i,k){css[k]=$(n).css(k);});$(this).addClass('ui-radio-element').wrap('<span class="ui-radio" />').click(function(){$(this).parent().toggleClass('checked',this.checked);$('input[type="radio"][name="'+$(this).attr('name')+'"]').not(this).parent().toggleClass('checked',!this.checked);}).on('check',function(){$(this).parent().toggleClass('checked',this.checked);}).on('disable',function(){$(this).parent().toggleClass('disabled',this.disabled);}).each(function(){$(this).parent().toggleClass('checked',this.checked).toggleClass('disabled',this.disabled).css(css);});}
return this;};$.jce={options:{},init:function(options){var self=this;$.extend(true,this.options,options);$('body').addClass('ui-jce');$('a.dialog').click(function(e){self.createDialog(e,{src:$(this).attr('href'),options:$(this).data('options')});e.preventDefault();});if(this.options.bootstrap){$('body').addClass('ui-bootstrap');$('input[size="100"]').addClass('input-xlarge');$('input[size="50"]').addClass('input-large');$('input[size="5"]').addClass('input-mini');}else{$('body').addClass('ui-jquery');$('div.icon a').addClass('ui-widget-content ui-corner-all');$('button#filter_go').button({icons:{primary:'ui-icon-search'}});$('button#filter_reset').button({icons:{primary:'ui-icon-arrowrefresh-1-e'}});$('button.upload-import').button({icons:{primary:'ui-icon-arrowthick-1-n'}});if(!$.support.leadingWhitespace){$('#profiles-list tr:odd').addClass('odd');$('#profiles-list tr:last-child').addClass('last');}}
$('.wf-tooltip, .hasTip').tips({parent:'#jce'});$('th input[type="checkbox"]',$('table.adminlist')).click(function(){var n=$('td input[type="checkbox"]',$('table.adminlist')).prop('checked',this.checked).trigger('check');$('input[name="boxchecked"]').val($(n).filter(':checked').length);});$('td input[type="checkbox"]',$('table.adminlist')).click(function(){var bc=$('input[name="boxchecked"]').val();var n=$('td input[type="checkbox"]',$('table.adminlist')).length;$('th input[type="checkbox"]',$('table.adminlist')).prop('checked',bc==n).trigger('check');});if(!$.support.cssFloat){$('#jce').addClass('ie');if(document.querySelector){if(!$.support.leadingWhitespace){$('#jce').addClass('ie8');}else{$('#jce').addClass('ie9');}}}
this._setDependants();this._formWidgets();$('label.radio').addClass('inline');$('#profiles-list tbody').sortable({handle:'span.sortable-handle',helper:function(e,tr){var $cells=tr.children();var $helper=tr.clone();$helper.children().each(function(i){$(this).width($cells.eq(i).width());});return $helper;},stop:function(e,ui){var n=this;$('input[name="task"]').val('saveorder');var cid=$('input[name^="cid"]',n).prop('checked',true).serialize();$('input[name^="cid"]',n).prop('checked',false);$('#profiles-list tbody').sortable('disable');$(ui.item).addClass('busy');function end(){$('#profiles-list tbody').sortable('enable');$(ui.item).removeClass('busy');}
var order=[];$('tr',n).each(function(i){order.push('order[]='+i);});$.ajax({type:'POST',url:'index.php',data:$('input[name]','#adminForm').not('input[name^="order"]').serialize()+'&'+cid+'&'+order.join('&')+'&tmpl=component',success:function(){end();$('tr',n).each(function(i){$('input[name^="order"]',this).val(i+1);$('input[id^="cb"]',this).attr('id','cb'+i);});if(!$.support.leadingWhitespace){$('#profiles-list tr').removeClass('odd').filter(':odd').addClass('odd');$('#profiles-list tr').removeClass('last').last().addClass('last');}},error:function(){end();}});}});$('span.order-up a','#profiles-list').click(function(e){$('input[name^=cid]',$(this).parents('tr')).prop('checked',true);$('input[name="task"]').val('orderup');$('#adminForm').submit();e.preventDefault();});$('span.order-down a','#profiles-list').click(function(e){$('input[name^=cid]',$(this).parents('tr')).prop('checked',true);$('input[name="task"]').val('orderdown');$('#adminForm').submit();e.preventDefault();});$(document).ready(function(){$('input[type="checkbox"]').checkbox();$('input[type="radio"]').radio();});},createDialog:function(e,o){var self=this,data={};if($.type(o.options)=='string'){data=$.parseJSON(o.options.replace(/'/g,'"'));}else{data=o.options;}
data=data||{width:640,height:480};return Joomla.modal(e.target,o.src,data.width,data.height);},closeDialog:function(el){var win=window.parent;if(typeof win.SqueezeBox!=='undefined'){return win.SqueezeBox.close();}},_passwordWidget:function(el){var span=document.createElement('span');$(span).addClass('widget-password locked').insertAfter(el).click(function(){el=$(this).siblings('input[type="password"]');if($(this).hasClass('locked')){var input=document.createElement('input');$(el).hide();$(input).attr({type:'text',size:$(el).attr('size'),value:$(el).val(),'class':$(el).attr('class')}).insertAfter(el).change(function(){$(el).val(this.value);});}else{var n=$(this).siblings('input[type="text"]');var v=$(n).val();$(n).remove();$(el).val(v).show();}
$(this).toggleClass('locked');});},_formWidgets:function(){var self=this;$('input[type="password"]').each(function(){self._passwordWidget(this);});$('input[placeholder]:not(:file), textarea[placeholder]').placeholder();$(':input[pattern]').pattern();$(':input[max]').max();$(':input[min]').min();},_setDependants:function(){$('input[data-parent], select[data-parent]').each(function(){var el=this,data=$(this).data('parent');var p=$(this).parents('li:first');$(p).hide();$.each(data.split(';'),function(i,s){s=/([\w\.]+)\[([\w,]+)\]/.exec(s);if(s){var k=s[1],v=s[2].split(',');k=k.replace(/[^\w]+/g,'');var ev='change.'+k;$('#params'+k).on(ev,function(){var state=$.inArray(this.value,v)!=-1;if(state){$(el).removeClass('child-of-'+k);if(el.className.indexOf('child-of-')===-1){$(p).show();}}else{$(p).hide();$(el).addClass('child-of-'+k);}
$(el).trigger('visibility:toggle',state);}).on('visibility:toggle',function(e,state){if(state){$(el).parent().show();}else{$(el).parent().hide();}}).trigger(ev);}});});}};})(jQuery);var $jce=jQuery.jce;
/*
* Name: editor_plugin.js (for ccSimpleUploader tinyMCE plugin)
* Author: Timur Kovalev - www.creativecodedesign.com
* Update: Bernard Saulmé - softs.saulme.fr 02/04/2011 06/04/2012 26/06/2012
* Distribution: Free for all to modify, distribute, and use, but I am not liable for any issues or problems.
*/

(function() {
    var strPluginURL;
	tinymce.PluginManager.requireLangPack("ccSimpleUploader");
    tinymce.create('tinymce.plugins.ccSimpleUploaderPlugin', {
        init: function(ed, url) 
        {
            strPluginURL = url;                                         // store the URL for future use..
            ed.addCommand('mceccSimpleUploader', function() {
                ccSimpleUploader();              
            });
            ed.addButton('ccSimpleUploader', {
                title:'ccSimpleUploader.desc',
                cmd:'mceccSimpleUploader',
                image: url + '/img/ccSimpleUploader.gif'
            });
        },
        createControl: function(n, cm) {
            return null;
        },
        getPluginURL: function() {
            return strPluginURL;
        },
        getInfo: function() {
            return {
                longname: 'ccSimpleUploader plugin',                
                author: 'Timur Kovalev, Bernard Saulmé',
                authorurl: 'http://www.creativecodedesign.com http://softs.saulme.fr/',
                infourl: 'http://www.creativecodedesign.com http://softs.saulme.fr/',
                version: "2.0.0"
            };
        }
    });
    tinymce.PluginManager.add('ccSimpleUploader', tinymce.plugins.ccSimpleUploaderPlugin);
})();

// this function can get called from the plugin inint (above) or from the callback on advlink/advimg plugins..
// in the latter case, win and type will be set.. In the rist case, we will just update the main editor window
// with the path of the uploaded file
function ccSimpleUploader(field_name, url, type, win) {    
    var strPluginPath = tinyMCE.activeEditor.plugins.ccSimpleUploader.getPluginURL();                               // get the path to the uploader plugin    
    var strUploaderURL = strPluginPath + "/uploader.php";                                                           // generate the path to the uploader script    
    var strUploadPath = tinyMCE.activeEditor.getParam('plugin_ccSimpleUploader_upload_path');                       // get the relative upload path
    var strSubstitutePath = tinyMCE.activeEditor.getParam('plugin_ccSimpleUploader_upload_substitute_path');        // get the path we'll substitute for the for the upload path (i.e. fully qualified)
	var strLanguage = tinyMCE.activeEditor.getParam('plugin_ccSimpleUploader_language_file'); 	                    // Get the language info for language file info
    var ResizeSizeX = tinyMCE.activeEditor.getParam('plugin_ccSimpleUploader_ResizeSizeX');                         // Get the max width (0 = no max)
	var ResizeSizeY = tinyMCE.activeEditor.getParam('plugin_ccSimpleUploader_ResizeSizeY');                         // Get de max height (0 = no max)
	var UserId = tinyMCE.activeEditor.getParam('plugin_ccSimpleUploader_UserId');                                   // Get the user Id
	var View = tinyMCE.activeEditor.getParam('plugin_ccSimpleUploader_View');                                       // Get the view
    if (strUploaderURL.indexOf("?") < 0)                                                                            // if we were called without any GET params
        strUploaderURL = strUploaderURL + "?type=" + type + "&d=" + strUploadPath + "&subs=" + strSubstitutePath + "&lf=" + strLanguage + "&re_x=" + ResizeSizeX + "&re_y= " + ResizeSizeY + "&id=" + UserId + "&view=" + View;  
    else
        strUploaderURL = strUploaderURL + "&type=" + type + "&d=" + strUploadPath + "&subs=" + strSubstitutePath + "&lf=" + strLanguage + "&re_x=" + ResizeSizeX + "&re_y= " + ResizeSizeY + "&id=" + UserId + "&view=" + View;
	// Note : & instead of &amp; because with the php cgi, $_POST has some problem to fill some variable in that case ...
	
    tinyMCE.activeEditor.windowManager.open({                                                                       // open the plugin popup
        file            : strUploaderURL,
        title           : '-',		// pop up window default title
        width           : 400,  
        height          : 180,
        resizable       : "yes", 
        inline          : 1,        // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous  : "no"
    }, {
        window : win,
        input : field_name
    });
  
    return false;
}
// This function will get called when the uploader is done uploading the file and ready to update
// calling dialog and close the upload popup
// strReturnURL should be the string with the path to the uploaded file
function ClosePluginPopup (strReturnURL) {
    var win = tinyMCEPopup.getWindowArg("window");                                          // insert information now
    if (!win)
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, strReturnURL);
    else
    {
        win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = strReturnURL;	
	    if (typeof(win.ImageDialog) != "undefined")                                             // are we an image browser
	    {		
		    if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();                   // we are, so update image dimensions and preview if necessary
		    if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(strReturnURL);
	    }	
	}
	tinyMCEPopup.close();	                                                                    // close popup window
}

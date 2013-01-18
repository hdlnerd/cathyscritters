<?php
/** 
 * @package ShareThisBar Plugin for Joomla! 2.5
 * @version $Id: sharethisbar.php 3.0 2012-09-22 17:00:33Z Dusanka $
 * @author Dusanka Ilic
 * @copyright (C) 2012 - Dusanka Ilic, All rights reserved.
 * @authorEmail: gog27.mail@gmail.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html, see LICENSE.txt
**/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Joomla! ShareThisBar Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage
 */

class plgSystemSharethisbar extends JPlugin
{

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//Set the language in the class
		$config = JFactory::getConfig();
	}
	
	/**
	 * onAfterInitialise handler
	 * 
	 * @access	public
	 * @return null
	 */
	function onAfterInitialise()
	{

		$app	= JFactory::getApplication();

		// doesnt apply to admin
		if ($app->isAdmin()) {
			return;  
		}
                
                // Check to see if it has already been loaded.
		static $loaded;
		if (!empty($loaded)) {
			return;
		}

	        // mootools lib.
                JHtml::_('behavior.framework');
                   
    $js .= " // <![CDATA[        
    var _gaq = _gaq || [];
    ";
    $js .= "  
    // Copyright (C) 2012 Dusanka Ilic. All rights reserved.
    // License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
    /**
     * @scriptname ShareThisBar - A script contains SocialBar class definition. It is a part of the plugin ShareThisBar, 
     * which displays social media buttons in a vertical sliding bar.
     * @author gog27.mail@gmail.com (Dusanka Ilic)
     * @authorUrl ExtensionsHub.com
     * @version 3.0 
    */
    
    var SocialBar = new Class({  
    Implements : [ Options ],
    options : {
            position : 'left',
            swidth : '0',
            sheight : '0',
            corrtop : '50',    
            corrbottom : '0',
            corleft : '100',
            corright : '-100',
            backcolor : '#fefefe',
            border : '1px solid #cbcbcb',
            boxshadow : '1px 1px 3px #dbdbdb',
            borderradius: '5px 5px 5px 5px',
            effectson: '1',
            transitioneff: '0',
            transitiondur: '1000',
            hreffb: '',
            hrefgoogle: '',
            hreftwitter: '',
            fb_datawidth: '62',
            fb_delay: '0',
            fb_xmlns: '0',
            googleanalyt: '0',
            minresshow: '1292'
    },
    initialize: function(idSelector, options){
                                     
                                 this.divId = document.getElementsByTagName('body')[0];  
				 
                                 this.setOptions(options);
              
                                 var objRef = {
                                    left: 0,
                                    top: 0,
                                    bottom: Window.getScrollSize().y,
                                    right: Window.getScrollSize().x
                                  }; 
                                  this.divIdCoord = objRef; 
                                           
				 this.createBar();
    },    
    createBar: function(){
    
           // kreiraj objekt inpt klase INPUT button 
            this.barContainer = new Element('div', {
				'id':'socialbar',
				'styles': {
				'position':'absolute',
                                'top': this.divIdCoord.top,           
				'cursor':'pointer',
                                'text-align': 'center',
                                'left': '-2000px'   
                                }
            }); ";    
        $js .=  "
        this.barContainer.setStyles({
            'background-color': this.options.backcolor,
            'border': this.options.border,
            'box-shadow': this.options.boxshadow,
            'border-radius': this.options.borderradius   
        }); ";
	$js .=  "
        // Push container into DOM.
 
        this.barContainer.inject(this.divId);  
        ";
        
          $parm_sharethisbarlink = $this->params->get('sharethisbarlink'); 
          
          // Spread word
          $parm_spreadword = $this->params->get('spreadword'); 
          $parm_spreadwordpic = $this->params->get('spreadwordpic'); 
          
          // facebook
          $fbDatawidth = (string)$this->params->get('fb_datawidth');
          $parm_fb_datawidth = ($fbDatawidth) ? ' data-width="'.$fbDatawidth.'"' : '';
          
          $fbHref = $this->params->get('hreffb');
          $parm_fb_href = ($fbHref) ? ' data-href="'.$fbHref.'"' : '';
          
          // twitter
          $parm_twitter_src = 'https://platform.twitter.com/widgets/tweet_button.html?count=vertical';
          
          $twittHref = $this->params->get('hreftwitter');
          $parm_twitter_href = ($twittHref) ? '&url='.urlencode($twittHref).'&counturl='.urlencode($twittHref) : '';
          
          $parm_twitter_src .= $parm_twitter_href;
          
          $twittLang = $this->params->get('twitter_locale');
          $parm_twitter_lang = ($twittLang) ? '&lang='.$twittLang : '';
          $parm_twitter_src .=  $parm_twitter_lang;
          
          $twittRelated = $this->params->get('twitter_datarelated');
          $parm_twitter_related = ($twittRelated) ? '&related='.$twittRelated : '';
          $parm_twitter_src .=  $parm_twitter_related;

          $twittText = $this->params->get('twitter_text');
          $parm_twitter_text = ($twittText) ? '&text='.$twittText : '';
          $parm_twitter_src .= $parm_twitter_text;
          
          $twittVia = $this->params->get('twitter_via');
          $parm_twitter_via = ($twittVia) ? '&via='.$twittVia : '';
          $parm_twitter_src .= $parm_twitter_via; 
          
          // google       
          $googleHref = $this->params->get('hrefgoogle'); 
          $parm_google_href = ($googleHref) ? ' data-href="'.$googleHref.'"' : '';
          
          // linkedin
          $linkedinHref = $this->params->get('hreflinkedin');
          $parm_linkedin_href = ($linkedinHref) ? ' data-url="'.$linkedinHref.'"' : '';
          
          // pinterest
          $parm_pinterest_href = urlencode($this->params->get('hrefpinterest'));
          $parm_pinterest_picture = urlencode($this->params->get('picturepinterest'));
          $parm_pinterest_desc = $this->params->get('descpinterest'); 
            
          $js .= " var barContainerHtml=''; ";
          
          if ($parm_spreadword) {
              $js .= " barContainerHtml += '<div id=\"spreadword\" style=\"margin-left:2px;height:50px;width:75px;background: url(\'/plugins/system/sharethisbar/images/".$parm_spreadwordpic."\') no-repeat scroll center center transparent\" alt=\"Spread the Word\" title=\"Spread the Word\"></div>'; "; 
          }
          
          // Social buttons ordering. i is the number of social buttons.
          for ($i = 1; $i <= 5; $i++) {
            // make parameter names.
            $in = 'order_'.(string)$i;
            $parmname = $this->params->get($in);
            
            switch ($parmname) {
                case 'fb':
                  $js .=  " barContainerHtml += '<div><div style=\"margin:3px 5px 3px 3px;text-align:center!important;left:0px;height=auto\" class=\"fb-like\" ".$parm_fb_href." data-send=\"false\" data-layout=\"box_count\" ".$parm_fb_datawidth." data-show-faces=\"false\" data-font=\"arial\"></div></div>'; ";
                break;
                case 'go':
                  $js .= " barContainerHtml += '<div style=\"margin:3px auto;text-align:center!important;height=auto\" class=\"plusone-container\"><div class=\"g-plusone\" data-size=\"tall\" ".$parm_google_href." data-count=\"true\" data-source=\"google:developers\"><\/div><\/div>'; ";    
                break;
                case 'tw':            
                 $js .=  " barContainerHtml += '<div style=\"margin:3px auto;height=auto\"><iframe allowtransparency=\"true\" frameborder=\"0\" scrolling=\"no\" src=\"".$parm_twitter_src."\" style=\"width:60px; height:62px;\"><\/iframe><\/div>'; "; 
                break;
                case 'li':
		     $js .=  " barContainerHtml += '<div style=\"margin:3px auto;height:auto\"><script type=\"IN\/Share\" ".$parm_linkedin_href." data-counter=\"top\" data-onsuccess=\"linkedinShareGA\" data-onerror=\"linkedinShareErrorGA\"><\/script><\/div>'; ";
                break; 
                case 'pi':
                  // Does all Pinterest params exist?
                  if ($parm_pinterest_href != '' && $parm_pinterest_picture != '' && $parm_pinterest_desc != '') {
                     $js .=  " barContainerHtml += '<div style=\"margin:3px auto;height:auto\"><a href=\"http:\/\/pinterest.com\/pin\/create/button/\?url=".$parm_pinterest_href."\&media=".$parm_pinterest_picture."\&description=".$parm_pinterest_desc.  
"\" class=\"pin-it-button\" count-layout=\"vertical\"><img border=\"0\" src=\"\/\/assets.pinterest.com\/images\/PinExt.png\" title=\"Pin It\" \/><\/a><\/div>'; ";
                  }     
                  else {}    
                break; 
                default:
                    
            } // endswitch
              
          } //endfor
          
          if ($parm_sharethisbarlink) {
          $js .= " barContainerHtml += '<div style=\"margin:4px 2px 0;border:1px solid #68a0ca;border-radius:5px;background-color:#fff \"><a style=\"text-align:center;color:#68a0ca;font-weight:bold;font-style:italic;font-size:10px;text-decoration:none\" href=\"http://extensionshub.com/\" alt=\"ShareThisBar home page\" title=\"ShareThisBar home page\">ShareThisBar</a></div>'; "; 
          }
          // Set innerHTML for bar container.
          $js .=  " this.barContainer.set('html', barContainerHtml); "; 
        
         $js .=  "
         // Scroll window event.
        window.addEvent('scroll', function() {

	//  Viewport vertical position.  
	var dtop = Window.getScroll().y;
	
	// Document vertical scroll size.
	var dvsz = Window.getScrollSize().y;
	
	// Viewport vertical size.
	var vvs = Window.getSize().y;
	
	// Viewport bottom position indicator.
	var maxDownPos = false;
	
	var maxBottomViewportPosition = dvsz - vvs;
	
	if ((dtop <= maxBottomViewportPosition + 5)&&(dtop >= maxBottomViewportPosition - 5))  {
	   maxDownPos = true;
	}
	
	var barScrollSize = dtop + this.divIdCoord.top;  
        
        this.moveBar(barScrollSize, maxDownPos);
        
       }.bind(this));
       
       // Window resize event    
       window.addEvent('resize', function() {  

	var ws = Window.getSize().x;        
	 
	if (ws >=  this.options.minresshow.toInt()){                     
           this.barContainer.fade('show'); 
        } else  { 
	   this.barContainer.fade('hide'); 
        } 
        }.bind(this));    
    },      
    moveBar: function(barScroll, maxDownPos){
           
            barCoord = this.barContainer.getCoordinates();

            if (barScroll >= barCoord.top) {
            
                var barOffset = (barScroll > this.barBottomScrollBoundary) ? this.barBottomScrollBoundary : barScroll;
               
                // What if there is a room for a bar to move downward but this isnt possible because viewport reached the end ?
                // When maxDownPos == true, means that further document scroll isnt possible(scroll event cant be provoked and this method also), 
                // and in order to move bar on predefined bottom position, now is the time to move bar further downward.
                
	        if (barOffset == barScroll) {
	           
	               if (maxDownPos == true)
	               {
	                  barOffset = this.barBottomScrollBoundary;
	               }
	    
	        }
                   
            } else {
                var barOffset = (barScroll > this.divIdCoord.top) ? barScroll : this.divIdCoord.top;
            }
            
            // Transition effects approved?
          if (this.options.effectson == '1') {
         
          var transitionEffect;
           switch (this.options.transitioneff) {
                case '0' :
                transitionEffect = 'linear';
                break;
                case '1' :
                transitionEffect = 'bounce:in';
                break;
                case '2' :
                transitionEffect = 'bounce:out';
                break;
                case '3' :
                transitionEffect = 'elastic:in';
                break;
                case '4' :
                transitionEffect = 'elastic:out';
                break;
                default :
                transitionEffect = 'linear';             
           }  
          
            this.barContainer.set('tween',{
                property: 'top',
                transition: transitionEffect,       // allowed: 'bounce:in', 'bounce:out', 'linear','elastic:in','elastic:out'
                duration: this.options.transitiondur
            });

            this.barContainer.tween(barOffset);  
          } else {
            this.barContainer.setStyle('top', barOffset);
          }
             
    },
   correctBar: function(){ 
                                 
         // Bar position correction based on the referential div
         this.divIdCoord.top += this.options.corrtop.toInt(); 
         this.divIdCoord.bottom += this.options.corrbottom.toInt(); 
				 
	this.barContainer.setStyle('top', this.divIdCoord.top);  

        // Determine container height. If parms sheight=0 use getHeight to determine container height.
        var barContainerHeight = 0;
                
        if (this.options.sheight.toInt() != 0) {
              barContainerHeight = this.options.sheight.toInt();
        } else {
              barContainerHeight = this.barContainer.getHeight();
        }
                
        this.barContainer.setStyle('height', barContainerHeight);    
          
        //this.divIdCoord.bottom cant be greater than Window scroll size. 
        
        var wSize = Window.getScrollSize().y;
        
        var maxScroll = Math.min(wSize, this.divIdCoord.bottom);
             
        this.barBottomScrollBoundary = maxScroll - barContainerHeight;
                   
         // Feature left based on bar width.    
         var barContainerWidth = 0;
         if (this.options.swidth.toInt() != 0) {
                 barContainerWidth = this.options.swidth.toInt();
         } else {
                 barContainerWidth = this.barContainer.getWidth();           // Ovo ne radi dok je display:none
         }
                
         this.barContainer.setStyle('width', barContainerWidth);

         // Bar position relative to referential div, left or right.
         if (this.options.position == 'left') {
                   var barLeft = this.divIdCoord.left - barContainerWidth + this.options.corleft.toInt();
                   this.barContainer.setStyle('left', barLeft);
         } else {
                   this.barContainer.setStyle('left', this.divIdCoord.right + this.options.corright.toInt()); 
         }

   }
                                       
   }); //class definition end     
   ";



 $js .= " window.addEvent('domready', function() {  "; 
                
                $js .= " SocialBarObj = new SocialBar('"; 
        
                $arrParams = $this->params->toArray();
                
                $strParms = " ";
                foreach ($arrParams as $k => $v) {

                    if ($k != 'tag_id')
                    {
                        
                        if ($v != '') {
                            $strParms .= $k.":'".$v."',";
                        } 
                        
                    }
                }
                
               //Trim last comma
               $strParms = rtrim($strParms,',');
                
                $js .= $divId."'".",{".$strParms."}); ";

    
     $js .= "  var prepareFB = function(d,loc){

     var js, id = 'facebook-jssdk'; 
     if (d.getElementById(id)) {return;}
     
     js = d.createElement('script'); js.id = id; 
     js.async = true;
     
     js.src = '//connect.facebook.net/' + loc + '/all.js'; 
          
     d.getElementsByTagName('head')[0].appendChild(js);
     
     }; ";

     $js .= "  var fbInitFun = function(d,apid){  

     // if FB app key exists 
     if (apid) {
     
     var js, id = 'facebook-jsfun'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id;
     js.text = 'window.fbAsyncInit = function() {'+
                'FB.init({'+  
                'appId  :\'' + apid + '\',' +
                'status : true,' +
                'cookie : true,' +
                'xfbml  : true' +      
                '});  ' +
                'var ganlt = parseInt(SocialBarObj.options.googleanalyt); ' +
                'if (ganlt == \'1\') {' +
                'FB.Event.subscribe(\'edge.create\',' + 
    		'	function(targetUrl) { ' + 
    		'         if (typeof _gaq == \'object\') {  ' +
        	'            _gaq.push([\'_trackSocial\',\'facebook\',\'like\',targetUrl]); ' + 
        	'         } ' +
    		'	} ' +
		'); ' +
		'FB.Event.subscribe(\'edge.remove\',' + 
    		'	function(targetUrl) { ' + 
    		'         if (typeof _gaq == \'object\') {  ' +
        	'            _gaq.push([\'_trackSocial\',\'facebook\',\'unlike\',targetUrl]); ' + 
        	'         } ' +
    		'	} ' +
		'); ' +
		'} ' +
                'var fbdl1 = parseInt(SocialBarObj.options.fb_delay); ' +
                'if (fbdl1 == 0) {' +
                'FB.Event.subscribe(\'xfbml.render\',' +
                '   function() {' +
                '      SocialBarObj.correctBar();' +
                '   }' +
                '); ' +
                '} ' +
             
                '}; // end fbAsyncInit ' ;
       } else {
       
       var js, id = 'facebook-jsfun'; if (d.getElementById(id)) {return;}
       js = d.createElement('script'); js.id = id;
       js.text = 'window.fbAsyncInit = function() {'+
                'FB.init({'+  
                'status : true,' +
                'cookie : true,' +
                'xfbml  : true' +      
                '});  ' +
                'var ganlt = parseInt(SocialBarObj.options.googleanalyt); ' +
                'if (ganlt == \'1\') {' +
             	'FB.Event.subscribe(\'edge.create\',' + 
    		'	function(targetUrl) { ' + 
        	'            _gaq.push([\'_trackSocial\',\'facebook\',\'like\',targetUrl]); ' + 
    		'	} ' +
		'); ' +
		'FB.Event.subscribe(\'edge.remove\',' + 
    		'	function(targetUrl) { ' + 
    		'         if (typeof _gaq == \'object\') {  ' +
        	'            _gaq.push([\'_trackSocial\',\'facebook\',\'unlike\',targetUrl]); ' + 
        	'         } ' +
    		'	} ' +
		'); ' +
		'} ' +
                'var fbdl1 = parseInt(SocialBarObj.options.fb_delay); ' +
                'if (fbdl1 == 0) {' +
                'FB.Event.subscribe(\'xfbml.render\',' +
                '   function() {' +
                '      SocialBarObj.correctBar();' +
                '   }' +
                '); ' +
                '} ' +
                
                '}; // end fbAsyncInit ' ;
       }         
                    
     d.getElementsByTagName('head')[0].appendChild(js);
     }; "; 
      
    $js .= " prepareFB(document,'".$this->params->get('fb_locale')."'); ";  
    
    $fbAppkey = (string)$this->params->get('fb_appkey');

    if ($fbAppkey) { 
      $js .= " fbInitFun(document,'".(string)$this->params->get('fb_appkey')."'); ";
    } else {               
      $js .= " fbInitFun(document); ";
    }
                    
    $js .= "
                var fbdl = parseInt(SocialBarObj.options.fb_delay)*1000;
                if (fbdl > 0)
                {             
                    // Call function with delay. Delay in seconds is determined in plugin parameters.
                    (function(){SocialBarObj.correctBar();}).delay(fbdl);  
                } "; 

    $js .= " var prepareGooglePlus = function(d, loc){

     var po, id = 'google-plus'; if (d.getElementById(id)) {return;}
     var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
     po.src = 'https://apis.google.com/js/plusone.js'; po.id = id;
     
     var s = d.getElementsByTagName('script')[0]; 
     s.parentNode.insertBefore(po, s);
     
     window.___gcfg = {lang: loc };
    }
                                
 
    window.twttr = (function (d,s,id) {

      var t, js, fjs = d.getElementsByTagName(s)[0];

      if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;

      js.src='//platform.twitter.com/widgets.js'; fjs.parentNode.insertBefore(js, fjs);

      return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });

    }(document, 'script', 'twitter-wjs'));
  
  
    function extractParamFromUri(uri, paramName) {
      if (!uri) {
       return;
      }
     var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
     var params = regex.exec(uri);
     if (params != null) {
       return unescape(params[1]);
     }
     return;
    }


    // Callback function called after click on the tweet button.
    trackTwitter = function(intent_event) {

     var opt_target; //Default value is undefined.

     if (intent_event && intent_event.type == 'tweet' || intent_event.type == 'click') {
     // extract url  
     if (intent_event.target.nodeName == 'IFRAME') {
      opt_target = extractParamFromUri(intent_event.target.src, 'url');
     }
     // Action decription.
     var socialAction = intent_event.type + ((intent_event.type == 'click') ?
        '-' + intent_event.region : ''); //append the type of click to action
    // Push to Google analytics
    _gaq.push(['_trackSocial', 'twitter', socialAction, opt_target]);
   
    }

   }

   // Wrap event bindings - Wait for async js to load
   twttr.ready(function (twttr) {
    //event bindings
    twttr.events.bind('tweet', trackTwitter);
    twttr.events.bind('click', trackTwitter);
   });  
   ";

   $js .= "prepareGooglePlus(document, '".$this->params->get('google_loc')."'); ";
   
   $js .= "  var prepareLinkedIn = function(d){

     var js, id = 'linkedin-jssdk'; 
     if (d.getElementById(id)) {return;}
     
     js = d.createElement('script'); js.id = id; 
     
     js.src = '//platform.linkedin.com/in.js';  
          
     d.getElementsByTagName('head')[0].appendChild(js);
     
     }; 
     
     //  LinkedIn Share button Tracking
     linkedinShareGA = function(urlshared) {
       _gaq.push(['_trackSocial', 'LinkedIn', 'Share', urlshared]); 
     };
     
     //  LinkedIn Share button Tracking - error sharing the url  
     linkedinShareErrorGA = function(urlshared) { 
       _gaq.push(['_trackSocial', 'LinkedIn', 'Share Error', urlshared]);  
     }; 
     ";
     
    $js .= " prepareLinkedIn(document); ";  
    
    $js .= "  var preparePinterest = function(d){

     var js, id = 'pinterest-jssdk'; 
     if (d.getElementById(id)) {return;}
     
     js = d.createElement('script'); js.id = id; 
     
     js.src = '//assets.pinterest.com/js/pinit.js';  
          
     d.getElementsByTagName('head')[0].appendChild(js);
     
     }; 
     ";
     
   $js .= " preparePinterest(document); ";
   
   $js .= " (function() {window.fireEvent('resize');})(); ";  
   

   $js .= " }); // domready  ]]> ";    
       
			 $document = & JFactory::getDocument();
       
       // Load the script into the document head.
       $document->addScriptDeclaration($js);
       
			 $doctype =  $document->getType();

       // Only render for HTML output
       if ( $doctype !== 'html' ) { return; }
 
       // FB meta tag og:image
       $fbimg = (string)$this->params->get('fb_ogimg');
       if ($fbimg != '') {   
         $custTag = '<meta property="og:image" content="'.$fbimg.'" />';
         $document->addCustomTag($custTag); 
       }
       
       // FB meta tag fb:app_id
       if ($fbAppkey) {   
         $custTag = '<meta property="fb:app_id" content="'.$fbAppkey.'" />';
         $document->addCustomTag($custTag);
       }
       
       // Ensure the files aren't loaded more than once.
       $loaded = true;      
       
         
                
		            
   }  // onAfterInitialise 
        
        
   function onAfterRender()
   {
        $app = JFactory::getApplication();

	if ($app->isAdmin()) {
		return;
	}
	
        $document = & JFactory::getDocument();
	$doctype	= $document->getType();

	// Only render for HTML output
	if ( $doctype !== 'html' ) { return; }
       
	$body = JResponse::getBody();
        
        if ($this->params->get('fb_xmlns')) {
          // If user wants Open Graph meta tags.
          $body = str_replace("<html ", "<html xmlns:fb=\"http://www.facebook.com/2008/fbml\" xmlns:og=\"http://ogp.me/ns#\" ", $body);
        }
        
	JResponse::setBody($body); 
   }
        
        
}  //class plgSystemSocialbar


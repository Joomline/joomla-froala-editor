<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
defined('_JEXEC') or die;
jimport('joomla.document.html.html');
JLoader::register('FroalaHelper', JPATH_ROOT . '/components/com_froala/helpers/froala.php');

class PlgEditorFroala extends JPlugin
{
    private
        $lang,
        $buttons,
        $profile;

    function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
        $this->lang = $this->getLang();
        $this->profile = FroalaHelper::loadProfile();
        $this->buttons = array();
        $this->theme = $this->profile->get('theme', 'gray');
        $this->buttons = $this->profile->get('buttons', array());
        $inlineStyles = $this->profile->get('inlineStyles', '');
        if(empty($inlineStyles)){
            $key = array_search('inlineStyle', $this->buttons);
            if($key !== false){
                unset($this->buttons[$key]);
            }
        }
        if(!$this->profile->get('file_upload',0)){
            $key = array_search('uploadFile', $this->buttons);
            if($key !== false){
                unset($this->buttons[$key]);
            }
        }
    }

	public function onInit()
	{
        JHtml::_('jquery.framework');
        $doc = JFactory::getDocument();
        $app = JFactory::getApplication();
        $client = $app->isAdmin() ? 'administrator' : 'site';
        $secret_key = $this->profile->get('secret_key', '');

        setcookie(md5('froalaEditor'), $client, time()+60*60, '/');

        $app->setUserState('froalaEditor.secret', $secret_key);

        $doc->addStyleSheet(JUri::root().'plugins/editors/froala/assets/css/font-awesome.min.css');
        $doc->addStyleSheet(JUri::root().'plugins/editors/froala/assets/css/froala_editor.min.css');
        $doc->addStyleSheet(JUri::root().'plugins/editors/froala/assets/css/froala_style.min.css');
        $doc->addStyleSheet(JUri::root().'plugins/editors/froala/assets/css/themes/'.$this->theme.'.css');

        $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/froala_editor.min.js');
        $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/langs/'.$this->lang.'.js');
        $doc->addScriptDeclaration('
            jQuery(document).ready(function(){
                jQuery(\'head\').append(\'<!-- Include IE8 JS. --><!--[if lt IE 9]><script src="'.JUri::root().'plugins/editors/froala/assets/js/froala_editor_ie8.min.js"></script><![endif]-->\');
            });
        ');

        if($this->profile->get('resize_editor', 0)){
            $doc->addStyleSheet(JUri::root().'plugins/editors/froala/assets/jquery-ui-1.11.4.custom/jquery-ui.min.css');
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/jquery-ui-1.11.4.custom/jquery-ui.min.js');
        }

        if(in_array('blockStyle', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/block_styles.min.js');
        }

        if(in_array('color', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/colors.min.js');
        }

        if(in_array('fontFamily', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/font_family.min.js');
        }

        if(in_array('fontSize', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/font_size.min.js');
        }

        if(in_array('fullscreen', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/fullscreen.min.js');
        }

        if(in_array('inlineStyle', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/inline_styles.min.js');
        }

        if(in_array('insertVideo', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/video.min.js');
        }

        if(in_array('insertOrderedList', $this->buttons) || in_array('insertUnorderedList', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/lists.min.js');
        }

        if(in_array('table', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/tables.min.js');
        }

        if(in_array('uploadFile', $this->buttons)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/file_upload.min.js');
        }

        if($this->profile->get('allow_media_manager', 1)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/media_manager.min.js');
        }

        if($this->profile->get('convert_urls', 1)){
            $doc->addScript(JUri::root().'plugins/editors/froala/assets/js/plugins/urls.min.js');
        }
    }

	public function onGetContent($editor)
	{
		return "jQuery('#$editor').editable('getHTML', true, true);";
	}

	public function onSetContent($editor, $html)
	{
        return "jQuery('#$editor').editable('setHTML', '$html', false);";
	}

	public function onSave($editor)
	{
        return "
        var froalaEditor = jQuery('#$editor').data('fa.editable');
        if(froalaEditor.isHTML){
            froalaEditor.html();
        }
        froalaEditor.sync();
        ";
	}

	public function onGetInsertMethod($name)
	{
		$doc = JFactory::getDocument();

        $js = "
            function isBrowserIE()
			{
				return navigator.appName==\"Microsoft Internet Explorer\";
			}

			function jInsertEditorText( text, editor )
			{
			    if(text.indexOf('<img') === 0)
			    {
			        var image = jQuery(text),
			            src = image.attr('src'),
			            alt = image.attr('alt');
			        if(src.indexOf('/') !==0 && src.indexOf('http') !==0)
			        {
			            src = '".JUri::root()."' + src;
			            text = '<img src=\"'+src+'\" alt=\"'+alt+'\" />'
			        }
			    }
			    jQuery('#$name').editable('insertHTML', text, true);
			}

			var global_ie_bookmark = false;

			function IeCursorFix()
			{
				if (isBrowserIE())
				{
					tinyMCE.execCommand('mceInsertContent', false, '');
					global_ie_bookmark = tinyMCE.activeEditor.selection.getBookmark(false);
				}
				return true;
			}";
		$doc->addScriptDeclaration($js);

		return true;
	}

	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null)
	{
        $doc = JFactory::getDocument();
        $app = JFactory::getApplication();

		if (empty($id))
		{
			$id = $name;
		}

        $aScriptStrings = array();
        $editorButtons = (is_array($this->buttons) && count($this->buttons)) ? implode("', '", $this->buttons) : '';
        $baseUrl = JUri::base().'?option=com_froala&task=';
        $preloaderSrc = JUri::root().'plugins/editors/froala/assets/img/preloader.gif';
        $paragraphy = $this->profile->get('paragraphy', 1) ? 'true' : 'false';
        $plainPaste = $this->profile->get('plain_paste', 0) ? 'true' : 'false';
        $maxFileSize = (float)$this->profile->get('max_file_size', 2);
        $maxImageSize = (float)$this->profile->get('max_image_size', 2);
        $froalaContaynerId = 'froala_'.$id;
        $cookieName = $id.'_froala_height';
        $editorCookieHeight = (!empty($_COOKIE[$cookieName])) ? (int)$_COOKIE[$cookieName] : 0;
        if($editorCookieHeight > 90)
        {
            $height = $editorCookieHeight-90;
        }

        $aScriptStrings[] = 'inlineMode: false';
        $aScriptStrings[] = "language: '$this->lang'";
        $aScriptStrings[] = "buttons: ['$editorButtons']";
        $aScriptStrings[] = "theme: '$this->theme'";
        $aScriptStrings[] = "width: 'D'";
        $aScriptStrings[] = "height: '$height'";
        $aScriptStrings[] = "paragraphy: $paragraphy";
        $aScriptStrings[] = "plainPaste: $plainPaste";
        $aScriptStrings[] = "maxFileSize: 1024 * 1024 * $maxFileSize";
        $aScriptStrings[] = "maxImageSize: 1024 * 1024 * $maxImageSize";
        $aScriptStrings[] = "preloaderSrc: '$preloaderSrc'";
        $aScriptStrings[] = "spellcheck: true";
        $aScriptStrings[] = "defaultImageWidth: ".(int)$this->profile->get('default_image_width', 300);

        if($this->profile->get('paste_image', 0)){
            $aScriptStrings[] = 'pasteImage: true';
            $aScriptStrings[] = "pastedImagesUploadURL: '".$baseUrl."pastedImagesUpload'";
        }
        else{
            $aScriptStrings[] = 'pasteImage: false';
        }

        if($this->profile->get('allow_media_manager', 1)){
            $aScriptStrings[] = 'mediaManager: true';
            $aScriptStrings[] = "imageDeleteURL: '".$baseUrl."imageDelete'";
            $aScriptStrings[] = "imagesLoadURL: '".$baseUrl."imageGetJson'";
        }
        else{
            $aScriptStrings[] = 'mediaManager: false';
        }

        if(!$this->profile->get('allow_comments', 1))
        {
            $aScriptStrings[] = 'allowComments: false';
        }

        if(!$this->profile->get('image_upload', 0)){
            $aScriptStrings[] = 'imageUpload: false';
        }
        else{
            $aScriptStrings[] = "imageUploadURL: '".$baseUrl."imageUpload'";
        }

        if(!$this->profile->get('shortcuts', 1))
        {
            $aScriptStrings[] = 'shortcuts: false';
        }

        if($this->profile->get('allow_script', 0)){
            $aScriptStrings[] = 'allowScript: true';
        }

        if($this->profile->get('allow_style', 0)){
            $aScriptStrings[] = 'allowStyle: true';
        }

        if($this->profile->get('disable_right_click', 1)){
            $aScriptStrings[] = 'disableRightClick: true';
        }

        $allowedAttrs = $this->profile->get('allowed_attrs', array());
        if(is_array($allowedAttrs) && count($allowedAttrs) && !in_array('*', $allowedAttrs)){
            $aScriptStrings[] = "allowedAttrs: ['".implode("', '", $allowedAttrs)."']";
        }
        $allowedBlankTags = $this->prepareString($this->profile->get('allowed_blank_tags', ''));
        if(!empty($allowedBlankTags)){
            $aScriptStrings[] = "allowedBlankTags: ['$allowedBlankTags']";
        }
        $allowedFileTypes = $this->prepareString($this->profile->get('allowed_file_types', 'application/pdf, application/msword'));
        if(!empty($allowedFileTypes)){
            $aScriptStrings[] = "allowedFileTypes: ['$allowedFileTypes']";
        }
        $allowedImageTypes = $this->prepareString($this->profile->get('allowed_image_types', 'jpeg, jpg, png, gif'));
        if(!empty($allowedImageTypes)){
            $aScriptStrings[] = "allowedImageTypes: ['$allowedImageTypes']";
        }
        $allowedTags = $this->prepareString($this->profile->get('allowed_tags', ''));
        if(!empty($allowedTags)){
            $aScriptStrings[] = "allowedTags: ['$allowedTags']";
        }
        $inlineStyles = $this->prepareObjectString($this->profile->get('inlineStyles', ''));
        if(!empty($inlineStyles)){
            $aScriptStrings[] = "inlineStyles: {".$inlineStyles."}";
        }
        $blockStyles = $this->profile->get('blockStyles', '');
        if(!empty($blockStyles) && $this->profile->get('blockStyle', 1)){
            $blockStyles = $this->prepareBlockStyles($blockStyles);
            if(!empty($blockStyles)){
                $aScriptStrings[] = "blockStyles: ".$blockStyles;
            }
        }
        $iconClasses = $this->profile->get('iconClasses', '');
        if(!empty($iconClasses)){
            $iconClasses = $this->prepareString($iconClasses);
            if(!empty($iconClasses)){
                $aScriptStrings[] = "iconClasses: ['".$iconClasses."']";
            }
        }

        $onEditableFileError = '';
        if(in_array('uploadFile', $this->buttons)){
            $aScriptStrings[] = "fileUploadURL: '".$baseUrl."fileUpload'";
            $onEditableFileError = ".on('editable.fileError', function (e, editor, error) {
                      // Custom error message returned from the server.
                      if (error.code == 0) { alert(error.code+': '+error.message); }
                      // Bad link.
                      else if (error.code == 1) { alert(error.code+': '+error.message); }
                      // No link in upload response.
                      else if (error.code == 2) { alert(error.code+': '+error.message);}
                      // Error during file upload.
                      else if (error.code == 3) { alert(error.code+': '+error.message); }
                      // Parsing response failed.
                      else if (error.code == 4) { alert(error.code+': '+error.message); }
                      // File too text-large.
                      else if (error.code == 5) { alert(error.code+': '+error.message); }
                      // Invalid file type.
                      else if (error.code == 6) { alert(error.code+': '+error.message); }
                      // File can be uploaded only to same domain in IE 8 and IE 9.
                      else if (error.code == 7) { alert(error.code+': '+error.message); }
                    })
            ";
        }

        $scriptStrings = '';
        if(count($aScriptStrings)){
            $scriptStrings = implode(",\n                ", $aScriptStrings);
        }

        $sanitize_url = '';
        if($this->profile->get('sanitize_url', 1) == 0 && $app->isAdmin()){
            $sanitize_url = '
              jQuery.Editable.prototype.sanitizeURL = function (url) {
                return url;
              }';
        }

        $resizable = '';
        if($this->profile->get('resize_editor', 0)){
            $store = '';
            if($this->profile->get('store_resize_height', 0)){
                $store_resize_days = (int)$this->profile->get('store_resize_days', 365);
                $store = ",
                stop: function( event, ui ){
                    var newHeight = ui.size.height;
                    date = new Date;
                    date.setDate(date.getDate() + $store_resize_days);
                    document.cookie = '$cookieName='+newHeight+'; expires=' + date.toUTCString();
                }";
            }
            $resizable = "
            jQuery('#$froalaContaynerId div.froala-box').resizable({
                alsoResize: 'div.froala-wrapper'$store
            });";
        }

        $doc->addScriptDeclaration("
            jQuery(function() {
              $sanitize_url
              jQuery('#$id').editable({
                $scriptStrings
                })$onEditableFileError
                $resizable
            });
        ");

        // Data object for the layout
        $textarea = new stdClass;
        $textarea->name    = $name;
        $textarea->id      = $id;
        $textarea->cols    = $col;
        $textarea->rows    = $row;
        $textarea->width   = $width;
        $textarea->height  = $height;
        $textarea->content = $content;

        $editor = '<div class="editor" id="'.$froalaContaynerId.'">';
        $editor .= JLayoutHelper::render('joomla.tinymce.textarea', $textarea);
        $editor .= $this->_displayButtons($id, $buttons, $asset, $author);
        $editor .= '</div>';

        return $editor;
	}

    /**
     * Displays the editor buttons.
     *
     * @param   string  $name     The editor name
     * @param   mixed   $buttons  [array with button objects | boolean true to display buttons]
     * @param   string  $asset    The object asset
     * @param   object  $author   The author.
     *
     * @return  string HTML
     */
    private function _displayButtons($name, $buttons, $asset, $author)
    {
        $return = '';

        $args = array(
            'name'  => $name,
            'event' => 'onGetInsertMethod'
        );

        $results = (array) $this->update($args);

        if ($results)
        {
            foreach ($results as $result)
            {
                if (is_string($result) && trim($result))
                {
                    $return .= $result;
                }
            }
        }

        if (is_array($buttons) || (is_bool($buttons) && $buttons))
        {
            $buttons = $this->_subject->getButtons($name, $buttons, $asset, $author);

            $return .= JLayoutHelper::render('joomla.tinymce.buttons', $buttons);
        }

        return $return;
    }

    private function getLang()
    {
        $sysLang = JFactory::getLanguage()->getTag();

        $langs = array(
            'ar-DZ' => 'ar',
            'bs-BA' => 'bs',
            'cs-CZ' => 'cs',
            'da-DK' => 'da',
            'de-DE' => 'de',
            'en-GB' => 'en_gb',
            'es-ES' => 'es',
            'fa-IR' => 'fa',
            'fi-FI' => 'fi',
            'fr-FR' => 'fr',
            'he-IL' => 'he',
            'hr-HR' => 'hr',
            'hu-HU' => 'hu',
            'it-IT' => 'it',
            'ja-JP' => 'ja',
            'ko-KR' => 'ko',
            'srp-ME' => 'me',
            'nb-NO' => 'nb',
            'nl-NL' => 'nl',
            'pl-PL' => 'pl',
            'pt-BR' => 'pt_br',
            'pt-PT' => 'pt_pt',
            'ro-RO' => 'ro',
            'ru-RU' => 'ru',
            'sr-YU' => 'sr',
            'sv-SE' => 'sv',
            'th-TH' => 'th',
            'tr-TR' => 'tr',
            'uk-UA' => 'uk',
            'zh-CN' => 'zh_cn',
            'zh-TW' => 'zh_tw',
            'et-EE' => 'et',
        );

        return (isset($langs[$sysLang])) ? $langs[$sysLang] : 'en_gb';
    }

    private function prepareString($string, $delimiter=',',$glue="', '"){
        $string = trim($string);
        if(empty($string)){
            return '';
        }
        $string = str_replace(array("'", '"', '  ', ' ', "\n", "\r", '.'), '', $string);
        $array = explode($delimiter, $string);
        return implode($glue, $array);
    }

    private function prepareObjectString($string){
        $string = trim($string);
        if(empty($string))
        {
            return '';
        }
        $return = array();
        $string = str_replace(array("'", '"',  '  ', ' ', "\n", "\r"), '', $string);
        $array = explode(',', $string);
        if(count($array))
        {
            foreach($array as $value)
            {
                $value = explode('::', $value);

                if(count($value) <2)
                {
                    continue;
                }

                $return[] = "'".$value[0]."': '".$value[1]."'";
            }
        }
        else
        {
            return '';
        }
        return implode(', ', $return);
    }

    private function prepareBlockStyles($string){
        $string = trim($string);
        if(empty($string))
        {
            return '';
        }

        $data = array();
        $string = str_replace(array("'", '"',  '  ', ' ', "\n", "\r"), '', $string);
        $array = explode(',', $string);
        if(count($array))
        {
            foreach($array as $value)
            {
                $value = explode('::', $value);

                if(count($value) <2)
                {
                    continue;
                }
                $data[$value[0]][$value[1]] = $value[1];
            }
        }
        else
        {
            return '';
        }

        if(!count($data))
        {
            return '';
        }

        $data = json_encode($data);

        return $data;
    }


}

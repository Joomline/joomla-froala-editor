<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// защита от прямого доступа
defined('_JEXEC') or die('@-_-@');

jimport('joomla.form.formfield');

class JFormFieldButtons extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Buttons';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
        JHtml::_('jquery.framework');
        JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JUri::root().'plugins/editors/froala/assets/css/font-awesome.min.css');
        $document->addStyleSheet(JURI::root().'administrator/components/com_froala/assets/css/style.css');
        $document->addScript(JURI::root().'administrator/components/com_froala/assets/js/jquery.ui.core.js');
        $document->addScript(JURI::root().'administrator/components/com_froala/assets/js/jquery.ui.widget.js');
        $document->addScript(JURI::root().'administrator/components/com_froala/assets/js/jquery.ui.mouse.js');
        $document->addScript(JURI::root().'administrator/components/com_froala/assets/js/jquery.ui.sortable.js');
        $document->addScript(JURI::root().'administrator/components/com_froala/assets/js/script.js');

        $value = empty($this->value) ? array() : (array)json_decode($this->value);

        $allbuttons =  array(
            'html' => array(
                'class' => 'fa fa-code',
                'text' => JText::_('COM_FROALA_BUTTONS_HTML')
            ),
            'table' => array(
                'class' => 'fa fa-table',
                'text' => JText::_('COM_FROALA_BUTTONS_TABLE')
            ),
            'blockStyle' => array(
                'class' => 'fa fa-magic',
                'text' => JText::_('COM_FROALA_BUTTONS_BLOCKSTYLE')
            ),
            'inlineStyle' => array(
                'class' => 'fa fa-paint-brush',
                'text' => JText::_('COM_FROALA_BUTTONS_INLINESTYLE')
            ),
            'formatBlock' => array(
                'class' => 'fa fa-paragraph',
                'text' => JText::_('COM_FROALA_BUTTONS_FORMATBLOCK')
            ),
            'bold' => array(
                'class' => 'fa fa-bold',
                'text' => JText::_('COM_FROALA_BUTTONS_BOLD')
            ),
            'italic' => array(
                'class' => 'fa fa-italic',
                'text' => JText::_('COM_FROALA_BUTTONS_ITALIC')
            ),
            'underline' => array(
                'class' => 'fa fa-underline',
                'text' => JText::_('COM_FROALA_BUTTONS_UNDERLINE')
            ),
            'strikeThrough' => array(
                'class' => 'fa fa-strikethrough',
                'text' => JText::_('COM_FROALA_BUTTONS_STRIKETHROUGH')
            ),
            'subscript' => array(
                'class' => 'fa fa-subscript',
                'text' => JText::_('COM_FROALA_BUTTONS_SUBSCRIPT')
            ),
            'superscript' => array(
                'class' => 'fa fa-superscript',
                'text' => JText::_('COM_FROALA_BUTTONS_SUPERSCRIPT')
            ),
            'fontFamily' => array(
                'class' => 'fa fa-font',
                'text' => JText::_('COM_FROALA_BUTTONS_FONTFAMILY')
            ),
            'fontSize' => array(
                'class' => 'fa fa-text-height',
                'text' => JText::_('COM_FROALA_BUTTONS_FONTSIZE')
            ),
            'color' => array(
                'class' => 'fa fa-tint',
                'text' => JText::_('COM_FROALA_BUTTONS_COLOR')
            ),
            'align' => array(
                'class' => 'fa fa-align-left',
                'text' => JText::_('COM_FROALA_BUTTONS_ALIGN')
            ),
            'insertOrderedList' => array(
                'class' => 'fa fa-list-ol',
                'text' => JText::_('COM_FROALA_BUTTONS_INSERTORDEREDLIST')
            ),
            'insertUnorderedList' => array(
                'class' => 'fa fa-list-ul',
                'text' => JText::_('COM_FROALA_BUTTONS_INSERTUNORDEREDLIST')
            ),
            'outdent' => array(
                'class' => 'fa fa-dedent',
                'text' => JText::_('COM_FROALA_BUTTONS_OUTDENT')
            ),
            'indent' => array(
                'class' => 'fa fa-indent',
                'text' => JText::_('COM_FROALA_BUTTONS_INDENT')
            ),
            'selectAll' => array(
                'class' => 'fa fa-file-text',
                'text' => JText::_('COM_FROALA_BUTTONS_SELECTALL')
            ),
            'createLink' => array(
                'class' => 'fa fa-link',
                'text' => JText::_('COM_FROALA_BUTTONS_CREATELINK')
            ),
            'insertImage' => array(
                'class' => 'fa fa-picture-o',
                'text' => JText::_('COM_FROALA_BUTTONS_INSERTIMAGE')
            ),
            'insertVideo' => array(
                'class' => 'fa fa-video-camera',
                'text' => JText::_('COM_FROALA_BUTTONS_INSERTVIDEO')
            ),
            'undo' => array(
                'class' => 'fa fa-undo',
                'text' => JText::_('COM_FROALA_BUTTONS_UNDO')
            ),
            'redo' => array(
                'class' => 'fa fa-repeat',
                'text' => JText::_('COM_FROALA_BUTTONS_REDO')
            ),
            'insertHorizontalRule' => array(
                'class' => 'fa fa-minus',
                'text' => JText::_('COM_FROALA_BUTTONS_INSERTHORIZONTALRULE')
            ),
            'uploadFile' => array(
                'class' => 'fa fa-paperclip',
                'text' => JText::_('COM_FROALA_BUTTONS_UPLOADFILE')
            ),
            'removeFormat' => array(
                'class' => 'fa fa-eraser',
                'text' => JText::_('COM_FROALA_BUTTONS_REMOVEFORMAT')
            ),
            'fullscreen' => array(
                'class' => 'fa fa-expand',
                'text' => JText::_('COM_FROALA_BUTTONS_FULLSCREEN')
            ),
            'sep' => array(
                'class' => 'fa fa-bolt',
                'text' => JText::_('COM_FROALA_BUTTONS_SEPARATOR')
            )
        );

        $notWorkingButtons = array_diff_key($allbuttons, array_flip($value));

        $html = '
        <h3>'. JText::_('COM_FROALA_WORKED_BUTTONS').'</h3>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span12">
                <ul class="positions" id="workingButtons">';

        if(count($value))
        {
            foreach($value as $k)
            {
                $v = $allbuttons[$k];
                $html .= '
                <li title="'.$v['text'].'"><i class="'.$v['class'].'"></i><input type="hidden" name="'.$this->name.'" value="'.$k.'"></li>';
            }
        }

        $html .= '
                </ul>
            </div>
        </div>
        <h3>'. JText::_('COM_FROALA_NOT_WORKED_BUTTONS').'</h3>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span12">
                <ul class="positions" id="notWorkingButtons">';

        if(count($notWorkingButtons))
        {
            foreach($notWorkingButtons as $k => $v)
            {
                $html .= '
                <li title="'.$v['text'].'"><i class="'.$v['class'].'"></i><input type="hidden" disabled="disabled" name="'.$this->name.'" value="'.$k.'"></li>';
            }
        }

        $v = $allbuttons['sep'];

        for($i=0; $i<10; $i++)
        {
            $html .= '
            <li title="'.$v['text'].'"><i class="'.$v['class'].'"></i><input type="hidden" disabled="disabled" name="'.$this->name.'" value="sep"></li>';
        }

        $html .= '
                </ul>
            </div>
        </div>';

		return $html;
	}
}
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

class PlgSystemFroala extends JPlugin
{
    public function onAfterDispatch()
    {
        if (JFactory::getApplication()->isAdmin())
        {
            return true;
        }

        $file = 'plugins/editors/froala/assets/css/froala_content.min.css';

        if(is_file(JPATH_ROOT.'/'.$file))
        {
            JFactory::getDocument()->addStyleSheet(JUri::root().$file);
        }

        return true;
    }
}

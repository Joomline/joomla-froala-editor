<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
// no direct access
defined('_JEXEC') or die ;

class plgSystemFroalaInstallerScript
{
	public function postflight($type, $parent)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->update('`#__extensions`')
            ->set('`enabled` = 1')
            ->where('`element` = '.$db->quote('froala'))
            ->where('`type` = '.$db->quote('plugin'))
            ->where('`folder` = '.$db->quote('system'))
           ;
        $db->setQuery($query)->execute();
    }
}
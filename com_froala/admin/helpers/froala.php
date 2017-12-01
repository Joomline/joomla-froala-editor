<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
defined( '_JEXEC' ) or die;

/**
 * Class FroalaHelper
 */
class FroalaAdminHelper
{
	/**
	 * Добавление подменю
	 * @param String $vName
	 */
	static function addSubmenu( $vName )
	{
		JHtmlSidebar::addEntry(
			JText::_( 'PROFILE_SUBMENU' ),
			'index.php?option=com_froala&view=profiles',
			$vName == 'profiles' );
	}
}
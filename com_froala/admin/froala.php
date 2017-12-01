<?php
defined( '_JEXEC' ) or die; // No direct access
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
$controller = JControllerLegacy::getInstance( 'froala' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
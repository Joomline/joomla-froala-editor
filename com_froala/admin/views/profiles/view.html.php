<?php

// No direct access
defined( '_JEXEC' ) or die;

/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
class FroalaViewProfiles extends JViewLegacy
{
	/**
	 * @var $items stdClass[]
	 */
	public $items;
	/**
	 * @var $pagination JPagination
	 */
	public $pagination;
	/**
	 * @var $state JObject
	 */
	public $state;
	/**
	 * @var $user JUser
	 */
	public $user;

	/**
	 * Method to display the current pattern
	 * @param type $tpl
	 */
	public function display( $tpl = null )
	{
		$this->items = $this->get( 'Items' );
		$this->pagination = $this->get( 'Pagination' );
		$this->state = $this->get( 'State' );

		$this->user = JFactory::getUser();

		$this->loadHelper( 'froala' );

		$this->addToolbar();
		//froalaHelper::addSubmenu( 'profiles' );
		//$this->sidebar = JHtmlSidebar::render();

		parent::display( $tpl );
	}

	/**
	 * Method to display the toolbar
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_FROALA_PROFILES' ) );
		$canDo = FroalaHelper::getActions();

		if ( $canDo->get( 'core.create' ) || ( count( $this->user->getAuthorisedCategories( 'com_froala', 'core.create' ) ) ) > 0 ) {
			JToolBarHelper::addNew( 'profile.add' );
		}

		if ( ( $canDo->get( 'core.edit' ) ) || ( $canDo->get( 'core.edit.own' ) ) ) {
			JToolBarHelper::editList( 'profile.edit' );
		}

		if ( $canDo->get( 'core.edit.state' ) ) {
			JToolBarHelper::divider();
			JToolBarHelper::publish( 'profiles.publish', 'JTOOLBAR_PUBLISH', true );
			JToolBarHelper::unpublish( 'profiles.unpublish', 'JTOOLBAR_UNPUBLISH', true );
			JToolBarHelper::divider();

			if ( $canDo->get( 'core.delete' ) ) {
				JToolBarHelper::deleteList( 'DELETE_QUERY_STRING', 'profiles.delete', 'JTOOLBAR_DELETE' );
				JToolBarHelper::divider();
			}

			if ( $canDo->get( 'core.admin' ) ) {
				JToolBarHelper::preferences( 'com_froala' );
				JToolBarHelper::divider();
			}
		}		
	}

	protected function getSortFields()
	{
		return array(
			'ordering' => JText::_( 'JGRID_HEADING_ORDERING' ),
			'published' => JText::_( 'JSTATUS' ),
			'title' => JText::_( 'JGLOBAL_TITLE' ),
			'created_by' => JText::_( 'JAUTHOR' ),
			'created' => JText::_( 'JDATE' ),
			'id' => JText::_( 'JGRID_HEADING_ID' )
		);
	}
}
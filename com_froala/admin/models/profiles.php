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
class FroalaModelProfiles extends JModelList
{

	/**
	 * Конструктор класса
	 * @param Array $config
	 */
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields'] = array( 'title', 'published', 'ordering', '`default`',  'description', 'id' );
		}
		parent::__construct( $config );
	}

	/**
	 * @param String $ordering
	 * @param String $direction
	 */
	protected function populateState( $ordering = null, $direction = null )
	{
		parent::populateState( 'id', 'desc' );
	}

	/**
	 * Составление запроса для получения списка записей
	 * @return JDatabaseQuery
	 */
	protected function getListQuery()
	{
        $query = $this->getDbo()->getQuery( true );
        $query->select( '*' );
        $query->from( '#__froala_profiles' );

		$orderCol = $this->state->get( 'list.ordering' );
		$orderDirn = $this->state->get( 'list.direction' );
		$query->order( $this->getDbo()->escape( $orderCol . ' ' . $orderDirn ) );
		return $query;
	}

}


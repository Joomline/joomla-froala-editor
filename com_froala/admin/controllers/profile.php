<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
// No direct access
defined( '_JEXEC' ) or die;

/**
 * Controller for edit current element
 * @author Arkadiy Sedelnikov, JoomLine
 */
class FroalaControllerProfile extends JControllerForm
{

	/**
	 * Class constructor
	 * @param array $config
	 */
	function __construct( $config = array() )
	{
		$this->view_list = 'profiles';
		parent::__construct( $config );
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param    array    $data    An array of input data.
	 * @param    string    $key    The name of the key for the primary key.
	 *
	 * @return    boolean
	 * @since    1.6
	 */
	protected function allowEdit( $data = array(), $key = 'id' )
	{
		// Initialise variables.
		$recordId = ( int )isset( $data[$key] ) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get( 'id' );
		// Check general edit permission first.
		if ( $user->authorise( 'core.edit' ) ) {
			return true;
		}
		// Fallback on edit.own.
		// First test if the permission is available.
		if ( $user->authorise( 'core.edit.own' ) ) {
			// Now test the owner is the user.
			$ownerId = ( int )isset( $data['created_by'] ) ? $data['created_by'] : 0;
			if ( empty( $ownerId ) && $recordId ) {
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem( $recordId );

				if ( empty( $record ) ) {
					return false;
				}

				$ownerId = $record->created_by;
			}
			// If the owner matches 'me' then do the test.
			if ( $ownerId == $userId ) {
				return true;
			}
		}
		// Since there is no asset tracking, revert to the component permissions.
		return false;
	}
}
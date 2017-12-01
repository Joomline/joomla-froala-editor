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
 * Controller for list current element
 * @author Aleks.Denezh
 */
class FroalaControllerProfiles extends JControllerAdmin
{

	/**
	 * Class constructor
	 * @param array $config
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

	/**
	 * Method to get current model
	 * @param String $name (model name)
	 * @param String $prefix (model prefox)
	 * @param Array $config
	 * @return model for current element
	 */
	public function getModel( $name = 'Profile', $prefix = 'FroalaModel', $config = array( 'ignore_request' => true ) )
	{
		return parent::getModel( $name, $prefix, $config );
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 * @return    void
	 */
	public function saveOrderAjax()
	{
		$pks = $this->input->post->get( 'cid', array(), 'array' );
		$order = $this->input->post->get( 'order', array(), 'array' );

		// Sanitize the input
		JArrayHelper::toInteger( $pks );
		JArrayHelper::toInteger( $order );

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder( $pks, $order );

		if ( $return ) {
			echo '1';
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}
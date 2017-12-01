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
 * Модель редактирования текущего элемента
 * @author Arkadiy Sedelnikov, JoomLine
 */
class FroalaModelProfile extends JModelAdmin {

	/**
	 * загрузка текущей формы
	 * @param Array $data
	 * @param Boolean $loadData
	 * @return Object form data
	 */
	public function getForm( $data = array( ), $loadData = true ) {
		$form = $this->loadForm( 'com_froala.profile', 'profile', array( 'control' => 'jform', 'load_data' => $loadData ) );
		if ( empty( $form ) ) {
			return false;
		}
		$user = JFactory::getUser();
		if ( !$user->authorise( 'core.edit.state', '#__froala_profiles.' . $this->getState( 'extdataedit.id' ) ) ) {
			$form->setFieldAttribute( 'published', 'disabled', 'true' );
			$form->setFieldAttribute( 'published', 'filter', 'unset' );
		}
		return $form;
	}

	/**
	 * @param Int $id (object identifier)
	 * @return Object (current item)
	 */
	public function getItem( $id = null )
    {
		if ( $item = parent::getItem( $id ) )
        {
            if(isset($item->user_groups))
            {
                $item->user_groups = json_decode($item->user_groups);
            }

            if(isset($item->allowed_attrs))
            {
                $item->allowed_attrs = json_decode($item->allowed_attrs);
            }
		}

		return $item;
	}

	/**
	 * @param string $type
	 * @param string $prefix
	 * @param array $config
	 * @return JTable|mixed
	 */
	public function getTable( $type = 'froala_profiles', $prefix = 'Table', $config = array( ) ) {
		return JTable::getInstance( $type, $prefix, $config );
	}

	/**
	 * Загрузка данных в форму
	 * @return Object
	 */
	protected function loadFormData() {
		$data = JFactory::getApplication()->getUserState( 'com_froala.edit.profile.data', array() );
		if ( empty( $data ) ) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Запрет удаления записи
	 * @param object $record
	 * @return bool
	 */
	protected function canDelete( $record )
	{
		if ( !empty( $record->id ) ) {
			return JFactory::getUser()->authorise( 'core.delete', '#__froala_profiles.' . (int)$record->id );
		}
	}

	/**
	 * Запрет изменения состояния
	 * @param object $record
	 * @return bool
	 */
	protected function canEditState( $record )
	{
		$user = JFactory::getUser();

		// Check for existing article.
		if ( !empty( $record->id ) ) {
			return $user->authorise( 'core.edit.state');
		} // New article, so check against the category.
		elseif ( !empty( $record->catid ) ) {
			return $user->authorise( 'core.edit.state');
		} // Default to component settings if neither article nor category known.
		else {
			return parent::canEditState( 'com_froala' );
		}
	}

    public function save($data)
    {
        if(isset($data['user_groups']) && is_array($data['user_groups'])){
            $data['user_groups'] = json_encode($data['user_groups']);
        }
        else{
            $data['user_groups'] = json_encode(array());
        }

        if(isset($data['allowed_attrs']) && is_array($data['allowed_attrs'])){
            $data['allowed_attrs'] = json_encode($data['allowed_attrs']);
        }

        if(isset($data['buttons']) && is_array($data['buttons'])){
            $data['buttons'] = json_encode($data['buttons']);
        }

        $countDefault = $this->getCountDefault((int)$data['id']);

        if($data['default'] == 1 && $countDefault > 0){
            $this->setDefaultToNull();
        }

        if($countDefault == 0)
        {
            $data['default'] = 1;
        }

        return parent::save($data);
    }

    private function getCountDefault($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from('#__froala_profiles')
            ->where('`default` = 1')
            ->where('`id` != '.$db->quote($id));
        return $db->setQuery($query)->loadResult();
    }

    private function setDefaultToNull()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__froala_profiles')
            ->set('`default` = 0');
        return $db->setQuery($query)->loadResult();
    }
}
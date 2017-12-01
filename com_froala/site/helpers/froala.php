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
class FroalaHelper
{
    public static function readDirRf($dirName, &$fileArray, $root)
    {
        if ($handle = opendir($dirName)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dirName . '/' . $file)) {
                        self::readDirRf($dirName . '/' . $file, $fileArray, $root);

                    } elseif (is_file($dirName . '/' . $file)) {
                        $dName = str_replace(JPATH_ROOT . '/', '', $dirName);
                        $dir = substr(str_replace($root, '', $dirName), 1);
                        $filePath = str_replace('//', '/', $dName . '/' . $file);
                        $fileArray[] = array('dir' => str_replace('/', ' / ', $dir), 'file' => $filePath, 'title' => $file);
                    }
                }
            }
            closedir($handle);
        }
    }

    public static function loadProfile()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('`#__froala_profiles`')
            ->where('`published` = 1')
        ;

        $profiles = $db->setQuery($query)->loadObjectList();
        $default = null;
        $my = array();
        $user = JFactory::getUser();

        if(count($profiles) == 0){
            return false;
        }
        else if(count($profiles) == 1)
        {
            $profile = $profiles[0];
        }
        else
        {
            foreach($profiles as $v)
            {
                $v->user_groups = (array)json_decode($v->user_groups);

                if($v->default == 1)
                {
                    $default = $v;
                }

                if(is_array($v->user_groups) && count($intersect = array_intersect($v->user_groups, $user->groups)))
                {
                    foreach($intersect as $k)
                    {
                        $my[$k] = $v;
                    }
                }
            }

            if(count($my)>0)
            {
                $maxKey = max(array_keys($my));
                $profile = $my[$maxKey];
            }
            else
            {
                $profile = $default;
            }
        }

        $profile->images_load_url = str_replace('{userId}', JFactory::getUser()->id, $profile->images_load_url);
        $profile->files_upload_url = str_replace('{userId}', JFactory::getUser()->id, $profile->files_upload_url);
        $profile->buttons = (array)json_decode($profile->buttons);

        $config = JComponentHelper::getParams('com_froala');
        $profile->secret_key = $config->get('secret_key', '');
        $profile->theme = $config->get('theme', 'gray');
        $profile->inlineStyles = $config->get('inlineStyles', "'Big Red': 'font-size: 20px; color: red;', 'Small Blue': 'font-size: 14px; color: blue;'");
        $profile->blockStyles = $config->get('blockStyles', 'h1: muted, h1: text-warning, p: muted, p: text-warning');
        $profile->default_image_width = $config->get('default_image_width', 300);
        $profile->iconClasses = $config->get('iconClasses', '');

        $return  = new FroalaProfile($profile);

        return $return;
    }

    public static function prepareString($string, $delimiter = ',')
    {
        $string = trim($string);

        if (empty($string))
        {
            return array();
        }

        $string = str_replace(array("'", '"', '  ', ' ', "\n", "\r", '.'), '', $string);
        $array = explode($delimiter, $string);
        return $array;
    }

    public static function createSecret()
    {
        $secret = self::generate_hash(15);

        // получаем параметры компонента com_users
        $params = JComponentHelper::getParams('com_froala');
        // устанавливаем требуемое значение
        $params->set('secret_key', $secret);

        // записываем измененные параметры в БД
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__extensions'));
        $query->set($db->quoteName('params') . '= ' . $db->quote((string)$params));
        $query->where($db->quoteName('element') . ' = ' . $db->quote('com_froala'));
        $query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
        $db->setQuery($query);

        if($db->execute())
        {
            return $secret;
        }
        return false;
    }

    private static function generate_hash($number)
    {
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z');
        $hash = '';
        // Генерируем хэш
        for($i = 0; $i < $number; $i++)
        {
            $index = rand(0, count($arr) - 1);
            $hash .= $arr[$index];
        }
        return $hash;
    }

    /**
     * Получаем доступные действия для текущего пользователя
     * @return JObject
     */
    public static function getActions()
    {
        $user = JFactory::getUser();
        $result = new JObject;
        $assetName = 'com_froala';
        $actions = JAccess::getActions( $assetName );
        foreach ( $actions as $action ) {
            $result->set( $action->name, $user->authorise( $action->name, $assetName ) );
        }
        return $result;
    }
}

class FroalaProfile
{
    private $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    function get($name, $default=null)
    {
        return (isset($this->data->$name)) ? $this->data->$name : $default;
    }
}
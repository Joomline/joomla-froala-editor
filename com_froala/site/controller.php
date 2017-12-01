<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
defined( '_JEXEC' ) or die; // No direct access

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

JLoader::register('FroalaHelper', __DIR__ . '/helpers/froala.php');

class FroalaController extends JControllerLegacy
{
    private
        $profile,
        $app,
        $rootURL;

    function __construct($config = array()){
        parent::__construct($config);
        $this->profile = FroalaHelper::loadProfile();
        $this->app = JFactory::getApplication();
        $this->rootURL = JUri::root(true);
        if(empty($this->rootURL)){
            $this->rootURL = '/';
        }
    }

    function display( $cachable = false, $urlparams = array())
    {
        $this->default_view = 'profiles';
        parent::display( $cachable, $urlparams);
    }

    public function imageGetJson()
    {
        $this->check('allow_media_manager', 1);

        $imgPath = trim($this->profile->get('images_load_url', 'images'), '/');
        $imgPath = JPATH_ROOT . '/' . $imgPath;

        if(!is_dir($imgPath))
        {
            JFolder::create($imgPath);
        }

        $allowedImageTypes = FroalaHelper::prepareString($this->profile->get('allowed_image_types', ''));
        $response = array();

        FroalaHelper::readDirRf($imgPath, $fileArray, $imgPath);

        if(is_array($fileArray) && count($fileArray))
        {
            foreach ($fileArray as $file)
            {
                $temp = explode(".", $file['file']);
                $extension = end($temp);

                if (!in_array($extension, $allowedImageTypes))
                {
                    continue;
                }
                array_push($response, $this->rootURL . $file['file']);
            }
        }

        $this->app->close(stripslashes(json_encode($response)));
    }

    public function imageUpload()
    {
        $this->check('image_upload', 0);

        $imgPath = trim($this->profile->get('images_load_url', 'images'), '/');
        $dir = JPATH_ROOT . '/' . $imgPath . '/';
        $imgUrl = $this->rootURL . $imgPath . '/';
        $allowedImageTypes = FroalaHelper::prepareString($this->profile->get('allowed_image_types', ''));
        $allowedFileTypes = FroalaHelper::prepareString($this->profile->get('allowed_file_types', ''));

        if(!is_dir($dir))
        {
            JFolder::create($dir);
        }

        if (empty($_FILES['file']))
        {
            $this->app->close(json_encode(array('error' => '$_FILES empty')));
        }

        if ($_FILES['file']['size'] == 0 || $_FILES['file']['size'] > $this->profile->get('max_image_size', 2) * 1024 * 1024)
        {
            $this->app->close(json_encode(array('error' => 'File size error')));
        }

        $temp = explode(".", $_FILES["file"]["name"]);

        $extension = end($temp);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES["file"]["tmp_name"]);

        if (in_array($mime, $allowedFileTypes) && in_array($extension, $allowedImageTypes))
        {
            $name = sha1(microtime()) . "." . $extension;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $name))
            {
                $response = new StdClass;
                $response->link = $imgUrl . $name;
                $this->app->close(stripslashes(json_encode($response)));
            }
        }
        $this->app->close('');
    }

    public function imageDelete()
    {
        $this->check('image_delete', 0);

        $src = JFactory::getApplication('site')->input->getString('src', '');
        $rootUrlLen = strlen($this->rootURL);

        $file = JPATH_ROOT . '/'. substr($src, $rootUrlLen);

        if (is_file($file))
        {
            unlink($file);
            $this->app->close('ok');
        }
        else
        {
            $this->app->close(json_encode(array('error' => 'URL '.$src.' FILE ' . $file . ' not set')));
        }

    }

    public function pastedImagesUpload()
    {
        $this->check('paste_image', 0);

        $response = new StdClass;

        if(!$this->profile->get('paste_image', 0))
        {
            $response->error = 'Paste Image not allowed.';
        }
        else
        {
            $imgPath = trim($this->profile->get('images_load_url', 'images'), '/');
            $dir = JPATH_ROOT . '/' . $imgPath . '/';
            $imgUrl = $this->rootURL . $imgPath . '/';

            if(!is_dir($dir))
            {
                JFolder::create($dir);
            }

            $img = $_POST['image'];

            if (empty($img))
            {
                $response->error = 'Empty image source.';
            }
            else
            {
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);

                $name = sha1(microtime()) . ".png";

                $file = $dir . $name;
                $success = JFile::write($file, $data);

                if ($success)
                {
                    // Generate response.
                    $response->link = $imgUrl . $name;
                }
                else
                {
                    $response->error = 'Could not write file.';
                }
            }
        }

        $this->app->close(stripslashes(json_encode($response)));
    }

    public function fileUpload()
    {
        $this->check('file_upload', 0);

        $filesUploadURL = trim($this->profile->get('files_upload_url', 'images'), '/');
        $dir = JPATH_ROOT . '/' . $filesUploadURL . '/';
        $fileUrl = $this->rootURL . $filesUploadURL . '/';
        $allowedFileExt = FroalaHelper::prepareString($this->profile->get('allowed_file_ext', ''));
        $allowedFileTypes = FroalaHelper::prepareString($this->profile->get('allowed_file_types', ''));

        if(!is_dir($dir))
        {
            JFolder::create($dir);
        }

        if (empty($_FILES['file']))
        {
            $this->app->close(json_encode(array('error' => '$_FILES empty')));
        }

        if ($_FILES['file']['size'] == 0 || $_FILES['file']['size'] > $this->profile->get('max_file_size', 2) * 1024 * 1024)
        {
            $this->app->close(json_encode(array('error' => 'File size error')));
        }

        $temp = explode(".", $_FILES["file"]["name"]);

        $extension = end($temp);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES["file"]["tmp_name"]);

        if (in_array($mime, $allowedFileTypes) && in_array($extension, $allowedFileExt))
        {
            $name = sha1(microtime()) . "." . $extension;
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $name))
            {
                $response = new StdClass;
                $response->link = $fileUrl . $name;
                $this->app->close(stripslashes(json_encode($response)));
            }
            else{
                $this->app->close(json_encode(array('error' => 'Error move uploaded file.')));
            }
        }
        else{
            $this->app->close(json_encode(array('error' => 'This file type "'.$mime.'" not allowed.')));
        }
    }

	private function check($profileSetting=null, $profileSettingDefault=0)
    {
        $client = empty($_COOKIE[md5('froalaEditor')]) ? '' : $_COOKIE[md5('froalaEditor')];
        $app = JFactory::getApplication($client);

        if (empty($client))
        {
            $app->close(json_encode(array('error' => 'Direct requests are prohibited')));
        }

        if($profileSetting && !$this->profile->get($profileSetting, $profileSettingDefault))
        {
            $app->close(json_encode(array('error' => 'Profile Setting '.$profileSetting.' not allowed')));
        }

        $requestSectet = $app->getUserState('froalaEditor.secret');
        $paramSecret = $this->profile->get('secret_key', '');

        if(empty($paramSecret))
        {
            $paramSecret = FroalaHelper::createSecret();
        }

        if ($requestSectet != $paramSecret)
        {
            $app->close(json_encode(array('error' => 'Secret key fail.')));
        }
    }
}
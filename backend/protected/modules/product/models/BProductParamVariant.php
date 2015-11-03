<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductParamVariant model(string $class = __CLASS__)
 *
 * @property string $id
 * @property string $param_id
 * @property string $name
 * @property string $notice
 * @property string $position
 *
 * @property BProductParamName $param
 */
class BProductParamVariant extends BActiveRecord
{
  const BASE_UPLOAD_PATH = 'f/upload/images/color/';

  public function rules()
  {
    return array(
      array('param_id, name', 'required'),
      //array('url', 'SUriValidator'),
      array('param_id, position', 'length', 'max' => 10),
      array('notice', 'length', 'max' => 255),
    );
  }

  public function relations()
  {
    return array(
      'param' => array(self::BELONGS_TO, 'BProductParamName', 'param_id'),
    );
  }

  public function getImage($field)
  {
    if( !empty($this->{$field}) )
    {
      return Yii::app()->request->hostInfo.'/'.self::BASE_UPLOAD_PATH.$this->{$field};
    }

    return Yii::app()->request->hostInfo.'/'.'i/sp.gif';
  }

  public function beforeSave()
  {
    if( isset($_FILES[get_class($this)]['name'][$this->id]) )
    {
      $data = Arr::get($_FILES[get_class($this)]['name'], $this->id);
      $field = key($data);

      if( $file = CUploadedFile::getInstanceByName(get_class($this)."[{$this->id}][{$field}]") )
      {
        $path = realpath(Yii::getPathOfAlias('frontend').'/..').'/'.self::BASE_UPLOAD_PATH;
        $fileName = $this->id.'.'.strtolower($file->getExtensionName());

        if( !file_exists($path) )
          CFileHelper::createDirectory($path, 0777, true);

        if( !$file->saveAs($path.$fileName) )
          throw new CHttpException(500, 'Ошибка загрузки файла '.$file->getName());

        @chmod($path.$fileName, 0777);
        $this->{$field} = $fileName;
      }
    }

    return true;
  }
}
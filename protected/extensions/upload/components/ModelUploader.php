<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 27.08.12
 */
class ModelUploader extends FileUploader
{
  public function __construct(UploadBehavior $behavior)
  {
    parent::__construct($behavior);

    if( empty($this->behavior->attribute) )
    {
      throw new CHttpException(500, 'В поведении UploadBehavior не задано свойство $attribute');
    }

    if( !$this->behavior->owner->hasAttribute($this->behavior->attribute) )
    {
      throw new CHttpException(500, 'В модели '.get_class($this->behavior->owner).' отсутствует поле '.$this->behavior->attribute);
    }
  }

  /**
   * Сохраняем файл в таблицу модели
   *
   * @param array $file
   *
   * @return bool
   */
  public function saveFile(array $file)
  {
    $this->behavior->owner->{$this->behavior->attribute} = $file['name'];
    $result = $this->behavior->owner->save();

    return $result ? $this->behavior->owner->id : false;
  }

  /**
   * @return CArrayDataProvider
   */
  public function getFiles()
  {
    $attr = $this->behavior->owner->{$this->behavior->attribute};

    if( !empty($attr) )
    {
      $file = array('id' => $this->behavior->owner->id,
                    'path' => Yii::app()->controller->module->getUploadUrl().$attr,
                    'thmb' => $this->getThumbPath($attr),
                    $this->behavior->attribute => $attr,
                   );
    }

    return new CArrayDataProvider(!empty($file) ? array($file) : array());
  }

  /**
   * @param $id
   *
   * @return string
   */
  public function getFileName($id)
  {
    return $this->behavior->owner->{$this->behavior->attribute};
  }

  /**
   * @param $id
   *
   * @return bool
   */
  public function deleteFile($id)
  {
    $this->behavior->owner->{$this->behavior->attribute} = null;
    return $this->behavior->owner->save();
  }
}

?>
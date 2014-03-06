<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.extensions.upload
 */
class TableUploader extends FileUploader
{
  public function __construct(UploadBehavior $behavior)
  {
    parent::__construct($behavior);

    $table = Yii::app()->db->schema->getTable($this->behavior->table);

    if( !isset($table->columns['name'], $table->columns['parent']) )
      throw new CHttpException(500, 'Таблица '.$this->behavior->table.' должна содержать столбцы `parent` и `name`');
  }

  /**
   * @param array $file
   *
   * @return bool
   */
  public function saveFile(array $file)
  {
    $table             = Yii::app()->db->schema->getTable($this->behavior->table);
    $columns           = array_intersect_key($file, $table->columns);
    $columns['parent'] = $this->behavior->owner->getPrimaryKey();
    $result            = Yii::app()->db->createCommand()->insert($this->behavior->table, $columns);

    return $result ? Yii::app()->db->getLastInsertID() : false;
  }

  /**
   * @return CArrayDataProvider
   */
  public function getFiles()
  {
    $files = Yii::app()->db->createCommand()->from($this->behavior->table)
                                            ->where('parent=:p', array(':p' => $this->behavior->owner->getPrimaryKey()))
                                            ->queryAll();

    foreach($files as &$file)
    {
      $file['path'] = Yii::app()->controller->module->getUploadUrl().$file['name'];
      $file['thmb'] = $this->getThumbPath($file['name']);
    }

    return new CArrayDataProvider($files);
  }

  /**
   * @param $id
   *
   * @return string
   */
  public function getFileName($id)
  {
    $file = Yii::app()->db->createCommand()->from($this->behavior->table)
                                           ->where('id=:id', array(':id' => $id))
                                           ->queryRow();

    return Arr::get($file, 'name', null);
  }

  /**
   * @param $id
   *
   * @return int
   */
  public function deleteFile($id)
  {
    return Yii::app()->db->createCommand()->delete($this->behavior->table, 'id=:id', array(':id' => $id));
  }
}
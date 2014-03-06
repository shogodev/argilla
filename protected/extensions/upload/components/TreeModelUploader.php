<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.extensions.upload

 * Класс для работы с загружаемыми файлами на моделях, которые обладают
 * NestedSets поведением.
 */
class TreeModelUploader extends ModelUploader
{
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
    $result = $this->behavior->owner->saveNode();

    return $result ? $this->behavior->owner->id : false;
  }

  /**
   * @param $id
   *
   * @return bool
   */
  public function deleteFile($id)
  {
    $this->behavior->owner->{$this->behavior->attribute} = null;
    return $this->behavior->owner->saveNode();
  }
}
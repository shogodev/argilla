<?php
/**
 * Класс для работы с загружаемыми файлами на моделях, которые обладают
 * NestedSets поведением.
 *
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 30.08.12
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

?>
<?php

Yii::import('backend.modules.directory.models.AbstractDirectory');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Directory
 */
class TestDirectory extends AbstractDirectory
{
  public static $tableName = '{{dir_test}}';

  public function tableName()
  {
    return self::$tableName;
  }
}
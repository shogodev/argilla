<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static AssignmentContent model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $section_id
 * @property integer $type_id
 * @property integer $category_id
 * @property integer $collection_id
 * @property string $location
 * @property string $content
 * @property integer $visible
 */
class AssignmentContent extends FActiveRecord
{
  private static $locations;

  public function tableName()
  {
    return '{{product_assignment_content}}';
  }

  /**
   * @return array
   */
  public function defaultScope()
  {
    return array(
      'condition' => 'visible = 1',
    );
  }

  /**
   * @param string $location
   * @param FActiveRecord|FActiveRecord[] $inputModels
   *
   * @return AssignmentContent|null
   */
  public function getContent($location, $inputModels)
  {
    $contentList = $this->getContentList($location, $inputModels);

    return Arr::reset($contentList);
  }

  /**
   * @param string $location
   * @param FActiveRecord|FActiveRecord[] $inputModels
   *
   * @return AssignmentContent[]|array()
   */
  public function getContentList($location, $inputModels)
  {
    $this->checkLocation($location);

    $criteria = new CDbCriteria();

    $models = is_array($inputModels) ? $inputModels : array($inputModels);
    $defaultValues = array(
      'section_id' => 0,
      'type_id' => 0,
      'category_id' => 0,
      'collection_id' => 0,
    );

    foreach($models as $model)
    {
      if( empty($model) )
        continue;
      $field = $this->getFieldForClass($model);
      $criteria->compare($field, $model->primaryKey);
      unset($defaultValues[$field]);
    }

    foreach($defaultValues as $field => $value)
      $criteria->compare($field, $value);

    $criteria->compare('location', $location);

    return $this->findAll($criteria);
  }

  public function __toString()
  {
    return $this->content;
  }


  protected function getFieldForClass($class)
  {
    $field = Utils::toSnakeCase(get_class($class));

    return str_replace('product_', '', $field).'_id';
  }

  private function checkLocation($location)
  {
    if( !self::$locations )
    {
      Yii::import('backend.modules.product.modules.assignmentContent.AssignmentContentModule');

      self::$locations = AssignmentContentModule::$locations;
    }

    if( !isset(self::$locations[$location]) )
      throw new CHttpException(500, 'Location '.$location.' не указан в свойстве $locations класса AssignmentContentModule');
  }
}
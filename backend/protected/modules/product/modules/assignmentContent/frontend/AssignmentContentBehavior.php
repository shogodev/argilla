<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример использования:
 *
 * return array(
 *  'assignmentContentBehavior' => array('class' => 'backend.modules.product.modules.assignmentContent.frontend.AssignmentContentBehavior'),
 * );
 */


/**
 * Class AssignmentContentBehavior
 */
class AssignmentContentBehavior extends SBehavior
{
  /**
   * @var AssignmentContent $assignmentContentModel
   */
  private $assignmentContentModel;

  public function init()
  {
    Yii::import('backend.modules.product.modules.assignmentContent.frontend.*');
    $this->assignmentContentModel = AssignmentContent::model();
  }

  /**
   * @param string $location
   * @param FActiveRecord|FActiveRecord[] $models
   *
   * @return AssignmentContent[]|array()
   */
  public function getAssignmentContentList($location, $models)
  {
    return $this->assignmentContentModel->getContentList($location, $models);
  }

  /**
   * @param string $location
   * @param FActiveRecord|FActiveRecord[] $models
   *
   * @return AssignmentContent|null
   */
  public function getAssignmentContent($location, $models)
  {
    return $this->assignmentContentModel->getContent($location, $models);
  }
} 
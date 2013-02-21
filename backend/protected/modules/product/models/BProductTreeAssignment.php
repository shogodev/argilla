<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductTreeAssignment model(string $class = __CLASS__)

 * @var string $srcModel
 * @var string $dstModel
 *
 * @property string $src
 * @property integer $src_id
 * @property string $dst
 * @property integer $dst_id
 *
 * <example>
 * Assign sections to TypeModels
 *
 * Type controller:
 *
 * $assignmentModel = BProductTreeAssignment::assignToModel($model, 'section');
 * $this->saveModels(array($model, $assignmentModel));
 *
 *
 * Type form view:
 *
 * echo $form->errorSummary(array($model, $assignmentModel));
 * ...
 * echo $form->dropDownAssignedRow($assignmentModel);
 *
 *
 * Type model afterDelete method
 *
 *  BProductTreeAssignment::assignToModel($this, 'section')->delete();
 *  return parent::afterDelete();
 *
 * </example>
 */
class BProductTreeAssignment extends BActiveRecord
{
  const BASE_MODEL = 'BProduct';
  const DST_FIELD  = 'dst_id';

  /**
   * @var string $srcModel модель, к которой привязываем параметр
   */
  static $srcModel;

  /**
   * @var string $dstModel модель, к id которой связываем с текущей моделью
   */
  static $dstModel;

  /**
   * Привязываемся к модели
   *
   * @param BActiveRecord $model
   * @param string $dstModel
   *
   * @return BProductTreeAssignment
   */
  public static function assignToModel(BActiveRecord $model, $dstModel)
  {
    self::$srcModel  = strtolower(str_replace(self::BASE_MODEL, "", get_class($model)));
    self::$dstModel = $dstModel;

    if( $model->getPrimaryKey() )
      $assignmentModel = BProductTreeAssignment::model()->findByPk(array('src' => self::$srcModel, 'src_id' => $model->getPrimaryKey()));

    if( !isset($assignmentModel) )
      $assignmentModel = new BProductTreeAssignment;

    return $assignmentModel;
  }

  public function rules()
  {
    return array(
      array(self::DST_FIELD, 'required'),
      array(self::DST_FIELD, 'length', 'max' => 10),
    );
  }

  /**
   * @return SActiveRecord[]
   * @throws CHttpException
   */
  public function getValues()
  {
    $className = self::BASE_MODEL.ucfirst(self::$dstModel);

    if( !class_exists($className) )
      throw new CHttpException('В модуле не найден класс '.$className);

    /**
     * @var BActiveRecord $model
     */
    $model = new $className();

    return $model->findAll();
  }

  public function setPrimaryKey($value)
  {
    parent::setPrimaryKey(array('src' => self::$srcModel, 'src_id' => $value));
  }

  public function beforeSave()
  {
    $this->setAttribute('dst', self::$dstModel);
    return parent::beforeSave();
  }

  public function attributeLabels()
  {
    $attributes = array(
      'category' => 'Бренд',
      'section' => 'Раздел',
    );

    return CMap::mergeArray(parent::attributeLabels(),
                            array(self::DST_FIELD => $attributes[self::$dstModel]));
  }
}
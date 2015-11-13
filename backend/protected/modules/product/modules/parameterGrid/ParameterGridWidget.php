<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения в формах
 * <div>
 *   <?php $form->widget('parameterGrid.ParameterGridWidget', array('model' => $model, 'header' => 'Цены и наличие'));?>
 * </div>
 */

/**
 * Class ParameterGridWidget
 */
class ParameterGridWidget extends CWidget
{
  public $header = 'Параметры';

  /**
   * @var $parameterKey - ключ параметра, если не указан брется с поведения parameterGridBehavior
   */
  public $parameterKey;

  /**
   * @var BProduct - модель продуктов с поведением parameterGridBehavior
   */
  public $model;

  /**
   * @var CDataProvider $dataProvider
   */
  private $dataProvider;

  public function init()
  {

    if( is_null($this->model) )
      throw new RequiredPropertiesException(get_class($this), 'model');

    if( is_null($this->model->asa('parameterGridBehavior')) )
      throw new CHttpException('500', 'Класс '.get_class($this->model).' должен иметь поведение parameterGridBehavior');

    $this->dataProvider = $this->model->getParametersDataProvider($this->parameterKey);
  }

  public function run()
  {
    if( !$this->isAvailable() )
      return;

    echo '<tr><th><label>'.$this->header.'</label></th><td>';
    $this->renderGrid();
    echo '</td></tr>';
  }

  private function renderGrid()
  {
    $productParam = BProductParam::model();
    $columns = $productParam->getMetaData()->columns;

    $updateButtonClass = 'parameter_grid_button';

    $optionColumns = array(
      array('name' => 'variant.name', 'header' => 'Название'),
    );

    if( isset($columns['articul']) )
    {
      $optionColumns[] = array(
        'name' => 'articul',
        'header' => 'Артикул',
        'class' => 'OnFlyEditField',
        'ajaxUrl' => Yii::app()->controller->createUrl('/product/parameterGrid/parameterGrid/onflyedit'),
        'htmlOptions' => array('class' => 'span5'),
      );
    }

    if( isset($columns['price']) )
    {
      $optionColumns[] = array(
        'name' => 'price',
        'header' => 'Цена',
        'class' => 'OnFlyEditField',
        'ajaxUrl' => Yii::app()->controller->createUrl('/product/parameterGrid/parameterGrid/onflyedit'),
        'htmlOptions' => array('class' => 'span2'),
      );
    }

    if( isset($columns['dump']) )
    {
      $optionColumns[] = array(
        'name' => 'dump',
        'class' => 'JToggleColumn',
        'action' => '/product/parameterGrid/parameterGrid/toggle',
        'header' => 'Наличие'
      );
    }

    $grid = $this->controller->widget('BGridView', array(
      'dataProvider' => $this->dataProvider,
      'template' => "{buttons}\n{items}\n{pager}\n{scripts}",
      'buttonsTemplate' => null,
      'columns' => $optionColumns
    ));

    $this->registerUpdateScript($grid->id, $updateButtonClass);
  }

  private function registerUpdateScript($widgetId, $updateButtonClass)
  {
    Yii::app()->clientScript->registerScript($updateButtonClass.'_script', "
      jQuery(document).on('click', '.{$updateButtonClass}', function(e) {
        e.preventDefault();
        assigner.open(this.href, {'updateGridId' : '{$widgetId}'});
      });
    ");
  }

  private function isAvailable()
  {
    return !$this->controller->popup && $this->controller->isUpdate() && $this->model->getParametersDataProvider()->getTotalItemCount() > 0;
  }
}
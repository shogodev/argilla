<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid
 */
Yii::import('bootstrap.widgets.TbGridView');

class BGridView extends TbGridView
{
  const FILTER_POS_SEPARATE = 'separate';

  public $type = 'striped bordered';

  public $enableHistory = true;

  public $template = "{filters}\n{buttons}\n{summary}\n{items}\n{pagesize}\n{pager}\n{buttons}\n{scripts}";

  public $buttonsTemplate = '//_form_button_create';

  public $filterPosition = 'separate';

  public $afterAjaxUpdate = 'notifyGridObservers';

  public $ajaxUpdate = 'flash-message';

  public $ajaxUpdateError;

  /**
   * @var BActiveDataProvider
   */
  public $dataProvider;

  public function init()
  {
    if( !isset($this->ajaxUpdateError) )
    {
      $this->ajaxUpdateError = 'function(xhr, textStatus, errorThrown, err){
        ajaxUpdateError(xhr, err);
      }';
    }

    parent::init();
  }

  public function renderFilters()
  {
    if( $this->filter !== null && $this->filterPosition === self::FILTER_POS_SEPARATE )
      $this->renderFilter();
  }

  public function renderFilter()
  {
    if( $this->filter === null )
      return;

    if( $this->filterPosition !== self::FILTER_POS_SEPARATE )
      parent::renderFilter();
    else
    {
      echo "<div class=\"{$this->filterCssClass}\">\n";
      foreach($this->columns as $column)
      {
        echo '<div class="filter-container">';
        /**
         * @var BDataColumn $column
         */
        $column->renderFilterCell();
        echo '</div>';
      }
      echo "<a href=\"".'?'.(!empty($this->owner->popup) ? 'popup=1' : '')."\" rel=\"tooltip\" class=\"btn btn-alone update\" title=\"Очистить\"></a>";
      echo "</div>\n";
    }
  }

  public function renderButtons()
  {
    if( !empty($this->buttonsTemplate) )
    {
      Yii::app()->controller->renderPartial($this->buttonsTemplate, array(
        'model' => !empty($this->filter) ? $this->filter : null,
        'grid' => $this
      ));
    }
  }

  public function renderPagesize()
  {
    if( $this->dataProvider instanceof BActiveDataProvider )
    {
      echo $this->dataProvider->getPageSizeForm();
    }
  }

  public function renderScripts()
  {
    Yii::app()->clientScript->registerScript('notifyGridObservers', "function notifyGridObservers(id, data) {
      if( jQuery.fn.yiiGridView.observers )
        jQuery.fn.yiiGridView.notifyObservers(id, data);
    }");

    $this->addObservers();
  }

  public function renderTableRow($row)
  {
    parent::renderTableRow($row);

    /**
     * @var BActiveRecord $model
     */

    $model = $this->dataProvider->data[$row];

    if( is_array($model) )
      return;

    foreach($model->behaviors()  as $behaviorName => $array)
    {
      /**
       * @var CBehavior $behavior
       */
      if( !$behavior = $model->asa($behaviorName) )
        continue;

      if( $behavior->hasEvent('onAfterRenderTableRow') )
        $behavior->raiseEvent('onAfterRenderTableRow', new CEvent($this, array('model' => $model)));
    }
  }

  protected function addObservers()
  {
    $afterAjaxUpdate = "function(id) {
      jQuery('.tooltip').remove();
      jQuery('.flash').animate({opacity: 0.5}, 5000).fadeOut('fast');
    }";

    Yii::app()->clientScript->registerScript(
      'observers'.$this->id,
      "jQuery.fn.yiiGridView.addObserver('".$this->id."', $afterAjaxUpdate)",
      CClientScript::POS_READY
    );
  }

  protected function initColumns()
  {
    foreach($this->columns as $i => $column)
    {
      if( is_array($column) && !isset($column['class']) )
        $this->columns[$i]['class'] = 'BDataColumn';
      else if( Yii::app()->controller->popup )
      {
        if( in_array($column['class'], array('BButtonColumn', 'BPopupColumn')) )
          unset($this->columns[$i]);
        else if( $column['class'] == 'JToggleColumn' )
          $this->columns[$i]['hideColumn'] = true;
      }
    }

    parent::initColumns();
  }

  /**
   * @param mixed $text
   *
   * @return CDataColumn|TbDataColumn
   * @throws CException
   */
  protected function createDataColumn($text)
  {
    if( !preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $text, $matches) )
      throw new CException(Yii::t('zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));

    $column       = new BDataColumn($this);
    $column->name = $matches[1];

    if( isset($matches[3]) && $matches[3] !== '' )
      $column->type = $matches[3];

    if( isset($matches[5]) )
      $column->header = $matches[5];

    return $column;
  }
}
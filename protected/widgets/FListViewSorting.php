<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 *
 * @property ProductController $owner
 */
class FListViewSorting extends CWidget
{
  public $containerClass = 'fl sort-block';

  public $dropDownContainerClass = 'fl sort-block';

  public $labelClass = 's12';

  public $listId = 'product_list';

  private $dropDowns;

  public function init()
  {
    $this->dropDowns = array(
      array(
        'label' => Yii::t('app', 'Сортировать по:'),
        'items' => array(
          'Не важно',
          'popular_up' => Yii::t('app', 'Сначала популярные'),
          'price_up' => Yii::t('app', 'Сначала дешевые'),
          'price_down' => Yii::t('app', 'Сначала дорогие'),
          'available_up' => Yii::t('app', 'Сначала "В наличии"'),
        )
      )
    );

    parent::init();
  }

  public function run()
  {
    foreach($this->dropDowns as $dropDown)
    {
      echo isset($this->containerClass) ? CHtml::tag('div', array('class' => $this->containerClass), false, false) : '';
      echo isset($this->labelClass) ? CHtml::tag('div', array('class' => $this->labelClass), $dropDown['label']) : $dropDown['label'];
      echo CHtml::tag('div', array('class' => $this->dropDownContainerClass), $this->renderDropDown($dropDown));
      echo isset($this->containerClass) ? CHtml::closeTag('div') : '';
    }
  }

  private function renderDropDown(array $dropDown)
  {
    return CHtml::dropDownList(false, $this->owner->sorting, $dropDown['items'], array(
      'onChange' => '$.fn.yiiListView.sortingHandler(this)',
      'data-list-id' => $this->listId,
      'autocomplete' => 'off',
    ));
  }
}
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
class FListViewPageSize extends CWidget
{
  public $containerClass = 'select-container';

  public $labelClass = 'sort-label';

  public $label = 'Отображать по:';

  public $dropDownContainerClass = 'product-list-pager';

  public $postUrl;

  public function init()
  {
    if( !isset($this->postUrl) )
      $this->postUrl = $this->owner->getActionUrl(true);

    parent::init();
  }

  public function run()
  {
    echo CHtml::tag('div', array('class' => $this->containerClass), false, false);
    echo CHtml::tag('span', array('class' => $this->labelClass), Yii::t('app', $this->label));
    echo '&nbsp;';
    echo CHtml::tag('div', array('class' => $this->dropDownContainerClass), $this->renderDropDown());
    echo CHtml::closeTag('div');
  }

  private function renderDropDown()
  {
    return CHtml::dropDownList(
      false,
      $this->owner->pageSize,
      $this->owner->pageSizeRange,
      array(
        'onChange' => '$.fn.yiiListView.pageSizeHandler(this)',
        'data-url' => $this->postUrl,
        'autocomplete' => 'off',
      )
    );
  }
}
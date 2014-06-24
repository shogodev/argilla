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
  public $label = 'Отображать по:';

  public $dropDownName = 'product-list-pager';

  public $labelClass = 'sort-label';

  public $containerClass = 'select-container';

  public $postUrl;

  public function init()
  {
    if( !isset($this->postUrl) )
      $this->postUrl = $this->owner->getActionUrl(false);

    parent::init();
  }

  public function run()
  {
    echo CHtml::tag('span', array('class' => $this->labelClass), Yii::t('app', $this->label));
    echo '&nbsp;';
    echo CHtml::tag('div', array('class' => $this->containerClass), $this->renderDropDown());
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
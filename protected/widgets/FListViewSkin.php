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
class FListViewSkin extends CWidget
{
  public $itemClass = 'view-icon';

  public $listId = 'product_list';

  private $links;

  public function init()
  {
    $this->links = array(
      array(
        'id' => 'line',
        'class' => $this->getSkinActiveClass('line').' view-1 '.$this->itemClass,
      ),
      array(
        'id' => 'tablet',
        'class' => $this->getSkinActiveClass('tablet').' view-2 '.$this->itemClass,
      )
    );

    parent::init();
  }

  public function run()
  {
    $options = array(
      'data-list-id' => $this->listId,
      'onClick' => 'return $.fn.yiiListView.skinHandler(this);'
    );

    foreach($this->links as $link)
      echo CHtml::link('', '#', CMap::mergeArray($options, $link));
  }

  /**
   * @param $skinId
   *
   * @return string
   */
  private function getSkinActiveClass($skinId)
  {
    if ($skinId === 'tablet')
      return $this->owner->isTabletView() ? 'active' : '';
    else
      return $this->owner->isTabletView() ? '' : 'active';
  }
}
<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 *
 * @property ProductController $owner
 *
 * Подключение:
 * <div class="short-filters-view-mode fl">
 *   <span class="label">Отображение</span>
 *   <?php $this->widget('FListViewSkin', array(
 *     'itemClass' => 'icon-list-toggle',
 *     'reverse' => true
 *   ))?>
 * </div>
 *
 *  Используется совместно с поведением TableListToggleBehavior
 */
class FListViewSkin extends CWidget
{
  public $itemClass = 'view-icon';

  public $listId = 'product_list';

  public $tableClass = 'catalog-view-1-icon';

  public $listClass = 'catalog-view-2-icon';

  public $reverse = false;

  private $links;

  public function init()
  {
    if( !Yii::app()->controller->asa('tableListToggleBehavior') )
      throw new CHttpException(500, 'Ошибка! Для корректной работы виджета FListViewSkin контроллер '.get_class(Yii::app()->controller).' должен иметь поведение tableListToggleBehavior');

    $this->links = array(
      array(
        'id' => 'line',
        'class' => $this->getSkinActiveClass('line'),
      ),
      array(
        'id' => 'tablet',
        'class' => $this->getSkinActiveClass('tablet'),
      )
    );

    if( $this->reverse )
      $this->links = array_reverse($this->links);

    parent::init();
  }

  public function run()
  {
    $options = array('data-list-id' => $this->listId);

    foreach($this->links as $link)
      echo CHtml::link('', '#', CMap::mergeArray($options, $link));

    Yii::app()->clientScript->registerScript('skinHandlerScript', "
    $('body #content').on('click', '.{$this->itemClass}' ,function(e) {
      e.preventDefault();
      if( $(this).hasClass('active') )
        return;

      $.cookie('lineView', $(this).attr('id') === 'tablet' ? 0 : 1, {path: '/'});
      $.fn.yiiListView.update('{$this->listId}');
    });");
  }

  /**
   * @param $skinId
   *
   * @return string
   */
  private function getSkinActiveClass($skinId)
  {
    $classes = array($this->itemClass);

    if( $skinId === 'tablet' )
    {
      $classes[] = $this->tableClass;
      if( $this->owner->isTable() )
        $classes[] = 'active';
    }
    else
    {
      $classes[] = $this->listClass;
      if( !$this->owner->isTable() )
        $classes[] = 'active';
    }

    return implode(' ', $classes);
  }
}
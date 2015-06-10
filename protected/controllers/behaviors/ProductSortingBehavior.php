<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 * Пример подключения:
 * 'productSortingBehavior' => array(
 *    'class' => 'ProductSortingBehavior',
 *    'defaultSorting' => 'default',
 *    'pageSizeRange' => array(20, 40, 60)
 * )
 */

/**
 * Class ProductSortingBehavior
 * @property ProductController $owner
 * @property string $sorting
 * @property string $pageSizeRange
 */
class ProductSortingBehavior extends SBehavior
{
  public $defaultSorting = 'default';

  public $pageSizeRange;

  public function init()
  {
    $params = Yii::app()->session->get($this->owner->id);
    $this->owner->pageSize = Arr::get($params, 'pageSize', $this->owner->getSettings('product_page_size', $this->owner->pageSize));
    $this->pageSizeRange = Arr::reflect($this->pageSizeRange);

    $this->setSorting();
  }

  /**
   * @return array|string
   */
  public function getSorting()
  {
    $params = Yii::app()->session->get($this->owner->id);
    return Arr::get($params, 'sorting', $this->defaultSorting);
  }

  protected function setSorting()
  {
    if( Yii::app()->request->isPostRequest && isset($_POST['setSorting']) )
    {
      $sessionParams = Yii::app()->session[$this->owner->id];

      $sorting = Yii::app()->request->getPost('sorting');
      $sessionParams['sorting'] = !empty($sorting) ? $sorting : null;

      $this->owner->pageSize = Yii::app()->request->getPost('pageSize', $this->owner->pageSize);
      $sessionParams['pageSize'] = $this->owner->pageSize;

      Yii::app()->session[$this->owner->id] = $sessionParams;
    }
  }
} 
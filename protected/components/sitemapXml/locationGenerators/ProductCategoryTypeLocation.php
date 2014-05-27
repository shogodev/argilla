<?php
/**
 * @author Nikita Pimoshenko <pimoshenko@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * An example of complicated location for sitemapXml
 */
class ProductCategoryTypeLocation extends LocationBase
{
  /**
   * @param CController $controller
   */
  function __construct(CController $controller)
  {
    parent::__construct($controller);
    /*$ProductAssignment=ProductAssignment::model()->getGreeningAssignments();
    $ProductAssignment = $this->unique(array('category_url', 'type_url'), $ProductAssignment);
    $this->_modelSource = new CDataProviderIterator(new CArrayDataProvider($ProductAssignment));*/
  }

  /**
   * @return string
   */
  public function current()
  {
    /*$current = $this->_modelSource->current();

    return $this->_controller->createAbsoluteUrl($this->getRoute(),  array('category' => $current['category_url'],'type' => $current['type_url']));*/
  }

  /**
   * @return string
   */
  public function getRoute()
  {
    /*return 'product/categoryType';*/
  }
}
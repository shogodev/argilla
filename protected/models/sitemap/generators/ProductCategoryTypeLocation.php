<?php
/**
 * @author Nikita Pimoshenko <pimoshenko@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap
 *
 * An example of complicated location for sitemapXml
 */
class ProductCategoryTypeLocation extends LocationBase
{
  public function __construct(CController $controller)
  {
    parent::__construct($controller);

    /*
    $assignments = ProductAssignment::model()->getAssignments();
    $assignments = $this->unique(array('category_url', 'type_url'), $assignments);
    $this->_modelSource = new CDataProviderIterator(new CArrayDataProvider($ProductAssignment));
    */
  }

  public function current()
  {
    $current = $this->_modelSource->current();

    /*
    return $this->_controller->createAbsoluteUrl($this->getRoute(), array('category' => $current['category_url'], 'type' => $current['type_url']));
    */
  }

  public function getRoute()
  {
    return 'product/categoryType';
  }
}


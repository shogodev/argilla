<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap.generators
 */
class NewsSectionLocation extends LocationBase
{
  /**
   * @param CController $controller
   */
  function __construct(CController $controller)
  {
    parent::__construct($controller);

    $this->_modelSource = new CDataProviderIterator(new CActiveDataProvider('NewsSection'));
  }

  /**
   * @return string
   */
  public function current()
  {
    /** @var $current NewsSection */
    $current = $this->_modelSource->current();

    return $this->_controller->createAbsoluteUrl($this->getRoute(), array('url' => $current->url));
  }

  /**
   * @return string
   */
  public function getRoute()
  {
    return 'news/section';
  }
}
<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap.generators
 */
class IndexLocation extends LocationBase
{
  /**
   * @param CController $controller
   */
  public function __construct(CController $controller)
  {
    parent::__construct($controller);

    $this->_modelSource = new ArrayIterator(array('/')); // Заглушка, чтобы не сыпались fatal errors.
  }

  /**
   * @return string
   */
  public function current()
  {
    return $this->_controller->createAbsoluteUrl($this->getRoute());
  }

  /**
   * @return string
   */
  public function getRoute()
  {
    return 'index/index';
  }
}
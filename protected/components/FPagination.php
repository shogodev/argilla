<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class FPagination extends CPagination
{
  public $defaultPage = 1;

  private $_currentPage;

  /**
   * @param bool $recalculate
   *
   * @return int
   * @throws CHttpException
   */
  public function getCurrentPage($recalculate = true)
  {
    if( $this->_currentPage === null || $recalculate )
    {
      if( isset($_GET[$this->pageVar]) )
      {
        $this->_currentPage = (int)$_GET[$this->pageVar];
        $this->checkDefaultPagination($this->_currentPage);
        $this->_currentPage = $this->_currentPage - 1;

        if( $this->validateCurrentPage )
        {
          $pageCount = $this->getPageCount();
          if( $this->_currentPage >= $pageCount )
          {
            throw new CHttpException(404, 'Страница не найдена');
          }
        }
        if( $this->_currentPage < 0 )
        {
          throw new CHttpException(404, 'Страница не найдена');
        }
      }
      else
        $this->_currentPage = 0;
    }
    return $this->_currentPage;
  }

  /**
   * @param integer $value
   */
  public function setCurrentPage($value)
  {
    $this->_currentPage   = $value;
    $_GET[$this->pageVar] = $value + 1;
  }

  /**
   * @param $currentPage
   */
  public function checkDefaultPagination($currentPage)
  {
    if( $currentPage == $this->defaultPage )
    {
      $get   = $_GET;
      $route = Yii::app()->controller->route;

      if( isset($get[$this->pageVar]) && $get[$this->pageVar] == $currentPage )
      {
        unset($get[$this->pageVar]);

        $url      = Yii::app()->request->getPathInfo();
        $redirect = Yii::app()->controller->createUrl($route, $get);

        if( trim($url, '/') !== trim(parse_url($redirect)['path'], '/') )
          Yii::app()->controller->redirect($redirect, 301);
      }
    }
  }
}
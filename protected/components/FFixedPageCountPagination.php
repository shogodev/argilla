<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class FFixedPageCountPagination Паджинация с фиксированным количестом страниц.
 */
class FFixedPageCountPagination extends FPagination
{
  /**
   * @var integer Количество страниц.
   */
  private $_pageCount = 1;

  /**
   * Инициализирует паджинацию для указанного количества страниц.
   *
   * @param integer $pageCount Количество страниц.
   */
  public function __construct($pageCount = 1)
  {
    // Для данного вида паджинации количество элементов передаваемое
    // в конструктор не играет ни какой роли и может быть произвольным.
    $this->pageCount = $pageCount;
    parent::__construct(0);
  }

  /**
   * @return integer Возвращает количество страниц.
   */
  public function getPageCount()
  {
    return $this->_pageCount;
  }

  /**
   * @param integer $pageCount Устанавливает количество страниц.
   */
  public function setPageCount($pageCount)
  {
    if( $pageCount <= 0 )
    {
      $pageCount = 1;
    }

    $this->_pageCount = $pageCount;
  }
}
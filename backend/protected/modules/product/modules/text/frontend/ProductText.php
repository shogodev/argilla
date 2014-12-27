<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license  http://argilla.ru/LICENSE
 *
 * @method static ProductText model(string $className = __CLASS__)
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $content
 * @property string $content_upper
 * @property string $img
 * @property bool $visible
 */
class ProductText extends FActiveRecord
{
  /**
   * @return array
   */
  public function defaultScope()
  {
    $alias = $this->getTableAlias(true, false);

    return array(
      'condition' => "{$alias}.visible = '1'"
    );
  }

  /**
   * @param string $url
   *
   * @return self
   */
  public function whereUrl($url)
  {
    $this->dbCriteria->mergeWith(array(
      'condition' => 'url = :url',
      'params' => array(
        ':url' => '/'.trim($url, " /").'/',
      ),
    ));

    return $this;
  }
}
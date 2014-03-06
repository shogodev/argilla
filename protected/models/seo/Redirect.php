<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property integer $id
 * @property string $base
 * @property string $target
 * @property string $type_id
 * @property string $visible
 */
class Redirect extends FActiveRecord
{
  public function tableName()
  {
    return '{{seo_redirect}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }

  /**
   * @param string $url
   *
   * @return bool
   */
  public function hasRegExpCoincidence($url)
  {
    return stripos($this->base, RedirectHelper::REGEXP_START_CHAR) === 0 && @preg_match($this->base, $url);
  }

  /**
   * @param string $url
   *
   * @return bool
   */
  public function hasStringCoincidence($url)
  {
    return $url === $this->base;
  }
}
<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.seo
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
}
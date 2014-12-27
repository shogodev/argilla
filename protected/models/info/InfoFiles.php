<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.info
 *
 * @property integer $id
 * @property integer $parent
 * @property string $name
 * @property string $size
 * @property string $type
 * @property string $notice
 * @property integer $position
 */
class InfoFiles extends FActiveImage
{
  protected $imageDir = 'f/info/';

  protected $availableTypes = array('pre');

  public function tableName()
  {
    return '{{info_files}}';
  }
}
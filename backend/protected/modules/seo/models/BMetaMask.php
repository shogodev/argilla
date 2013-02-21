<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BMetaMask model(string $class = __CLASS__)
 *
 * @property string $url_mask
 */
class BMetaMask extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('url_mask, title', 'required'),
      array('url_mask', 'unique'),
      array('url_mask, title, description, keywords', 'length', 'max' => 255),
      array('url_mask, title, description, keywords, visible' , 'safe'),
    );
  }

  public function beforeSave()
  {
    $this->url_mask = $this->cutDomain($this->url_mask);
    return parent::beforeSave();
  }

  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('url_mask', $this->url_mask, true);
    $criteria->compare('visible', $this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'url_mask' => 'Маска',
      'title' => 'Title страницы',
    ));
  }


  protected function cutDomain($url)
  {
    $parse_url = parse_url($url);
    $path      = !empty($parse_url['path']) ? $parse_url['path'] : $url;
    $path      = !preg_match('/^\/.*/', $path) ? '/'.$path : $path;

    if( strlen($path) > 1 && substr($path, -1) == '/' )
      $path = substr($path, 0, strlen($path) - 1);

    return $path;
  }
}
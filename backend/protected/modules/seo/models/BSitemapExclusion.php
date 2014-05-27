<?php
/**
 * @method static BSitemapRoute model(string $className = __CLASS__)
 *
 * @property int    $id
 * @property string $route
 * @property bool   $lastmod
 * @property string $changefreq
 * @property float  $priority
 * @property bool   $visible
 *
 * @property array  $changeFreqs
 */
class BSitemapExclusion extends BActiveRecord
{
  /**
   * Function used to replace standard tableName when module connects to DB table
   * By default module connects to table that is named like module class
   * Using this function you can make module connect to table that has name that differs from module class name
   * Usually returns alias string
   *
   * @return string
   */
  public function tableName()
  {
    return '{{seo_sitemap_exclusion}}';
  }

  /**
   * Validation rules
   *
   * @return array
   */
  public function rules()
  {
    return array(
      array('route', 'filter', 'filter' => array('Utils', 'parseRelativeUrl')),
      array('route', 'required'),
      array('lastmod, visible', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 1),
      array('route, changefreq', 'length', 'max' => 255),
      array('route', 'unique'),
      array('priority', 'length', 'max' => 5),
      array('id, route, lastmod, changefreq, priority, visible', 'safe', 'on' => 'search'),
    );
  }

  /**
   * Returns attribute Labels for form widgets
   *
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'route' => 'URL',
      'lastmod' => 'lastmod',
      'changefreq' => 'Частота изменения',
      'priority' => 'Приоритет',
    ));
  }

  /**
   * Returns search criteria used for rows filtration in widget
   *
   * @return CdbCriteria
   */
  public function getSearchCriteria(CdbCriteria $criteria)
  {
    $criteria->compare('id', $this->id);
    $criteria->compare('route', $this->route, true);
    $criteria->compare('lastmod', $this->lastmod);
    $criteria->compare('changefreq', $this->changefreq, true);
    $criteria->compare('priority', $this->priority, true);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }

  /**
   * Returns array of all available pages' change frequencies
   *
   * @return array
   */
  public function getChangeFreqs()
  {
    return array(
      'always' => 'Постоянно',
      'hourly' => 'Каждый час',
      'daily' => 'Каждый день',
      'weekly' => 'Каждую неделю',
      'monthly' => 'Каждый месяц',
      'yearly' => 'Каждый год',
      'never' => 'Никогда',
    );
  }

}
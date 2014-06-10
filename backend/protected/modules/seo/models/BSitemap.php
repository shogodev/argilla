<?php
/**
 * @author Nikita Pimoshenko <pimoshenko@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo.models
 *
 * @method static BSitemap model(string $className=__CLASS__)
 */
class BSitemap extends BActiveRecord
{
  public function tableName()
  {
    return '{{seo_sitemap_route}}';
  }

  /**
   * Returns attribute Labels for form widgets
   *
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'route' => 'Маршрут',
      'changefreq' => 'Частота изменения',
      'priority' => 'Приоритет',
    ));
  }

  /**
   * Returns filtered criteria
   *
   * @param CdbCriteria $criteria
   *
   * @return CDbCriteria
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
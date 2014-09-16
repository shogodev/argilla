<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share.formatters
 */
class DateAgoFormatter
{
  /**
   * array(
   *  type => array('именительный', 'родительный', 'множественное число родительный'), #один, два, много
   * ),
   *
   * @var array
   */
  private $forms = array(
    'year' => array(
      'год', 'года', 'лет'
    ),

    'month' => array(
      'месяц', 'месяца', 'месяцев',
    ),

    'day' => array(
      'день', 'дня', 'дней',
    ),

    'hour' => array(
      'час', 'часа', 'часов',
    ),

    'minute' => array(
      'минуту', 'минуты', 'минут'
    ),
  );

  /**
   * @var DateTime
   */
  private $now;

  /**
   * @var DateTime
   */
  private $date;

  /**
   * @param string $date
   */
  public function __construct($date)
  {
    $this->now  = new DateTime('now');
    $this->date = new DateTime(date('Y-m-d H:i:s', strtotime($date)));
  }

  /**
   * @return string
   */
  public function ago()
  {
    $interval = $this->now->diff($this->date);

    $type = null;
    $count = 0;

    if( $interval->y > 0 )
    {
      $type = 'year';
      $count = $interval->y;
    }
    elseif( $interval->m > 0 )
    {
      $type = 'month';
      $count = $interval->m;
    }
    elseif( $interval->d > 0 )
    {
      $type = 'day';
      $count = $interval->d;
    }
    elseif( $interval->h > 0 )
    {
      $type = 'hour';
      $count = $interval->h;
    }
    elseif( $interval->i > 0 )
    {
      $type = 'minute';
      $count = $interval->i;
    }

    return $this->pluralize($count, $type);
  }

  /**
   * @param integer $count
   * @param string $type
   *
   * @return string
   */
  private function pluralize($count, $type)
  {
    if( in_array($type, array_keys($this->forms)) )
    {
      $form = Yii::t('app', implode("|", $this->forms[$type]), $count);
      return $count . ' ' . $form . ' назад';
    }
    else
    {
      return 'только что';
    }
  }
}
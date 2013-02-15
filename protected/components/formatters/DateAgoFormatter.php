<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.formatters
 */
class DateAgoFormatter
{
  /**
   * array(
   *  type => array(
   *   'именительный', 'родительный', 'множественное число родительный'
   *  ),
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
      $forms = $this->forms[$type];

      /**
       * @link https://gist.github.com/2382775
       */
      $form = $count % 10 == 1 && $count % 100 != 11 ? $forms[0] : ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) ? $forms[1] : $forms[2]);

      return $count . ' ' . $form . ' назад';
    }
    else
      return 'только что';
  }
}
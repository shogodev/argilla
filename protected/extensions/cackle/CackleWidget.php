<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 *
 * @link http://ru.cackle.me/help/widget-api - документация
 * @property null|string|CActiveRecord $channel
 */
class CackleWidget extends CWidget
{
  const COMMENT = 'Comment';

  const REVIEW = 'Review';

  public $siteId;

  public $widget;

  public $container;

  public $channel;

  private $settings;

  private static $innerCounter = 1;

  public function init()
  {
    $this->registerCommonScript();

    $this->settings = array(
      'id' => $this->siteId,
      'widget' => $this->widget,
      'container' => $this->getContainer(),
    );

    if( $channel = $this->getChannel() )
      $this->settings['channel'] = $channel;
  }

  public function run()
  {
    echo CHtml::tag('div', array('id' => $this->getContainer()), $this->getContent());

    Yii::app()->clientScript->registerScript('CackleScript#'.$this->getContainer(), "cackle_widget.push(".CJSON::encode($this->settings).");", CClientScript::POS_BEGIN);
  }

  protected function getChannel()
  {
    if( is_string($this->channel) )
      return $this->channel;

    if( $this->channel instanceof CActiveRecord )
      return Utils::toSnakeCase(get_class($this->channel)).'-'.$this->channel->primaryKey;

    return null;
  }

  protected function getContainer()
  {
    if( $this->container == null )
    {
      $this->container = 'mc-'.strtolower($this->widget).'-'.self::$innerCounter++ ;
    }
    return $this->container;
  }

  protected function registerCommonScript()
  {
    Yii::app()->clientScript->registerScript('CackleCommonBeginScript', "cackle_widget = window.cackle_widget || [];", CClientScript::POS_BEGIN);

    Yii::app()->clientScript->registerScript('CackleCommonEndScript', "
    (function() {
      var mc = document.createElement('script');
      mc.type = 'text/javascript';
      mc.async = true;
      mc.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cackle.me/widget.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(mc, s.nextSibling);
    })();", CClientScript::POS_READY);

  }

  protected function getContent()
  {
    if( $this->widget == self::COMMENT )
    {
      return $this->getComments();
    }
    else if( $this->widget == self::REVIEW )
    {
      return $this->getReviews();
    }

    return '';
  }

  protected function getReviews()
  {
    /**
     * @var CackleReview[] $reviews
     */
    if($reviews = CackleReview::model()->getReviewsByChannel($this->getChannel()) )
    {
      $items = array();
      foreach($reviews as $review)
      {
        $items[] = strtr($this->getTemplate('review'), array(
          '{id}' => $review->id,
          '{comment}' => $review->comment,
          '{dignity}' => $review->dignity,
          '{lack}' => $review->lack,
          '{author}' => $this->getAuthor($review),
          '{dateRaw}' => $review->date,
          '{date}' => $this->getDate($review)
        ));
      }

      return strtr($this->getTemplate('reviews'), array('{items}' => implode('', $items)));
    }

    return '';
  }

  protected function getComments()
  {
    /**
     * @var CackleComment[] $comments
     */
    if( $comments = CackleComment::model()->getCommentByChannel($this->getChannel()) )
    {
      $items = array();
      foreach($comments as $comment)
      {
        $items[] = strtr($this->getTemplate('comment'), array(
          '{id}' => $comment->id,
          '{comment}' => $comment->comment,
          '{name}' => $comment->author,
          '{avatar}' => !empty($comment->avatar) ? strtr($this->getTemplate('comment_avatar'), array('{image}' => $comment->avatar)) : '',
          //'{dateRaw}' => $comment->date,
          '{date}' => $this->getDate($comment),
          '{rating}' => $comment->rating
        ));
      }

      return strtr($this->getTemplate('comments'), array(
        '{items}' => implode('', $items),
        '{comments_amount}' => count($comments).' '.Utils::plural(count($comments), 'комментарий,комментария,комментариев')
      ));
    }

    return '';
  }

  /**
   * @param CackleReview|CackleComment $data
   *
   * @return string
   */
  protected function getAuthor($data)
  {
    return strtr($this->getTemplate('review_author'), array(
      '{name}' => $data->author,
      '{avatar}' => !empty($data->avatar) ? strtr($this->getTemplate('review_avatar'), array('{image}' => $data->avatar)) : ''
    ));
  }

  /**
   * @param CackleReview|CackleComment $data
   *
   * @return string
   */
  protected function getDate($data)
  {
    if( $data->date == '0000-00-00 00:00:00' )
      return '';

    $normalDate = DateTime::createFromFormat('Y-m-d H:i:s', $data->date)->format('d.m.Y');

    return strtr($this->getTemplate('date'), array('{normalDate}' => $normalDate, '{dateRaw}' => $data->date));
  }

  /**
   * @param $name
   * @return string
   */
  protected function getTemplate($name)
  {
    return Yii::app()->controller->renderPartial('ext.cackle.views.'.$name, null, true);
  }
}
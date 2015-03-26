<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package
 */
Yii::import('frontend.components.ar.*');
Yii::import('frontend.share.helpers.*');
Yii::import('frontend.share.*');
Yii::import('ext.cackle.*');
Yii::import('ext.cackle.models.*');
Yii::import('ext.cackle.components.*');

class CackleImportCommand extends CConsoleCommand
{
  /**
   * @var CackleApi $api
   */
  private $api;

  public function actionIndex()
  {
    $this->api = new CackleApi();
    $this->sendComments();
  }

  /**
   * @return array
   */
  public function getCommentsModels()
  {
    /**
     * @var CackleComment[] $data
     */
    $data = CackleReview::model()->findAll();
    $commentModels = array();

    foreach($data as $item)
    {
      $model = new CackleComment();
      $model->id = $item->id;
      $model->url = $item->channel;
      $model->channel = $item->channel;
      $model->author = $item->author;
      $model->date = $item->date;
      $model->status = $item->status == 'approved' || $item->status == 1 ? 1 : 0;
      $model->comment = $item->comment;

      $commentModels[] = $model;
     }

    return $commentModels;
  }

  public function sendComments()
  {
    if( $data = $this->getCommentsModels() )
    {
      foreach($data as $item)
        $this->sendComment($item, end($data) == $item);

      echo 'Отправка данных завершена'.PHP_EOL;
    }
  }

  public function sendComment(CackleComment $model, $last)
  {
    echo 'Обработка записи '.$model->id.PHP_EOL;
    echo 'Результат '.$this->api->sendComment($this->createCommentXml($model), $last).PHP_EOL;
  }

  private function createCommentXml(CackleComment $model)
  {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<rss version="2.0"';
    $xml .= ' xmlns:excerpt="http://wordpress.org/export/1.0/excerpt/"';
    $xml .= ' xmlns:content="http://purl.org/rss/1.0/modules/content/"';
    $xml .= ' xmlns:cackle="http://cackle.me/"';
    $xml .= ' xmlns:wfw="http://wellformedweb.org/CommentAPI/"';
    $xml .= ' xmlns:dc="http://purl.org/dc/elements/1.1/"';
    $xml .= ' xmlns:wp="http://wordpress.org/export/1.0/">';

    $xml .= '<channel>';
    $xml .= '<item>';
    $xml .= $this->createCommentXmlItem($model);
    $xml .= '</item>';
    $xml .= '</channel>';
    $xml .= '</rss>';

    return $xml;
  }

  private function createCommentXmlItem(CackleComment $model)
  {
    $xml = '<title>import comment</title>';
    $xml .= '<link>'.$model->url.'</link>';
    $xml .= '<wp:post_id>'.$model->channel.'</wp:post_id>';
    $xml .= '<wp:comment>';

    $xml .= '<wp:comment_id>'.$model->id.'</wp:comment_id>';
    $xml .= '<wp:comment_author>'.str_replace("<br />","\r\n", CHtml::encode($model->author)).'</wp:comment_author>';
    $xml .= '<wp:comment_author_email>'.$model->email.'</wp:comment_author_email>';
    $xml .= '<wp:comment_author_IP>'.$model->ip.'</wp:comment_author_IP>';
    $xml .= '<wp:comment_date>'.$model->date.'</wp:comment_date>';
    $xml .= '<wp:comment_date_gmt>'.$model->date.'</wp:comment_date_gmt>';
    $xml .= '<wp:comment_content>'.CHtml::encode(strip_tags(str_replace("<br />","\r\n", $model->comment))).'</wp:comment_content>';
    $xml .= '<wp:comment_approved>'.$model->status.'</wp:comment_approved>';
    $xml .= '<wp:comment_type></wp:comment_type>';
    $xml .= '<wp:comment_parent>0</wp:comment_parent>';
    $xml .= '</wp:comment>';

    return $xml;
  }
}
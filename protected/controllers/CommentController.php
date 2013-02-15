<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.CommentController
 */
class CommentController extends FController
{
  /**
   * @param string $model
   * @param string $item
   */
  public function actionAdd($model, $item)
  {
    $comment = new Comment();
    $comment->user_id = Yii::app()->user->id;

    $form = CommentForm::build(Comment::$models[$model], $item, $comment);
    $form->ajaxValidation($comment);

    if( $form->save() )
    {
      Yii::app()->notification->send($comment);
      $form->responseSuccess('Сообщение отправлено. Комментарий будет опубликован после проверки.');
    }
  }

  /**
   * Рендер всех комментариев для заданной модели
   *
   * @param CommentForm $form
   */
  public function renderComments(CommentForm $form)
  {
    /**
     * @var Comment $model
     */
    $model       = $form->getModel();
    $targetModel = $model->model;

    $comments = Comment::model()->target($targetModel, $model->item)->findAll();

    $this->renderPartial($form->getView(), array(
      'comments' => $comments,
      'form'     => $form,
    ));
  }
}
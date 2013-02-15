<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.CommentForm
 */
class CommentForm extends FForm
{
  /**
   * @var string
   */
  private $view = '//comments';

  /**
   * @var string
   */
  private $formClass = 'form medium-inputs float-none';

  /**
   * Для верной работы модели необходимо заполнить массив Comment::$models,
   * где ключом будет хэш модели, а значением - её название
   *
   * @param string  $model
   * @param integer $item
   * @param Comment $comment
   *
   * @return CommentForm
   */
  public static function build($model, $item, Comment $comment = null)
  {
    if( $comment === null )
      $comment = new Comment();

    $comment->model = $model;
    $comment->item  = $item;

    $form         = new self('CommentForm', $comment);
    $form->action = Yii::app()->controller->createUrl('comment/add', array('model' => Comment::getModelHash($model), 'item' => $item));

    return $form;
  }

  /**
   * @return string
   */
  public function getView()
  {
    return $this->view;
  }

  /**
   * @param string $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }

  /**
   * @return string
   */
  public function getFormClass()
  {
    return $this->formClass;
  }

  public function show()
  {
    $commentController = new BCommentController('commentController');
    $commentController->renderComments($this);
  }
}
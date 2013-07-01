<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class LinkController extends FController
{
  public function actionIndex()
  {
    $sections = LinkSection::model()->findAll();

    $form = new FForm('LinkForm', new Link());
    $form->loadFromSession = true;
    $form->clearAfterSubmit = true;

    $form->ajaxValidation();

    if( Yii::app()->request->isAjaxRequest && $form->save() )
    {
      $form->responseSuccess(CHtml::tag('div', array('class' => 'center bb'), 'Ваша ссылка успешно отправлена.'));
    }
    else
    {
      $this->render('index', [
        'sections' => $sections,
        'form' => $form,
      ]);
    }
  }

  /**
   * @param string $section
   * @param int $page
   *
   * @throws CHttpException
   */
  public function actionSection($section, $page)
  {
    /** @var $model LinkSection */
    $model = LinkSection::model()->whereUrl($section)->find();

    if( $model === null )
    {
      throw new CHttpException(404);
    }

    $this->breadcrumbs = [
      'Каталог ссылок' => $this->createUrl('link/index'),
      $model->name,
    ];

    $pages = new FFixedPageCountPagination($model->pageCount);

    /** @var $links Link[] */
    $links = $model->getLinksOnPage($page);
    $sections = LinkSection::model()->findAll();

    $this->render('section', [
      'model' => $model,
      'sections' => $sections,
      'dataProvider' => new FArrayDataProvider($links),
      'pages' => $pages,
    ]);
  }
}
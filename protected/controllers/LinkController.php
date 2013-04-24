<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class LinkController extends FController
{
	public function actionIndex()
	{
		return $this->render('index', array(
			'dataProvider' => new FActiveDataProvider('LinkSection')
		));
	}

	public function actionSection($url)
	{
		/**@var LinkSection $section*/
		$section = LinkSection::model()->findByAttributes(array('url' => $url));

		if( $section === null )
			throw new CHttpException(404);

		$this->breadcrumbs = array(
			'Каталог ссылок' => $this->createUrl('link/index'),
			$section->name,
		);

		$criteria = new CDbCriteria();
		$criteria->compare('section_id', $section->id);

		$this->render('section', array(
				'dataProvider' => new FActiveDataProvider('Link', array(
					'criteria' => $criteria,
				)),
			));
	}

	public function actionOne($id)
	{
		/**@var Link $link*/
		$link = Link::model()->findByPk($id);

		if( $link === null )
			throw new CHttpException(404);

		$this->breadcrumbs = array(
			'Каталог ссылок' => $this->createUrl('link/index'),
			$link->section->name => $this->createUrl('link/section', array('url' => $link->section->url)),
			$link->title,
		);

		$this->render('link', array(
			'link' => $link,
		));
	}

	public function actionAdd()
	{
		$form = new FForm('LinkForm', new Link());

		$this->render('add', array(
				'form' => $form,
		));
	}
}
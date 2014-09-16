<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * @var LinkController $this
 * @var Link $link
 */
echo CHtml::openTag('li');
echo CHtml::openTag('div', ['class' => 'm10']);
echo CHtml::link($link->title, $link->url);
echo $link->content;
echo CHtml::closeTag('div');
echo CHtml::closeTag('li');
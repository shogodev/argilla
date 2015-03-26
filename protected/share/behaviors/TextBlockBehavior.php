<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package @package frontend.share.behaviors
 */
class TextBlockBehavior extends SBehavior
{
  /**
   * @var TextBlock[] $textBlocks
   */
  protected $textBlocks;

  public function init()
  {
    Yii::import('frontend.components.ar.FActiveRecord');
    Yii::import('frontend.components.image.FSingleImage');
    Yii::import('frontend.components.image.ImageInterface');
    Yii::import('frontend.models.TextBlock');
  }

  /**
   * @param $location
   * @param array $replace
   *
   * @return TextBlock
   */
  public function textBlock($location, $replace = array())
  {
    return Arr::reduce($this->textBlocks($location, $replace));
  }

  /**
   * @param $location
   * @param array $replace
   *
   * @return TextBlock[]
   */
  public function textBlocks($location, $replace = array())
  {
    if( !isset($this->textBlocks) )
      $this->textBlocks = TextBlock::model()->getGroupByLocation();

    return !isset($this->textBlocks[$location]) ? array() : $this->executeReplace($this->textBlocks[$location], $replace);
  }

  /**
   * Если текстовый блок не существует, то он создается
   * @param string|null $name - название
   * @param string|null $content - текст по умолчанию
   * @param array('class' => 'success-message') $htmlOptions
   * @param array $replace
   *
   * @return string
   */
  public function textBlockRegister($name = null, $content = null, $htmlOptions = array('class' => 'success-message'), array $replace = array())
  {
    if( $name === null )
      $location = $this->owner->route;
    else
      $location = Utils::translite($name);

    if( !$this->textBlock($location) )
    {
      if( TextBlock::model()->resetScope()->getByLocation($location) )
        return '';

      $textBlock = new TextBlock();
      $textBlock->attributes = array(
        'location' => $location,
        'name' => empty($name) ? $this->owner->route : $name.' ('.$this->owner->route.')',
        'visible' => 1,
        'auto_created' => 1
      );

      if( $content !== null )
        $textBlock->setAttribute('content', $htmlOptions != null ? CHtml::tag('div', $htmlOptions, $content) : $content);

      $textBlock->save();

      if( $content === null )
      {
        $textBlock->content = '<div style="background-color: red; padding: 10px; color: white; font-size: 12px; font-weight: bold">';
        $textBlock->content .= 'Данный текстовый блок сгенерирован автоматически в '.$this->owner->route.'.';
        $textBlock->content .= 'Текст нужно заменить в <a href="/backend/textblock/bTextBlock/update/'.$textBlock->id.'">backend</a>';
        $textBlock->content .= '</div>';
        $textBlock->save();
      }

      $this->textBlocks[$location] = array($textBlock->content);
    }

    return $this->textBlock($location, $replace);
  }

  protected function executeReplace($data, $replace)
  {
    foreach($data as $key => $text)
    {
      if( preg_match_all('/{{([^{}\s]*)}}|{([^{}\s]*)}/', $text, $matches) )
      {
        $autoReplace = array();
        foreach(Arr::reset($matches) as $matchIndex => $expression)
        {
          $autoReplace[$expression] = !empty($matches[1][$matchIndex]) ? trim(strip_tags($this->textBlock($matches[1][$matchIndex]))) : $this->textBlock($matches[2][$matchIndex]);
        }

        $replace = array_merge($autoReplace, $replace);
      }

      if( $replace )
        $data[$key] = strtr($text, $replace);
    }

    return $data;
  }
}
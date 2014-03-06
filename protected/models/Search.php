<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 */
class Search extends CComponent
{
  public $query;

  public $page = 0;

  public $pageSize = 0;

  public $itemsCount = 0;

  public $project;

  /**
   * @var FActiveDataProvider
   */
  public $searchResult;

  protected $searchContent;

  protected $serviceUrl = 'http://yasearch.seonet.ru:17000/';

  public function __construct()
  {
    $this->initQuery();

    $this->page    = Yii::app()->request->getQuery('page', $this->page);
    $this->page    = $this->page < 1 ? 1 : $this->page;
    $this->project = empty($this->project) ? $this->project = Yii::app()->params['project'] : $this->project;

    $this->getContent();
    $this->parseResults();
  }

  protected function initQuery()
  {
    $query = Yii::app()->request->getQuery('search');
    $query = CHtml::encode(trim($query));

    if( !empty($query) )
      Yii::app()->session['search'] = $query;

    $this->query = Yii::app()->session['search'];
  }

  protected function getContent()
  {
    $parameters = array(
      'text' => $this->query,
      'xml' => 'yes',
      'p' => $this->page - 1,
    );

    $context = stream_context_create(array(
      'http' => array(
        'timeout' => 5,
      )
    ));

    $this->searchContent = @file_get_contents($this->serviceUrl.$this->project."?".http_build_query($parameters), 0, $context);
  }

  protected function convert($string)
  {
    return $string;
  }

  protected function parseResults()
  {
    $xml = new DOMDocument();
    $xml->preserveWhiteSpace = false;
    @$xml->loadXML($this->searchContent);
    $searchResult = array();
    $i = 0;

    foreach($xml->childNodes as $root)
    {
      foreach($root->childNodes as $lvl1)
      {
        if( $lvl1->nodeName == 'request' )
          foreach($lvl1->childNodes as $lvl8)
          {
            if( $lvl8->nodeName != 'page' )
              continue;

            $this->page = $lvl8->nodeValue;
          }

        if( $lvl1->nodeName == 'response' )
        {
          foreach($lvl1->childNodes as $lvl2)
          {
            if( $lvl2->nodeName != 'results' )
              continue;

            foreach($lvl2->childNodes as $lvl3)
            {
              if( $lvl3->nodeName == 'grouping' )
                $this->pageSize = $lvl3->GetAttribute('groups-on-page');

              foreach($lvl3->childNodes as $lvl4)
              {
                if( $lvl4->nodeName == 'found' )
                  $this->itemsCount = $lvl4->nodeValue;

                if( $lvl4->nodeName == 'group' )
                {
                  foreach($lvl4->childNodes as $lvl5)
                  {
                    if( $lvl5->nodeName != 'doc' )
                      continue;

                    $searchResult[$i]['id'] = $i;

                    foreach($lvl5->childNodes as $lvl6)
                    {
                      if( $lvl6->nodeName == 'url' )
                      {
                        $searchResult[$i]['url'] = $lvl6->nodeValue;
                        $searchResult[$i]['url'] = str_replace(array("webds://", "webds/"), "http://", $searchResult[$i]['url']);
                      }
                      if( $lvl6->nodeName == 'title' )
                      {
                        $searchResult[$i]['title'] = $lvl6->nodeValue;
                        $searchResult[$i]['title'] = $this->convert($searchResult[$i]['title']);

                        foreach($lvl6->childNodes as $lvl12)
                        {
                          if( $lvl12->nodeName == 'hlword' )
                          {
                            $val1 = $this->convert($lvl12->nodeValue);
                            if( !empty($val1) )
                              $s1[] = $val1;
                          }
                        }

                        if( !empty($s1) )
                        {
                          foreach($s1 as $value1)
                          {
                            if( strlen($value1) > 1 )
                              $searchResult[$i]['title'] = str_replace($value1, " $value1 ", $searchResult[$i]['title']);
                          }
                        }
                      }

                      if( $lvl6->nodeName == 'passages' )
                      {
                        foreach($lvl6->childNodes as $lvl7)
                        {
                          $searchResult[$i]['content'] = $lvl7->nodeValue;
                          $searchResult[$i]['content'] = $this->convert($searchResult[$i]['content']);

                          $s = array();
                          foreach($lvl7->childNodes as $lvl10)
                          {
                            if( $lvl10->nodeName == 'hlword' )
                            {
                              $val = $this->convert($lvl10->nodeValue);
                              if( !empty($val) )
                                if( strlen($val) > 1 )
                                  $s[] = $val;
                            }
                          }

                          usort($s, function($a, $b){return !strcmp($a, $b) ? 0 : strcmp($b, $a);});
                        }
                      }

                      if( !empty($s) && !empty($searchResult[$i]['content']) )
                        foreach($s as $value)
                          $searchResult[$i]['content'] = str_replace($value, "<b class=\"red\">$value</b>", $searchResult[$i]['content']);
                      else
                        $searchResult[$i]['content'] = "";
                    }

                    $i++;
                  }
                }
              }
            }
          }
        }
      }
    }

    $this->searchResult = new FArrayDataProvider($searchResult, array(
      'pagination' => false,
    ));
  }
}
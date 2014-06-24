<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands.api
 */
Yii::import('frontend.commands.api.ApiModel');

class checkPhpDoc extends ApiModel
{
  private $_currentClass;

  public function checkProperties($sourceFiles)
  {
    echo "Checking PHPDoc @property ...\n";
    $this->build($sourceFiles);

    /**
     * todo: Вытащить свойства из behaviors и из родительских классов
     * todo: Если родитель CComponent и если есть методы с get и set то фиксировать свойство
     */

    /**
     * @var ClassDoc $class
     */
    foreach($this->classes  as $className => $class)
    {
      $phpDocProperties = $this->getPhpDocsProperties($class->phpDoc, $class);
      $reallyProperties = array();

      foreach($class->properties as $propertyName => $property)
      {
        /**
         * @var PropertyDoc $property
         */
        if( $property->definedBy == $className && !$property->readOnly && !$property->isProtected )
          $reallyProperties[$propertyName] = $property->type;
      }

      //&& !in_array($className, array('FActiveFileRecord', 'FActiveImage', 'FActiveRecord'))
      if( in_array('CActiveRecord', $class->parentClasses) && !$class->isAbstract )
      {
        /**
         * @var CActiveRecord $model
         */
        $model = $className::model();

        foreach($model->metaData->columns as $column)
          $reallyProperties[$column->name] = strpos($column->dbType, 'int') !== false ? 'integer' : $column->type;
      }

      if( !empty($phpDocProperties) && !empty($reallyProperties) )
      {
        ksort($phpDocProperties);
        ksort($reallyProperties);

/*        // Лишние
        $diff = array_diff(array_keys($phpDocProperties), array_keys($reallyProperties));
        if( !empty($diff)  )
        {
          echo "ERROR.............: Excess properties in PhpDoc!\n";
          echo "Source file.......: ".$class->sourcePath."\n";
          echo "Class.............: ".$className."\n";
          echo "Properties:.......: ".implode(', ', $diff)."\n\n";
        }*/

        // Не хватает
        $diff = array_diff(array_keys($reallyProperties), array_keys($phpDocProperties));
        if( !empty($diff)  )
        {
          echo "ERROR.............: Not enough properties in PhpDoc!\n";
          echo "Source file.......: ".$class->sourcePath."\n";
          echo "Class.............: ".$className."\n";
          echo "Properties:.......: ".implode(', ', $diff)."\n\n";
        }
      }

/*      if( isset($class->methods['behaviors']) )
      {
        $class = new $className;
        $behaviors = $class->behaviors();
      }*/
    }
    echo "Done.\n\n";
  }

  public function checkPackage($sourceFiles)
  {
    echo "Checking PHPDoc @package in source files ...\n";
    foreach($sourceFiles as $sourceFile)
    {
      $fileContent = file($sourceFile);

      foreach($fileContent as $no=>$line)
      {
        if( preg_match('/^\s*\*\s*@package\s*([\w\.]+)/', $line, $matches, PREG_OFFSET_CAPTURE) )
        {
          if( Yii::getPathOfAlias($matches[1][0]) == dirname($sourceFile))
            continue;

          $docLine = $no + 1;
          $docName = $matches[1][0];

          echo "ERROR.............: Package path not valid!\n";
          echo "Source file.......: ".$sourceFile."\n";
          echo "Parameter line....: ".$docLine."\n";
          echo "Parameter value....: ".$docName."\n\n";
        }
      }
    }
    echo "Done.\n\n";
  }

  public function checkParams($sourceFiles)
  {
    $this->check($sourceFiles);
  }

  protected function getPhpDocsProperties($phpDoc)
  {
    $properties = array();

    if( preg_match_all('/\s*@property\s*([\w\|]+)\s*\$(\w+)/', $phpDoc, $matches) )
    {
      foreach($matches[0] as $key => $value)
        $properties[$matches[2][$key]] = $matches[1][$key];
    }

    return $properties;
  }

  protected function processClass($class)
  {
    $doc=new ClassDocExt;
    $doc->name=$class->getName();
    $doc->loadSource($class);
    $this->_currentClass=$doc->name;
    for($parent=$class;$parent=$parent->getParentClass();)
      $doc->parentClasses[]=$parent->getName();
    foreach($class->getInterfaces() as $interface)
      $doc->interfaces[]=$interface->getName();
    $doc->isInterface=$class->isInterface();
    $doc->isAbstract=$class->isAbstract();
    $doc->isFinal=$class->isFinal();
    $doc->methods=$this->processMethods($class);
    $doc->properties=$this->processProperties($class);
    $doc->signature=($doc->isInterface?'interface ':'class ').$doc->name;
    if($doc->isFinal)
      $doc->signature='final '.$doc->signature;
    if($doc->isAbstract && !$doc->isInterface)
      $doc->signature='abstract '.$doc->signature;
    if(in_array('CComponent',$doc->parentClasses))
    {
      $doc->properties=array_merge($doc->properties,$this->processComponentProperties($class));
      $doc->events=$this->processComponentEvents($class);
    }
    ksort($doc->properties);

    foreach($doc->properties as $property)
    {
      if($property->isProtected)
        $doc->protectedPropertyCount++;
      else
        $doc->publicPropertyCount++;
      if(!$property->isInherited)
        $doc->nativePropertyCount++;
    }
    foreach($doc->methods as $method)
    {
      if($method->isProtected)
        $doc->protectedMethodCount++;
      else
        $doc->publicMethodCount++;
      if(!$method->isInherited)
        $doc->nativeMethodCount++;
    }
    foreach($doc->events as $event)
    {
      if(!$event->isInherited)
        $doc->nativeEventCount++;
    }
    $this->processComment($doc,$class->getDocComment());

    $doc->phpDoc = $class->getDocComment();
    return $doc;
  }
}

class ClassDocExt extends ClassDoc
{
  public $phpDoc;
}
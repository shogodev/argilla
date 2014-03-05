<?php
/**
 * ApiCommand class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

Yii::import('frontend.commands.api.*');

/**
 * MessageCommand extracts messages to be translated from source files.
 * The extracted messages are saved as PHP message source files
 * under the specified directory.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.build
 * @since 1.0
 */
class ApiCommand extends CConsoleCommand
{
	const URL_PATTERN='/\{\{([^\}]+)\|([^\}]+)\}\}/';
	public $classes;
	public $packages;
	public $pageTitle;
	public $themePath;
	public $currentClass;
	public $baseSourceUrl="https://github.com/shogodev/argilla/blob/master/";
	public $version;

	protected function buildPackages($docPath)
	{
		file_put_contents($docPath.'/packages.txt',serialize($this->packages));
	}

	protected function buildKeywords($docPath)
	{
		$keywords=array();
		foreach($this->classes as $class)
			$keywords[]=$class->name;
		foreach($this->classes as $class)
		{
			$name=$class->name;
			foreach($class->properties as $property)
			{
				if(!$property->isInherited)
					$keywords[]=$name.'.'.$property->name;
			}
			foreach($class->methods as $method)
			{
				if(!$method->isInherited)
					$keywords[]=$name.'.'.$method->name.'()';
			}
		}
		file_put_contents($docPath.'/keywords.txt',implode(',',$keywords));
	}

	public function render($view,$data=null,$return=false,$layout='main')
	{
		$viewFile=$this->themePath."/views/{$view}.php";
		$layoutFile=$this->themePath."/layouts/{$layout}.php";
		$content=$this->renderFile($viewFile,$data,true);
		return $this->renderFile($layoutFile,array('content'=>$content),$return);
	}

	public function renderPartial($view,$data=null,$return=false)
	{
		$viewFile=$this->themePath."/views/{$view}.php";
		return $this->renderFile($viewFile,$data,$return);
	}

	public function renderSourceLink($sourcePath,$line=null)
	{
		if($line===null)
			return CHtml::link('framework'.$sourcePath,$this->baseSourceUrl.$sourcePath,array('class'=>'sourceLink'));
		else
			return CHtml::link('framework'.$sourcePath.'#'.$line, $this->baseSourceUrl.$sourcePath.'#L'.$line,array('class'=>'sourceLink'));
	}

	public function highlight($code,$limit=20)
	{
		$code=preg_replace("/^    /m",'',rtrim(str_replace("  ","    ",$code)));
		$code=highlight_string("<?php\n".$code,true);
		return preg_replace('/&lt;\\?php<br \\/>/','',$code,1);
	}

	protected function buildOfflinePages($docPath,$themePath)
	{
		$this->themePath=$themePath;
		@mkdir($docPath);
		$content=$this->render('index',null,true);
		$content=preg_replace_callback(self::URL_PATTERN,array($this,'fixOfflineLink'),$content);
		file_put_contents($docPath.'/index.html',$content);

		foreach($this->classes as $name=>$class)
		{
			$this->currentClass=$name;
			$this->pageTitle=$name;
			$content=$this->render('class',array('class'=>$class),true);
			$content=preg_replace_callback(self::URL_PATTERN,array($this,'fixOfflineLink'),$content);
			file_put_contents($docPath.'/'.$name.'.html',$content);
		}

		CFileHelper::copyDirectory($this->themePath.'/assets',$docPath);

		$content=$this->renderPartial('chmProject',null,true);
		file_put_contents($docPath.'/manual.hhp',$content);

		$content=$this->renderPartial('chmIndex',null,true);
		file_put_contents($docPath.'/manual.hhk',$content);

		$content=$this->renderPartial('chmContents',null,true);
		file_put_contents($docPath.'/manual.hhc',$content);
	}

	protected function buildModel($sourcePath,$options)
	{
		$files = CFileHelper::findFiles($sourcePath, $options);

    $model=new ApiModel;
		$model->build($files);
		return $model;
	}

	public function renderInheritance($class)
	{
		$parents=array($class->signature);
		foreach($class->parentClasses as $parent)
		{
			if(isset($this->classes[$parent]))
				$parents[]='{{'.$parent.'|'.$parent.'}}';
			else
				$parents[]=$parent;
		}
		return implode(" &raquo;\n",$parents);
	}

	public function renderImplements($class)
	{
		$interfaces=array();
		foreach($class->interfaces as $interface)
		{
			if(isset($this->classes[$interface]))
				$interfaces[]='{{'.$interface.'|'.$interface.'}}';
			else
				$interfaces[]=$interface;
		}
		return implode(', ',$interfaces);
	}

	public function renderSubclasses($class)
	{
		$subclasses=array();
		foreach($class->subclasses as $subclass)
		{
			if(isset($this->classes[$subclass]))
				$subclasses[]='{{'.$subclass.'|'.$subclass.'}}';
			else
				$subclasses[]=$subclass;
		}
		return implode(', ',$subclasses);
	}

	public function renderTypeUrl($type)
	{
		if(isset($this->classes[$type]) && $type!==$this->currentClass)
			return '{{'.$type.'|'.$type.'}}';
		else
			return $type;
	}

	public function renderSubjectUrl($type,$subject,$text=null)
	{
		if($text===null)
			$text=$subject;
		if(isset($this->classes[$type])) {
			return '{{'.$type.'::'.$subject.'-detail'.'|'.$text.'}}';
		}
		else
			return $text;
	}

	public function renderPropertySignature($property)
	{
		if(!empty($property->signature))
			return $property->signature;
		$sig='';
		if(!empty($property->getter))
			$sig=$property->getter->signature;
		if(!empty($property->setter))
		{
			if($sig!=='')
				$sig.='<br/>';
			$sig.=$property->setter->signature;
		}
		return $sig;
	}

	public function fixMethodAnchor($class,$name)
	{
		if(isset($this->classes[$class]->properties[$name]))
			return $name."()";
		else
			return $name;
	}

	protected function fixOfflineLink($matches)
	{
		if(($pos=strpos($matches[1],'::'))!==false)
		{
			$className=substr($matches[1],0,$pos);
			$method=substr($matches[1],$pos+2);
			return "<a href=\"{$className}.html#{$method}\">{$matches[2]}</a>";
		}
		else
			return "<a href=\"{$matches[1]}.html\">{$matches[2]}</a>";
	}
}

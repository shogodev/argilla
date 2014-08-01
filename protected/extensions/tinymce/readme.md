TinyMCE integration for yii
===========================
Almost in every application, i have need in wysiwyg editor for content.
In most of them I have used tinymce extension writen by MetaYii(with some ugly changes, added by me, to connect elFinder file manager to it).

Recently I have written my own widget for TinyMce and for elFinder with possibility of integrating them. Also I have written separate actions for TinyMce compessor and for spellchecker plugin. So i think that my code looks more cleaner than something like tinymceelfinder extension, that has similar functionality.

Also I have added less ugly skin for tinyMce(modified version of cirkuitSkin).

##TinyMCE Versions

There is two TinyMCE versions - 3.x and new 4.x

Extension has the same interfaces for both of them, but because they are different
they will have slightly different settings.

So when configuring it - refer to appropriate [documentation version](http://www.tinymce.com/wiki.php).

##Requirements

* Tested with Yii 1.1.14, but should work with previous versions too
* To use with elFinder, requires https://bitbucket.org/z_bodya/yii-elfinder

##Usage

1. Checkout source code to ext.tinymce
2. To use spellchecker and compressor, create controller and add corresponding actions to it
3. Use it as any other input widget:
4. More about elFinder extension here: https://bitbucket.org/z_bodya/yii-elfinder

        :::php
        // controller for tinyMce
        Yii::import('ext.tinymce.*');
        class TinyMceController extends CController
        {
             public function actions()
             {
                  return array(
                       'spellchecker' => array(
                           'class' => 'TinyMceSpellcheckerAction',
                       ),
                  );
              }
        }
        // in view
        $this->widget('ext.tinymce.TinyMce', array(
            'model' => $model,
            'attribute' => 'tinyMceArea',
            // Optional config
            //'spellcheckerUrl' => array('tinyMce/spellchecker'),
            // or use yandex spell: http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml
            'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
            'fileManager' => array(
                'class' => 'ext.elFinder.TinyMceElFinder',
                'connectorRoute'=>'admin/elfinder/connector',
            ),
            'htmlOptions' => array(
                'rows' => 6,
                'cols' => 60,
            ),
        ));


##CSRF token validation problem, for spellchecker requsts
By default Yii validates csrf token for all requsts, but spellchecker has requst content-type "application/json" - so even if we pass csrf token in request, yii will not validate it.

[Forum discussion about this](http://www.yiiframework.com/forum/index.php/topic/37367-csrf-validation-problem-for-requests-with-content-type-applicationjson/page__gopid__180225 "")

Also there is no need in csrf validation for spellchecker service, so possible solutions is to skip validation for such requests. I order to do so we need to extend CHttpRequst like in sample below:


        ::php
        class HttpRequest extends CHttpRequest
        {
            public function validateCsrfToken($event)
            {
                $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : null;
                if ($contentType !== 'application/json')
                    parent::validateCsrfToken($event);
            }
        }


And add it into application configuration:

        ::php
        // application components
        'components' => array(
            'request' => array(
               'class' => 'HttpRequest',
               'enableCsrfValidation' => true,
            ),
            ...
        ),

##Resources

 * [Extension page](https://bitbucket.org/z_bodya/yii-tinymce)
 * [elFinder extension](http://www.yiiframework.com/extension/elfinder/)
 * [TinyMce page](http://www.tinymce.com/)
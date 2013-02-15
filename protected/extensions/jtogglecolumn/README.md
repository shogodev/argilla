JToggleColumn
====
Column for CGridView which toggles the boolean ( TINYINT(1) ) value of model attribute. Tested with Yii 1.10.

Example
====
![JToggleColumn](https://bitbucket.org/johonunu/jtogglecolumn/raw/6220c9674443/example.png)

History
====
24.04.2012 - first release
25.04.2012 - added filter option and is now using assets
17.06.2012 - added ability to change action(two included: toggle(default) and switch), now using CActions

Tutorial
====
Extract downloaded zip to your components or extensions directory.

If you extracted to extensions directory add this line to import array in your /config/main.php :

    <?php
 
    'import'=>array(
        ...
        'application.extensions.jtogglecolumn.*', 
    )
    
    ?>

Define a JToggleColumn in your CGridView widget:

    <?php 
    
    $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'language-grid',
            'dataProvider'=>$model->search(),
            'filter'=>$model,
            'columns'=>array(
                    'id',
                    'name',
                    'lang_code',
                    'time_format',
                    array(
                                    'class'=>'JToggleColumn',
                                    'name'=>'is_active', // boolean model attribute (tinyint(1) with values 0 or 1)
                                    'filter' => array('0' => 'No', '1' => 'Yes'), // filter
                                    'htmlOptions'=>array('style'=>'text-align:center;min-width:60px;')
                    ),
                    array(
                                            'class'=>'JToggleColumn',
                                            'name'=>'is_default', // boolean model attribute (tinyint(1) with values 0 or 1)
                                            'filter' => array('0' => 'No', '1' => 'Yes'), // filter
                                            'action'=>'switch', // other action, default is 'toggle' action
                                            'checkedButtonImageUrl'=>'/images/toggle/yes.png', // checked image
                                            'uncheckedButtonImageUrl'=>'/images/toggle/no.png', // unchecked image
                                            'checkedButtonLabel'=>'No', // tooltip
                                            'uncheckedButtonLabel'=>'Yes', // tooltip
                                            'htmlOptions'=>array('style'=>'text-align:center;min-width:60px;')
                    ),
                    array(
                            'class'=>'CButtonColumn',
                    ),
            ),
    )); 

    ?>
 
Add action(s) in your controller:

    <?php
    
    public function actions(){
        return array(
                'toggle'=>'ext.jtogglecolumn.ToggleAction',
                'switch'=>'ext.jtogglecolumn.SwitchAction', // only if you need it
        );
    }
    
    ?>

Don't forget to add this action to controllers accessRules:

    <?php

    public function accessRules()
    {
            return array(
                    array('allow',
                            'actions'=>array('toggle','switch'),
                            'users'=>array('admin'),
                    )
            );
    }

    ?>

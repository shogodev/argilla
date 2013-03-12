<?php
/**
 * @var BInfo $model
 * @var BInfoController $this
 * @var integer $current
 */

$this->widget('CTreeView', array('options'     => array('persist'   => 'cookie',
                                                        'collapsed' => true,
                                                        'animated'  => 'fast'),
                                 'htmlOptions' => array('id'    => 'tree_'.get_class($model),
                                                        'class' => 'filetree'),
                                 'data'        => $model->getTreeView(null, true, $this->createUrl($this->id.'/update/'), $current),
));
?>
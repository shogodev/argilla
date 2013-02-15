<aside id="left">
  <div class="m30">
    <div class="nav-block-hd">
      <h4><?php echo isset($root) ? $root->name : ''?></h4>
    </div>

    <?php $this->widget('FMenu', array(
      'items' => $model->getMenu(),
      'onlyActiveItems' => true,
      'hideEmptyItems' => false,
      'firstItemCssClass' => 'first',
      'activateParents' => 'true',
       'htmlOptions' => array(
         'class' => 'nav-block news-block sidebar-nav',
       ),
    ));?>
  </div>
</aside>
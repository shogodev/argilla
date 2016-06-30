<?php
/**
 * @var BProductImportCronLogController $this
 */
Yii::app()->breadcrumbs->show();?>

<div>
  <?php echo $dataLog;?>
</div>
<script type="text/javascript">
  //<![CDATA[
  $(function() {
    $('.log-header').on('click', function(e) {
      e.preventDefault();
      $(this).next('.log-body').toggle();
    });
  });
  //]]>
</script>

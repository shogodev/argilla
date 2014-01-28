<?php
/**
 * @var FController $this
 */
?>

<?php $this->renderPartial('/popups/_callback', $_data_)?>

<?php $this->renderPartial('/user/_login_popup', $_data_)?>

<?php $this->renderPartial('/user/_registration', $_data_)?>

<?php $this->renderPartial('/popups/_compare', $_data_)?>

<?php $this->renderPartial('/popups/_fastorder', $_data_)?>

<?php if( $this->id != 'basket' ) $this->renderPartial('/panel/panel', $_data_)?>

<script>
  //<![CDATA[
  $(function(){
    $('body').on('click', '.popup .close,.popup .close-btn', function(e){
      e.preventDefault();
      var target = $(this).closest('.popup');
      $.overlayLoader(false, target);
    });
  });
  //]]>
</script>
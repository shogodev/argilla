<?php
/**
 * @var FController $this
 */
?>

<?php $this->renderPartial('/popups/_callback', $_data_)?>

<?php $this->renderPartial('/user/_login_popup', $_data_)?>

<?php //$this->renderPartial('/popups/_compare', $_data_)?>

<?php $this->renderPartial('/popups/_fastorder', $_data_)?>

<?php //if( $this->id != 'order' ) $this->renderPartial('/panel/panel', $_data_)?>
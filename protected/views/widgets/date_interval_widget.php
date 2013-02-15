<?php
/**
 * @var $this DateIntervalWidget
 */
?>
    <div class="select-container left m0">
      <select id="<?php echo $this->attribute?>-day">
        <?php foreach($this->getDays() as $day) { ?>
        <option value="<?php echo $day?>"><?php echo $day?></option>
        <?php } ?>
      </select>
    </div>
    <div class="select-container left m0">
      <select id="<?php echo $this->attribute?>-month">
        <?php foreach($this->getMonths() as $key => $month) { ?>
        <option value="<?php echo $key?>"><?php echo $month?></option>
        <?php } ?>
      </select>
    </div>
    <div class="select-container left m0">
      <select id="<?php echo $this->attribute?>-year">
        <?php foreach($this->getYears() as $year) { ?>
        <option value="<?php echo $year?>"><?php echo $year?></option>
        <?php } ?>
      </select>
    </div>

  <div class="calendar m5" <?php echo $this->hideCalendar ? ' style="display: none"' : ''?>></div>

  <?php
  echo CHtml::hiddenField(CHtml::resolveName($this->model, $this->attribute), !empty($this->form->model->{$this->attribute}) ? $this->form->model->{$this->attribute} : date('Y.m.d'));
  echo $this->form->getActiveFormWidget()->error($this->form->model, $this->attribute);
  ?>

<script type="text/javascript">
//<![CDATA[
$(function()
{
  var model = '<?php echo get_class($this->model)?>';
  var attr  = '<?php echo $this->attribute;?>';

/*
  $('.calendar')
    .datePicker({inline:true})
    .bind('dateSelected', function(e, selectedDate, $td) {
      updateSelects(selectedDate);
      $('#' + model + '_' + attr).val(selectedDate.asString('yyyy.mm.dd')).trigger('change');
    });
*/

  var updateSelects = function(date)
  {
    var selectedDate = new Date(date);
    $('#' + attr + '-day').val(selectedDate.getDate()).trigger('change');
    $('#' + attr + '-month').val(selectedDate.getMonth() + 1).trigger('change');
    $('#' + attr + '-year').val(selectedDate.getFullYear()).trigger('change');
  };

  $('#' + attr + '-day, ' + '#' + attr + '-month, ' + '#' + attr + '-year').on('change', function(e) {
    var d = new Date($('#' + attr + '-year').val(), $('#' + attr + '-month').val() - 1, $('#' + attr + '-day').val());
    //$('.calendar').dpSetSelected(d.asString());
        $('#' + model + '_' + attr).val(d.asString('yyyy.mm.dd')).trigger('change');
  });

  var parseDate = $('#' + model + '_' + attr).val().split('.');
  var defaultDate = new Date(parseDate[0], parseDate[1] - 1, parseDate[2]);
  updateSelects(defaultDate.getTime());
  //$('#' + attr + '-day').trigger('change');
});
//]]>
</script>



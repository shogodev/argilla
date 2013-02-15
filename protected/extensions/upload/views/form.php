<?php
/**
 * @var $this UploadWidget
 * @var $htmlOptions array
 */
?>

<div id="<?php echo $this->htmlOptions['id']?>">
  <!-- The table listing the files available for upload/download -->
  <table class="table table-striped table-bordered fileupload-files">
    <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
  </table>

  <div class="row-fluid fileupload-buttonbar">
    <div class="">
      <!-- The fileinput-button span is used to style the file input field as button -->
      <span class="btn btn-info fileinput-button">
        <span>Добавить файл<?php echo $this->multiple ? "ы" : ""?>...</span>
        <?php
        if ($this->hasModel()) :
          echo CHtml::activeFileField($this->model, $this->attribute, CMap::mergeArray($htmlOptions, array('style' => 'width: 1px;')))."\n";
        else :
          echo CHtml::fileField($name, $this->value, $htmlOptions)."\n";
        endif;
        ?>
      </span>
      <?php if( $this->multiple ) { ?>
      <button type="submit" class="btn btn-success start">
        <span>Начать загрузку</span>
      </button>
      <button type="reset" class="btn btn-warning cancel">
        <span>Отменить</span>
      </button>
      <button type="button" class="btn btn-danger delete">
        <span>Удалить</span>
      </button>
      <?php } ?>
    </div>
    <div class="span5" style="display:none!important">
      <!-- The global progress bar -->
      <div class="progress progress-info progress-striped active fade">
        <div class="bar" style="width:10%;"></div>
      </div>
    </div>
  </div>
  <!-- The loading indicator is shown during image processing -->
  <div class="fileupload-loading"></div>
</div>
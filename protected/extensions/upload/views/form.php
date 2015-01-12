<?php
/**
 * @var $this UploadWidget
 * @var $htmlOptions array
 */
?>
<div id="<?php echo $this->htmlOptions['id']?>">

  <!-- The table listing the files available for upload/download -->
  <table class="table table-striped table-bordered fileupload-files" data-toggle="modal-gallery" data-target="#modal-gallery" role="presentation">
    <tbody class="files"></tbody>
  </table>

  <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
  <div class="row-fluid fileupload-buttonbar">
    <div class="col-lg-7">
      <!-- The fileinput-button span is used to style the file input field as button -->
      <span class="btn btn-success fileinput-button">
          <i class="glyphicon glyphicon-plus"></i>
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
      <!-- The global file processing state -->
      <span class="fileupload-process"></span>
    </div>
    <!-- The global progress state -->
    <div class="col-lg-5 fileupload-progress fade" style="display: none">
      <!-- The global progress bar -->
      <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar progress-bar-success" style="width:0%;"></div>
      </div>
      <!-- The extended global progress state -->
      <div class="progress-extended">&nbsp;</div>
    </div>
  </div>
</div>
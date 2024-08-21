<?php echo e(Form::open(array('url'=>'document','method'=>'post', 'enctype' => "multipart/form-data"))); ?>

<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-12">
            <?php echo e(Form::label('name',__('Name'),array('class'=>'form-label'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter document name')))); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('category_id',__('Category'),array('class'=>'form-label'))); ?>

            <?php echo e(Form::select('category_id',$category,null,array('class'=>'form-control hidesearch','id'=>'category'))); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('sub_category_id',__('Sub Category'),array('class'=>'form-label'))); ?>

            <div class="sc_div">
                <select class="form-control hidesearch sub_category_id" id="sub_category_id" name="sub_category_id">
                    <option value=""><?php echo e(__('Select Sub Category')); ?></option>
                </select>
            </div>
        </div>
        <div class="form-group  col-md-12">
            <?php echo e(Form::label('document',__('Document'),array('class'=>'form-label'))); ?>

            <?php echo e(Form::file('document',array('class'=>'form-control'))); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('tages',__('Tages'),array('class'=>'form-label'))); ?>

            <?php echo e(Form::select('tages[]',$tages,null,array('class'=>'form-control hidesearch','multiple'))); ?>

        </div>
        <div class="form-group  col-md-12">
            <?php echo e(Form::label('description',__('Description'),array('class'=>'form-label'))); ?>

            <?php echo e(Form::textarea('description',null,array('class'=>'form-control','rows'=>3))); ?>

        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
    <?php echo e(Form::submit(__('Create'),array('class'=>'btn btn-primary btn-rounded'))); ?>

</div>
<?php echo e(Form::close()); ?>

<script>
    var url = "<?php echo e(route("category.sub-category", ":id")); ?>";
</script>
<script src="<?php echo e(asset('js/custom/document.js')); ?>"></script>
<?php /**PATH /home/mpurseco/public_html/vms/resources/views/document/create.blade.php ENDPATH**/ ?>
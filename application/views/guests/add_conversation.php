<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="assets/js/pages/editor_ckeditor.js"></script>
<?php
$edit = 0;
if (isset($account)) {
    $edit = 1;
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($account))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-plus-circle2"></i>';
                ?>
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('accounts'); ?>"><i class="icon-grid6 position-left"></i> Accounts</a></li>
            <li class="active"><?php echo $heading; ?></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->session->flashdata('success')) {
                ?>
                <div class="alert alert-success hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('success') ?></strong>
                </div>
                <?php
            } else if ($this->session->flashdata('error')) {
                ?>
                <div class="alert alert-danger hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('error') ?></strong>
                </div>

                <?php
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <form class="form-horizontal form-validate-jquery" action="" id="add_conversation_form" method="post" enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Conversation <span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <textarea name="editor-full" id="editor-full" rows="4" cols="4">										
                                    <?php echo (isset($conversation)) ? $conversation['note'] : set_value('note'); ?>			
                                </textarea>
                                <?php
                                echo '<label id="tax_id-error" class="validation-error-label" for="website">' . form_error('website') . '</label>';
                                ?>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">Media<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <div class="media no-margin-top">
                                    <div class="media-left" id="image_preview_div">
                                        <?php
                                        $required = 'required';
                                        if (isset($conversation) && $conversation['media'] != '') {
                                            $required = '';
                                            ?>
                                            <img src="<?php echo GUEST_IMAGES . $conversation['media']; ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php } else {
                                            ?>
                                            <img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php } ?>
                                    </div>

                                    <div class="media-body">
                                        <input type="file" name="media" id="media" class="file-styled" onchange="readURL(this);" <?php echo $required; ?>>
                                        <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                    </div>
                                </div>
                                <?php
                                if (isset($logo_validation))
                                    echo '<label id="logo-error" class="validation-error-label" for="logo">' . $logo_validation . '</label>';
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-12">
                                <button class="btn btn-success" type="submit" id="account_btn_submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript">
    var edit = <?php echo $edit ?>;
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-pink'
    });
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch

    $("#add_conversation_form").validate({
        ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
        errorClass: 'validation-error-label',
        successClass: 'validation-valid-label',
        highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        },
        unhighlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        },
        // Different components require proper error label placement
        errorPlacement: function (error, element) {

            // Styled checkboxes, radios, bootstrap switch
            if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container')) {
                if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                    error.appendTo(element.parent().parent().parent().parent());
                } else {
                    error.appendTo(element.parent().parent().parent().parent().parent());
                }
            }

            // Unstyled checkboxes, radios
            else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                error.appendTo(element.parent().parent().parent());
            }

            // Input with icons and Select2
            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                error.appendTo(element.parent());
            }

            // Inline checkboxes, radios
            else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                error.appendTo(element.parent().parent());
            }

            // Input group, styled file input
            else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                error.appendTo(element.parent().parent());
            } else {
                error.insertAfter(element);
            }
        },
        validClass: "validation-valid-label",
        success: function (label) {
            label.addClass("validation-valid-label")
        },
        rules: {

        },
        submitHandler: function (form) {
            $('#account_btn_submit').attr('disabled', true);
            form.submit();
        }
    });

</script>
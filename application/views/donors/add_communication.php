<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="assets/js/pages/editor_ckeditor.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<?php
$edit = 0;
if (isset($donor_communication)) {
    $edit = 1;
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($donor_communication))
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
            <li><a href="<?php echo site_url('donors'); ?>"><i class="icon-coins position-left"></i> Donors</a></li>
            <!--<li><a href="<?php echo site_url('accounts'); ?>"><i class="icon-comment-discussion position-left"></i> Guest Communication</a></li>-->
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
                            <label class="col-lg-2 control-label">Communication Date <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    <input type="text" name="communication_date" id="communication_date" placeholder="Enter Communication Date" class="form-control pickadate" value="<?php echo (isset($donor_communication)) ? date('d F, Y', strtotime($donor_communication['communication_date'])) : set_value('communication_date'); ?>" required="required">
                                </div>
                                <?php
                                echo '<label id="communication_date-error" class="validation-error-label" for="communication_date">' . form_error('communication_date') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Follow Up Date <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    <input type="text" name="follow_up_date" id="follow_up_date" placeholder="Enter Follow Up Date" class="form-control pickadate" value="<?php echo (isset($donor_communication)) ? date('d F, Y', strtotime($donor_communication['follow_up_date'])) : set_value('follow_up_date'); ?>" required="required">
                                </div>
                                <?php
                                echo '<label id="follow_up_date-error" class="validation-error-label" for="follow_up_date">' . form_error('follow_up_date') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Subject <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="subject" id="subject" placeholder="Enter Subject" class="form-control" required="required" value="<?php echo (isset($donor_communication) && $donor_communication['subject']) ? $guest_communication['subject'] : set_value('subject'); ?>">
                                <?php
                                echo '<label id="subject-error" class="validation-error-label" for="subject">' . form_error('subject') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Communication <span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <textarea name="note" id="editor-full" rows="4" cols="4">										
                                    <?php echo (isset($donor_communication)) ? $donor_communication['note'] : set_value('note'); ?>			
                                </textarea>
                                <?php
                                echo '<label id="note-error" class="validation-error-label" for="note">' . form_error('note') . '</label>';
                                ?>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">Media</label>
                            <div class="col-lg-6">
                                <div class="media no-margin-top">
                                    <div class="media-left" id="image_preview_div">
                                        <?php
                                        $required = 'required';
                                        if (isset($donor_communication) && $donor_communication['media'] != '') {
                                            $required = '';
                                            if (preg_match("/\.(gif|png|jpg)$/", $donor_communication['media'])) {
                                                ?>
                                                <img src="<?php echo COMMUNICATION_IMAGES . $donor_communication['media']; ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                            <?php } else { ?>
                                                <a class="fancybox" target="_blank" href="<?php echo COMMUNICATION_IMAGES . $donor_communication['media']; ?>" data-fancybox-group="gallery" ><img src="assets/images/default_file.png" height="55px" width="55px" alt="" class="img-circle"/></a>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php } ?>
                                    </div>

                                    <div class="media-body">
                                        <input type="file" name="media" id="media" class="file-styled" onchange="readURL(this);ValidateSingleInput(this)">
                                        <span class="help-block">Accepted formats:  png, jpg , jpeg, doc, docx, pdf</span>
                                    </div>
                                </div>
                                <?php
                                if (isset($media_validation))
                                    echo '<label id="logo-error" class="validation-error-label" for="logo">' . $media_validation . '</label>';
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
<div id="validation_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-teal-400">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title"></h6>
            </div>
            <div class="modal-body panel-body validation_alert">
                <label></label>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var edit = <?php echo $edit ?>;
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-pink'
    });
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch
    $('.pickadate').pickadate({
        max: new Date()
    });
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
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var valid_extensions = /(\.jpg|\.jpeg|\.png)$/i;
                if (valid_extensions.test(input.files[0].name)) {
                    var html = '<img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                } else {
                    var html = '<img src="assets/images/default_file.png" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                }
                $('#image_preview_div').html(html);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    var _validFileExtensions = [".jpg", ".jpeg", ".doc", ".png", ".docx", ".pdf", ".xlsx"];
//    var _validFileExtensions_Video = [".mp4", ".webm", ".ogv", ".png",".MPG",".MPEG" ,".OGG",".ogg",".mpeg"];    
    function ValidateSingleInput(oInput) {
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    }
                }
                if (!blnValid) {
                    $(".validation_alert label").text("Sorry, invalid file, allowed extensions are: " + _validFileExtensions.join(", "));
                    $("#validation_modal").modal();
                    oInput.value = "";
                    return false;
                }
            }
        }
        return true;
    }
</script>
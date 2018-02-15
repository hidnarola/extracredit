<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdyDSU074CCHVR2oygIqTLO9_ZOZEVrWE"  type="text/javascript"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/js/jquery.custom_validate.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

<?php
$edit = 0;
if (isset($guest)) {
    $edit = 1;
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($guest))
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
            <li><a href="<?php echo site_url('guests'); ?>"><i class="icon-people position-left"></i> Guests</a></li>
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
                    <form class="form-horizontal form-validate-jquery" action="" id="add_guest_form" method="post" enctype="multipart/form-data">                        
                        <fieldset class="content-group">
                            <legend class="text-bold">Basic Guest Details</legend>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">First Name <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="firstname" id="firstname" required="" placeholder="Enter First Name" class="form-control text-capitalize" value="<?php echo (isset($guest)) ? $guest['firstname'] : set_value('firstname'); ?>">
                                    <?php
                                    echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                                    ?>
                                </div>

                                <label class="col-lg-1 control-label">Last Name </label>
                                <div class="col-lg-4">
                                    <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" class="form-control text-capitalize" value="<?php echo (isset($guest)) ? $guest['lastname'] : set_value('lastname'); ?>">
                                    <?php
                                    echo '<label id="lastname-error" class="validation-error-label" for="lastname">' . form_error('lastname') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Company Name</label>
                                <div class="col-lg-4">
                                    <input type="text" name="companyname" id="companyname" placeholder="Enter Company Name" class="form-control text-capitalize"  value="<?php echo (isset($guest)) ? $guest['companyname'] : set_value('companyname'); ?>">
                                    <?php
                                    echo '<label id="companyname-error" class="validation-error-label" for="companyname">' . form_error('companyname') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Company Website </label>
                                <div class="col-lg-4">
                                    <input type="text" name="company_website" id="company_website" placeholder="Enter Company Website" class="form-control" value="<?php echo (isset($guest)) ? $guest['company_website'] : set_value('company_website'); ?>">
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Address</label>
                                <div class="col-lg-4">
                                    <textarea name="address" id="address" placeholder="Enter Address" class="form-control text-capitalize"><?php echo (isset($guest)) ? $guest['address'] : set_value('address'); ?></textarea>
                                    <?php
                                    echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Email</label>
                                <div class="col-lg-4">
                                    <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($guest) && $guest['email']) ? $guest['email'] : set_value('email'); ?>">
                                    <?php
                                    echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">City</label>
                                <div class="col-lg-4" id="city_wrap">
                                    <input type="text" name="city_id" id="city_id" readonly="" placeholder="Enter City" class="form-control"  value="<?php echo (isset($guest)) ? $guest['city'] : set_value('city_id'); ?>">
                                    <?php
                                    echo '<label id="city_id-error" class="validation-error-label" for="city_id">' . form_error('city_id') . '</label>';
                                    ?>
                                </div>
                                <input type="hidden" name="state_short" id="state_short" value="<?php echo (isset($guest)) ? $guest['state_short'] : set_value('state_short'); ?>"/>
                                <label class="col-lg-1 control-label">State</label>
                                <div class="col-lg-4">
                                    <input type="text" name="state_id" id="state_id" readonly="" placeholder="Enter State" class="form-control"  value="<?php echo (isset($guest)) ? $guest['state'] : set_value('state_id'); ?>">

                                    <?php
                                    echo '<label id="state_id-error" class="validation-error-label" for="state_id">' . form_error('state_id') . '</label>';
                                    ?>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Zip</label>
                                <div class="col-lg-4">
                                    <input type="text" name="zip" id="zip" placeholder="Enter Zip" class="form-control"  value="<?php echo (isset($guest) && $guest['zip']) ? $guest['zip'] : set_value('zip'); ?>">
                                    <?php
                                    echo '<label id="zip-error" class="validation-error-label" for="zip">' . form_error('zip') . '</label>';
                                    ?>
                                </div>

                                <!--                                <label class="col-lg-1 control-label">Phone</label>
                                                                <div class="col-lg-4">
                                                                    <input type="text" name="phone" id="phone" placeholder="Enter Phone" class="form-control"  value="<?php echo (isset($guest) && $guest['phone']) ? $guest['phone'] : set_value('phone'); ?>">
                                <?php
                                echo '<label id="phone-error" class="validation-error-label" for="phone">' . form_error('phone') . '</label>';
                                ?>
                                                                </div>-->
                            </div>



                            <div class="form-group">
                                <label class="col-lg-1 control-label">Phone</label>
                                <div class="col-lg-4">
                                    <input type="text" name="phone" id="phone" placeholder="Enter Phone" class="form-control"  value="<?php echo (isset($guest) && $guest['phone']) ? $guest['phone'] : set_value('phone'); ?>">


                                </div>
                                <label class="control-label col-lg-1">Logo</label>
                                <div class="col-lg-4">
                                    <div class="media no-margin-top">
                                        <div class="media-left" id="image_preview_div">
                                            <?php
                                            $required = 'required';
                                            if (isset($guest) && $guest['logo'] != '') {
                                                $required = '';
                                                ?>
                                                <img src="<?php echo GUEST_IMAGES . $guest['logo']; ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                            <?php } else {
                                                ?>
                                                <img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                            <?php } ?>
                                        </div>

                                        <div class="media-body">
                                            <input type="file" name="logo" id="logo" class="file-styled" onchange="readURL(this);ValidateSingleInput(this);">
                                            <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($logo_validation))
                                        echo '<label id="logo-error" class="validation-error-label" for="logo">' . $logo_validation . '</label>';
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend class="text-bold">Program</legend>  
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Program</label>
                                <div class="col-lg-6">
                                    <select name="account_id" id="account_id" class="select2" >
                                        <option value="">None</option>
                                        <?php
                                        foreach ($accounts as $account) {
                                            $selected = '';
                                            if (isset($guest) && $guest['account_id'] == $account['id'])
                                                $selected = 'selected';
                                            ?>
                                            <option value="<?php echo $account['id']; ?>" <?php echo $selected ?>><?php echo ($account['action_matters_campaign'] != '') ? $account['action_matters_campaign'] : $account['vendor_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    echo '<label id="account_id-error" class="validation-error-label" for="account_id">' . form_error('account_id') . '</label>';
                                    ?>
                                </div>

                            </div>
                        </fieldset>
                        <fieldset class="content-group">
                            <legend class="text-bold">Extra Guest Details</legend>                          
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Invite Date</label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="invite_date" id="invite_date" placeholder="Enter Invite Date" class="form-control pickadate" value="<?php
                                        if (isset($guest) && !empty($guest['invite_date'])) {
                                            echo date('d F Y', strtotime($guest['invite_date']));
                                        } else
                                            echo set_value('invite_date');
                                        ?>" >
                                    </div>
                                    <?php
                                    echo '<label id="invite_date-error" class="validation-error-label" for="invite_date">' . form_error('invite_date') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Guest Date</label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="guest_date" id="guest_date" placeholder="Enter Guest Date" class="form-control pickadate" value="<?php
                                        if (isset($guest) && !empty($guest['guest_date'])) {
                                            echo date('d F Y', strtotime($guest['guest_date']));
                                        } else
                                            echo set_value('guest_date');
                                        ?>" >
                                    </div>
                                    <?php
                                    echo '<label id="guest_date-error" class="validation-error-label" for="guest_date">' . form_error('guest_date') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">AIR Date</label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="AIR_date" id="post_date" placeholder="Enter AIR Date" class="form-control pickadate" value="<?php
                                        if (isset($guest) && !empty($guest['AIR_date'])) {
                                            echo date('d F Y', strtotime($guest['AIR_date']));
                                        } else
                                            echo set_value('AIR_date');
                                        ?>" >
                                    </div>
                                    <?php
                                    echo '<label id="AIR_date-error" class="validation-error-label" for="AIR_date">' . form_error('AIR_date') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">AMC created?</label>
                                <div class="col-lg-4">
                                    <div class="checkbox checkbox-switch">
                                        <label>
                                            <input type="checkbox" name="AMC_created" id="AMC_created" data-off-color="danger" data-on-text="Yes" data-off-text="No" class="switch" <?php
                                            if (isset($guest) && $guest['AMC_created'] == 'No')
                                                echo '';
                                            else
                                                echo 'checked="checked"';
                                            ?> value="1">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Assistant</label>
                                <div class="col-lg-4">
                                    <input type="text" name="assistant" id="assistant" placeholder="Enter Assistant" class="form-control"  value="<?php echo (isset($guest) && $guest['assistant']) ? $guest['assistant'] : set_value('assistant'); ?>">
                                    <?php
                                    echo '<label id="assistant-error" class="validation-error-label" for="assistant">' . form_error('assistant') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">AMC Active?</label>
                                <div class="col-lg-4">
                                    <div class="checkbox checkbox-switch">
                                        <label>
                                            <input type="checkbox" name="AMC_active" id="AMC_active" data-off-color="danger" data-on-text="Yes" data-off-text="No" class="switch" <?php
                                            if (isset($guest) && $guest['AMC_active'] == 'No')
                                                echo '';
                                            else if (isset($guest) && $guest['AMC_active'] == 'Yes')
                                                echo 'checked="checked"';
                                            else
                                                echo '';
                                            ?> value="1">
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Assistant Email</label>
                                <div class="col-lg-4">
                                    <input type="text" name="assistant_email" id="assistant_email" placeholder="Enter Assistant Email" class="form-control"  value="<?php echo (isset($guest) && $guest['assistant_email']) ? $guest['assistant_email'] : set_value('assistant_email'); ?>">
                                    <?php
                                    echo '<label id="assistant_email-error" class="validation-error-label" for="assistant_email">' . form_error('assistant_email') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Assistant Phone</label>
                                <div class="col-lg-4">
                                    <input type="text" name="assistant_phone" id="assistant_phone" placeholder="Enter Assistant Phone" class="form-control"  value="<?php echo (isset($guest) && $guest['assistant_phone']) ? $guest['assistant_phone'] : set_value('assistant_phone'); ?>">
                                    <?php
                                    echo '<label id="assistant_phone-error" class="validation-error-label" for="assistant_phone">' . form_error('assistant_phone') . '</label>';
                                    ?>
                                </div>
                            </div>

                        </fieldset>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type="submit" name="save" class="btn bg-teal custom_save_button" id="guest_btn_submit">Save<i class="icon-arrow-right14 position-right"></i></button>
                                <button type="button" class="btn border-slate btn-flat cancel-btn custom_cancel_button" onclick="window.history.back()">Cancel</button>
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
    // Styled file input
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-blue'
    });
    $("#phone").mask("999-999-9999");
    $("#assistant_phone").mask("999-999-9999");
    $('.pickadate').pickadate({
        format: 'd mmmm yyyy'
//        max: new Date()
    });

    var edit = <?php echo $edit ?>;
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch

    $('#logo').change(function () {
        $(this).rules("add", {
            extension: "jpg|png|jpeg",
            maxFileSize: {
                "unit": "MB",
                "size": 2
            }
        });
    });
    $("#add_guest_form").validate({
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
//            email: {
//                required: true,
//                email: true,
//            },
//            company_website: {
//                validUrl: true
//            },
//            assistant_email: {
//                required: true,
//                email: true,
//            },
//            phone: {
//                required: true,
//                phoneno: true
//            },
//            assistant_phone: {
//                required: true,
//                phoneno: true
//            },
//            zip: {
//                zipcodeUS: true
//            }
        },
        submitHandler: function (form) {
            $('#guest_btn_submit').attr('disabled', true);
            form.submit();
        }
    });

    jQuery.validator.addMethod("phoneno", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
                phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
    }, "Please specify a valid phone number");
    // Display the preview of image on image upload
//    function readURL(input) {
//        if (input.files && input.files[0]) {
//            var reader = new FileReader();
//
//            reader.onload = function (e) {
//                var html = '<img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
//                $('#image_preview_div').html(html);
//            }
//            reader.readAsDataURL(input.files[0]);
//        }
//    }
</script>
<script>
    $(document).ready(function () {
        var geocoder = new google.maps.Geocoder();
        //when the user clicks off of the zip field:
        $("#zip").on("keyup keydown change", function () {
            if ($(this).val().length == 5) {
                var zip = $(this).val();
                var city = '';
                var state = '';
                var state_short = '';
                //make a request to the google geocode api
                $.getJSON('https://maps.googleapis.com/maps/api/geocode/json?address=' + zip)
                        .success(function (response) {
                            //find the city and state
                            var address_components = response.results[0].address_components;
                            $.each(address_components, function (index, component) {
                                var types = component.types;
                                $.each(types, function (index, type) {
                                    if (type == 'locality') {
                                        city = component.long_name;
                                    }
                                    if (type == 'administrative_area_level_1') {
                                        state_short = component.short_name;
                                        state = component.long_name;
                                    }
                                });
                            });
                            //pre-fill the city and state
                            var cities = response.results[0].postcode_localities;
                            if (cities) {
                                //turn city into a dropdown if necessary
                                var $select = $(document.createElement('select'));
                                $.each(cities, function (index, locality) {
                                    var $option = $(document.createElement('option'));
                                    $option.html(locality);
                                    $option.attr('value', locality);
                                    if (city == locality) {
                                        $option.attr('selected', 'selected');
                                    }
                                    $select.append($option);
                                });
                                $select.attr('id', 'city_id');
                                $select.attr('name', 'city_id');
                                $('#city_wrap').html($select);
                                $('#city_id').select2();
                            } else {
                                var txtbox = '<input type="text" name="city_id" id="city_id" placeholder="City" class="form-control" required="required" value="' + city + '" readonly>'
                                $('#city_wrap').html(txtbox);
                                $('#city_id').val(city);
                            }
                            $('#state_id').val(state);
                            $('#state_short').val(state_short);
                        });
            }
        });
    });
    /*Validator method for US Zipcode*/
    $.validator.addMethod("zipcodeUS", function (value, element) {
        return this.optional(element) || /^\d{5}-\d{4}$|^\d{5}$/.test(value);
    }, "The specified US ZIP Code is invalid");
    /*Validator method for valid URL*/
    $.validator.addMethod('validUrl', function (value, element) {
        var url = $.validator.methods.url.bind(this);
        return url(value, element) || url('http://' + value, element);
    }, 'Please enter a valid URL');


    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var valid_extensions = /(\.jpg|\.jpeg|\.png)$/i;
                if (typeof (input.files[0]) != 'undefined') {
                    if (valid_extensions.test(input.files[0].name)) {
                        var html = '<img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                    } else {
                        var html = '<img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                    }
                } else {
                    var html = '<img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                }
                $('#image_preview_div').html(html);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    var _validFileExtensions = [".jpg", ".jpeg", ".png"];
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

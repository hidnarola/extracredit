<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdyDSU074CCHVR2oygIqTLO9_ZOZEVrWE"  type="text/javascript"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/core/libraries/jasny_bootstrap.min.js"></script>
<?php
$edit = 0;
if (isset($vendor))
    $edit = 1;
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($vendor))
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
            <li><a href="<?php echo site_url('vendors'); ?>"><i class="icon-file-media position-left"></i> Vendors</a></li>
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
                    <form class="form-horizontal form-validate-jquery" action="" id="add_vendor_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-lg-1 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <input type="text" name="name" id="name" placeholder="Enter Vendor Name" class="form-control text-capitalize" value="<?php echo (isset($vendor)) ? $vendor['name'] : set_value('name'); ?>" required>
                                <?php
                                echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                                ?>
                            </div>
                            <label class="col-lg-1 control-label">Contact <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <input type="text" name="contact_name" id="contact_name" placeholder="Enter Contact Name" class="form-control" value="<?php echo (isset($vendor) && $vendor['contact_name']) ? $vendor['contact_name'] : set_value('contact_name'); ?>" required>
                                <?php
                                echo '<label id="contact_name-error" class="validation-error-label" for="contact_name">' . form_error('contact_name') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-1 control-label">Address </label>
                            <div class="col-lg-4">
                                <textarea name="address" id="address" placeholder="Enter Address" class="form-control text-capitalize" ><?php echo (isset($vendor)) ? $vendor['address'] : set_value('address'); ?></textarea>
                                <?php
                                echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                                ?>
                            </div>
                            <label class="col-lg-1 control-label">Zip </label>
                            <div class="col-lg-4">
                                <input type="text" name="zip" id="zip" placeholder="Enter Zip" class="form-control" value="<?php echo (isset($vendor) && $vendor['zip']) ? $vendor['zip'] : set_value('zip'); ?>">
                                <?php
                                echo '<label id="zip-error" class="validation-error-label" for="zip">' . form_error('zip') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-1 control-label">City </label>
                            <div class="col-lg-4" id="city_wrap">
                                <input type="text" name="city_id" id="city_id" readonly="" class="form-control" value="<?php echo (isset($vendor)) ? $vendor['city'] : set_value('city_id'); ?>">
                                <?php
                                echo '<label id="city_id-error" class="validation-error-label" for="city_id">' . form_error('city_id') . '</label>';
                                ?>
                            </div>
                            <label class="col-lg-1 control-label">State </label>
                            <div class="col-lg-4">
                                <input type="text" name="state_id" id="state_id" readonly="" class="form-control"  value="<?php echo (isset($vendor)) ? $vendor['state'] : set_value('state_id'); ?>">

                                <?php
                                echo '<label id="state_id-error" class="validation-error-label" for="state_id">' . form_error('state_id') . '</label>';
                                ?>
                            </div>
                            <input type="hidden" name="state_short" id="state_short" value="<?php echo (isset($vendor)) ? $vendor['state_short'] : set_value('state_short'); ?>"/>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-1 control-label">Phone </label>
                            <div class="col-lg-4">
                                <input type="text" name="phone" id="phone" placeholder="Enter Phone" class="form-control" value="<?php echo (isset($vendor) && $vendor['phone']) ? $vendor['phone'] : set_value('phone'); ?>" data-mask="999-999-9999">
                                <?php
                                echo '<label id="phone-error" class="validation-error-label" for="phone">' . form_error('phone') . '</label>';
                                ?>
                            </div>

                            <div class="program_div">
                                <label class="col-lg-1 control-label">Email </label>
                                <div class="col-lg-4">
                                    <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($vendor) && $vendor['email']) ? $vendor['email'] : set_value('email'); ?>">
                                    <?php
                                    echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-1 control-label">Website </label>
                            <div class="col-lg-4">
                                <input type="text" name="website" id="website" placeholder="Enter website" class="form-control" value="<?php echo (isset($vendor) && $vendor['website']) ? $vendor['website'] : set_value('website'); ?>" >
                                <?php
                                echo '<label id="website-error" class="validation-error-label" for="website">' . form_error('website') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type="submit" name="save" class="btn bg-teal custom_save_button" id="vendor_btn_submit">Save<i class="icon-arrow-right14 position-right"></i></button>
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
<script type="text/javascript">
    var edit = <?php echo $edit ?>;
    var vendor_url = site_url + 'vendors/checkUniqueVendor/';
    if (edit == 1) {
        var append_id = <?php echo (isset($vendor)) ? $vendor['id'] : 0 ?>;
        vendor_url += btoa(append_id);
    }
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch

    $("#add_vendor_form").validate({
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
            name: {
                required: true,
                remote: vendor_url,
            },
            contact_name: {
                required: true,
            },
            email: {
                email: true,
            },
            website: {
                validUrl: true,
            },
            zip: {
                zipcodeUS: true
            }
        },
        messages: {
            name: {
                remote: $.validator.format("Vendor name with this is already added!")
            },
        },
        submitHandler: function (form) {
            $('#vendor_btn_submit').attr('disabled', true);
            form.submit();
        }
    });
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
                                $select.addClass('select2');
                                $select.attr('name', 'city_id');
                                $select.attr('id', 'city_id');
                                $.each(cities, function (index, locality) {
                                    var $option = $(document.createElement('option'));
                                    $option.html(locality);
                                    $option.attr('value', locality);
                                    if (city == locality) {
                                        $option.attr('selected', 'selected');
                                    }
                                    $select.append($option);
                                });
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
        value = $.trim(value);
        var url = $.validator.methods.url.bind(this);
        return url(value, element) || url('http://' + value, element);
    }, 'Please enter a valid URL');
</script>
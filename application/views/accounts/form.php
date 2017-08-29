<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
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
                if (isset($user))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-user-plus"></i>';
                ?>
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('accounts'); ?>"><i class="icon-users4 position-left"></i> Accounts</a></li>
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
                    <form class="form-horizontal form-validate-jquery" action="" id="add_account_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Fund Types <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="fund_type" id="fund_type" class="select2" required="required">
                                    <?php
                                    foreach ($fund_types as $type) {
                                        $selected = '';
                                        if (isset($account) && $account['fund_type_id'] == $val['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?php echo $type['id']; ?>" <?php echo $selected ?>><?php echo $type['type'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                echo '<label id="fund_type-error" class="validation-error-label" for="fund_type">' . form_error('fund_type') . '</label>';
                                ?>
                            </div>
                        </div>
                        <?php
                        $program_div_style = '';
                        $vendor_div_style = 'style="display:none"';
                        if (isset($account) && $account['is_vendor'] == 1) {
                            $program_div_style = 'style="display:none"';
                            $vendor_div_style = '';
                        }
                        ?>
                        <div class="form-group program_div" <?php echo $program_div_style ?>>
                            <label class="col-lg-2 control-label">Action Matters Campaign <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="action_matters_campaign" id="action_matters_campaign" placeholder="Enter Action Matters Campaign" class="form-control" required="required" value="<?php echo (isset($account)) ? $account['action_matters_campaign'] : set_value('action_matters_campaign'); ?>">
                                <?php
                                echo '<label id="action_matters_campaign-error" class="validation-error-label" for="action_matters_campaign">' . form_error('action_matters_campaign') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group vendor_div" <?php echo $vendor_div_style ?>>
                            <label class="col-lg-2 control-label">Vendor Name <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="vendor_name" id="vendor_name" placeholder="Enter Vendor Name" class="form-control" required="required" value="<?php echo (isset($account)) ? $account['vendor_name'] : set_value('vendor_name'); ?>">
                                <?php
                                echo '<label id="vendor_name-error" class="validation-error-label" for="vendor_name">' . form_error('vendor_name') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Contact Name <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="contact_name" id="contact_name" placeholder="Enter Contact Name" class="form-control" required="required" value="<?php echo (isset($account)) ? $account['contact_name'] : set_value('contact_name'); ?>">
                                <?php
                                echo '<label id="contact_name-error" class="validation-error-label" for="contact_name">' . form_error('contact_name') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Address <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <textarea name="address" id="address" placeholder="Enter Address" class="form-control" required="required"><?php echo (isset($account)) ? $account['address'] : set_value('address'); ?></textarea>
                                <?php
                                echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">State <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="state_id" id="state_id" class="select2" required="required">
                                    <?php
                                    foreach ($states as $state) {
                                        $selected = '';
                                        if (isset($account) && $account['state_id'] == $val['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?php echo $state['id']; ?>" <?php echo $selected ?>><?php echo $state['name'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                echo '<label id="state_id-error" class="validation-error-label" for="state_id">' . form_error('state_id') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">City <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="city_id" id="city_id" class="select2" required="required">
                                    <?php
                                    foreach ($cities as $city) {
                                        $selected = '';
                                        if (isset($account) && $account['city_id'] == $val['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?php echo $city['id']; ?>" <?php echo $selected ?>><?php echo $city['name'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                echo '<label id="city_id-error" class="validation-error-label" for="city_id">' . form_error('city_id') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Email <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($user) && $user['email']) ? $user['email'] : set_value('email'); ?>">
                                <?php
                                echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Is Active? <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <div class="checkbox checkbox-switch">
                                    <label>
                                        <input type="checkbox" name="is_active" id="is_active" data-off-color="danger" data-on-text="Yes" data-off-text="No" class="switch" <?php
                                        if (isset($account) && $account['is_active'] == 0)
                                            echo '';
                                        else
                                            echo 'checked="checked"';
                                        ?> value="1">
                                    </label>
                                </div>
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
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch
    //-- fund type change event
    $('#fund_type').change(function () {
        $.ajax({
            url: '<?php echo site_url('accounts/get_fund_type') ?>',
            data: {id: btoa($(this).val())},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if (data.is_vendor == 1) {
                    $('.vendor_div').show();
                    $('.program_div').hide();
                } else {
                    $('.vendor_div').hide();
                    $('.program_div').show();
                }
            }
        });
    });
    $('#state_id').change(function () {
        $.ajax({
            url: '<?php echo site_url('accounts/get_cities') ?>',
            data: {id: $(this).val()},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if (data.is_vendor == 1) {
                    $('.vendor_div').show();
                    $('.program_div').hide();
                } else {
                    $('.vendor_div').hide();
                    $('.program_div').show();
                }
            }
        });
    });

    $("#add_account_form").validate({
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
                }
                else {
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
            }

            else {
                error.insertAfter(element);
            }
        },
        validClass: "validation-valid-label",
        success: function (label) {
            label.addClass("validation-valid-label")
        },
        rules: {
            email: {
                required: true,
                email: true,
            },
        },
        submitHandler: function (form) {
            $('#account_btn_submit').attr('disabled', true);
            form.submit();
        }
    });
</script>
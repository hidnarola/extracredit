<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?php echo base_url(); ?>">

        <title><?php echo $title ?></title>
        <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0;URL=js_disabled">
        </noscript>    
        <!-- Global stylesheets -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
        <link href="assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
        <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="assets/css/core.css" rel="stylesheet" type="text/css">
        <link href="assets/css/components.css" rel="stylesheet" type="text/css">
        <link href="assets/css/colors.css" rel="stylesheet" type="text/css">
        <!-- /global stylesheets -->

        <!-- Core JS files -->
        <script type="text/javascript" src="assets/js/plugins/loaders/pace.min.js"></script>
        <script type="text/javascript" src="assets/js/core/libraries/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/core/libraries/bootstrap.min.js"></script>
        <script type="text/javascript" src="assets/js/plugins/loaders/blockui.min.js"></script>
        <!-- /core JS files -->
        <script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>

        <!-- Theme JS files -->
        <script type="text/javascript" src="assets/js/core/app.js"></script>
        <!-- /theme JS files -->
    </head>

    <body class="login-container login-cover">

        <!-- Page container -->
        <div class="page-container">

            <!-- Page content -->
            <div class="page-content">

                <!-- Main content -->
                <div class="content-wrapper">

                    <!-- Content area -->
                    <div class="content">

                        <!-- Password recovery -->
                        <form action="" method="post" id="forgot_password_form">
                            <div class="panel panel-body login-form">
                                <div class="text-center">
                                    <div class="icon-object border-warning text-warning"><i class="icon-spinner11"></i></div>
                                    <h5 class="content-group">Password recovery <small class="display-block">We'll send you instructions in email</small></h5>
                                </div>

                                <div class="form-group has-feedback">
                                    <input type="text" name="email" class="form-control" placeholder="Your email">
                                    <div class="form-control-feedback">
                                        <i class="icon-mail5 text-muted"></i>
                                    </div>
                                </div>
                                <div class="form-group login-options">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <a href="<?php echo site_url('login') ?>">Back to Login</a>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn bg-blue btn-block" id="reset_password">Reset password <i class="icon-arrow-right14 position-right"></i></button>
                            </div>
                        </form>
                        <!-- /password recovery -->
                    </div>
                    <!-- /content area -->

                </div>
                <!-- /main content -->

            </div>
            <!-- /page content -->

        </div>
        <!-- /page container -->

    </body>
    <script type="text/javascript">
        $("#forgot_password_form").validate({
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
                    email: true,
                    required: true,
                    remote: '<?php echo site_url('login/check_email') ?>'
                }
            },
            messages: {
                email: {
                    remote: jQuery.validator.format("Invalid Email Address")
                }
            },
            submitHandler: function (form) {
                $('#reset_password').attr('disabled', true);
                // do other things for a valid form
                form.submit();
            }
        });

    </script>
</html>

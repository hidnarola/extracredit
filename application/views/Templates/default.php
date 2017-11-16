<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <?php
        $this->load->view('Templates/header');
        ?>
        <script type="text/javascript">
            //-- Set common javascript vairable
            var site_url = "<?php echo site_url() ?>";
            var base_url = "<?php echo base_url() ?>";
        </script>
        <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0;URL=js_disabled">
        </noscript>    
    </head>
    <body>
        <!-- Main navbar -->
        <div class="navbar navbar-inverse">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?php echo site_url('home'); ?>">
                    <!--<img src="assets/images/logo_light.png" alt="">-->
                    Extra Credit Show
                </a>
                <ul class="nav navbar-nav visible-xs-block">
                    <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
                    <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
                </ul>
            </div>
            <div class="navbar-collapse collapse" id="navbar-mobile">
                <ul class="nav navbar-nav">
                    <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown dropdown-user">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <?php if ($this->session->userdata('extracredit_user')['profile_image'] != '') { ?>
                                <img src="<?php echo base_url(USER_IMAGES . $this->session->userdata('extracredit_user')['profile_image']) ?>" alt="">
                            <?php } else { ?>
                                <img src="<?php echo base_url('assets/images/placeholder.jpg') ?>" alt="">
                            <?php } ?>
                            <span><?php echo $this->session->userdata('extracredit_user')['firstname'] . ' ' . $this->session->userdata('extracredit_user')['lastname'] ?></span>
                            <i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="<?php echo site_url('home/profile') ?>"><i class="icon-cog5"></i> Account settings</a></li>
                            <li><a href="<?php echo site_url('logout'); ?>"><i class="icon-switch2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /main navbar -->
        <!-- Page container -->
        <div class="page-container">
            <!-- Page content -->
            <div class="page-content">
                <!-- Main sidebar -->
                <div class="sidebar sidebar-main">
                    <div class="sidebar-content">
                        <!-- User menu -->
                        <div class="sidebar-user">
                            <div class="category-content">
                                <div class="media">
                                    <a href="#" class="media-left">
                                        <?php if ($this->session->userdata('extracredit_user')['profile_image'] != '') { ?>
                                            <img src="<?php echo base_url(USER_IMAGES . $this->session->userdata('extracredit_user')['profile_image']) ?>" class="img-circle img-sm" alt="">
                                        <?php } else { ?>
                                            <img src="<?php echo base_url('assets/images/placeholder.jpg') ?>" class="img-circle img-sm" alt="">
                                        <?php } ?>
                                    </a>
                                    <div class="media-body">
                                        <span class="media-heading text-semibold">Extra Credit</span>
                                        <div class="text-size-mini text-muted">
                                            <i class="icon-user"></i> &nbsp;<?php echo ($this->session->userdata('extracredit_user')['role'] == 'admin') ? 'admin' : 'staff'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /user menu -->
                        <!-- Main navigation -->
                        <div class="sidebar-category sidebar-category-visible">
                            <div class="category-content no-padding">
                                <ul class="navigation navigation-main navigation-accordion">
                                    <li class="<?php echo ($this->controller == 'home') ? 'active' : ''; ?>"><a href="<?php echo site_url('home'); ?>"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
                                    <?php if (checkPrivileges('donors', 'view', 1)) { ?>
                                        <li class="<?php echo ($this->controller == 'donors') ? 'active' : ''; ?>"><a href="<?php echo site_url('donors'); ?>"><i class="icon-coins"></i> <span>Donors</span></a></li>
                                    <?php } ?>
                                    <?php if (checkPrivileges('guests', 'view', 1)) { ?>
                                        <li class="<?php echo ($this->controller == 'guests') ? 'active' : ''; ?>"><a href="<?php echo site_url('guests'); ?>"><i class="icon-people"></i> <span>Guests</span></a></li>
                                    <?php } ?>
                                    <?php if (checkPrivileges('accounts', 'view', 1)) { ?>
                                        <li class="<?php echo ($this->controller == 'accounts') ? 'active' : ''; ?>"><a href="<?php echo site_url('accounts'); ?>"><i class="icon-calculator3"></i> <span>Accounts</span></a></li>
                                    <?php } ?>
                                    <?php if (checkPrivileges('admin_fund', 'view', 1) || (checkPrivileges('accounts_fund', 'view', 1)) || (checkPrivileges('donors_fund', 'view', 1)) || (checkPrivileges('payments_fund', 'view', 1))) { ?>
                                        <li class="<?php echo ($this->controller == 'funds') ? 'active' : ''; ?>">
                                            <a href="#" class="has-ul"><i class="icon-cash4"></i><span>Funds</span></a>
                                            <ul class="hidden-ul" style="<?php echo ($this->controller == 'funds' && in_array($this->action, array('admin_fund', 'accounts', 'donors', 'payments', 'transactions'))) ? 'display: block;' : ''; ?>">
                                                <?php if (checkPrivileges('admin_fund', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'funds' && ($this->action == 'admin_fund')) ? 'active' : ''; ?>"><a href="<?php echo site_url('funds/admin_fund'); ?>">Admin Fund</a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('account_fund', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'funds' && ($this->action == 'accounts' || $this->action == 'transactions') ) ? 'active' : ''; ?>" ><a href="<?php echo site_url('funds/accounts'); ?>">Accounts</a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('donor_fund', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'funds' && ($this->action == 'donors')) ? 'active' : ''; ?>" ><a href="<?php echo site_url('funds/donors'); ?>">Donors</a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('payment_fund', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'funds' && ($this->action == 'payments')) ? 'active' : ''; ?>" ><a href="<?php echo site_url('funds/payments'); ?>">Payments</a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <?php if (checkPrivileges('payments', 'view', 1)) { ?>
                                        <li class="<?php echo ($this->controller == 'payments') ? 'active' : ''; ?>"><a href="<?php echo site_url('payments'); ?>"><i class=" icon-credit-card"></i> <span>Payments</span></a></li>
                                    <?php } ?>
                                    <?php if (checkPrivileges('donor_report', 'view', 1) || (checkPrivileges('guest_report', 'view', 1)) || (checkPrivileges('account_report', 'view', 1)) || (checkPrivileges('amc_balance_report', 'view', 1)) || (checkPrivileges('payments_made_report', 'view', 1))) { ?>
                                        <li class="<?php echo ($this->controller == 'reports') ? 'active' : ''; ?>">
                                            <a href="#" class="has-ul"><i class="icon-graph"></i><span>Reports</span></a>
                                            <ul class="hidden-ul" style="<?php echo ($this->controller == 'reports' || in_array($this->action, array('donors_report'))) ? 'display: block;' : ''; ?>">
                                                <?php if (checkPrivileges('donor_report', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'reports' && ($this->action == 'donors_report')) ? 'active' : ''; ?>"><a href="<?php echo site_url('reports/donors_report'); ?>"><span>Ultimate Donor Report</span></a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('guest_report', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'reports' && ($this->action == 'guests_report')) ? 'active' : ''; ?>"><a href="<?php echo site_url('reports/guests_report'); ?>"><span>Ultimate Guest Reports</span></a></li>
                                                <?php } ?>
                                                    <?php if (checkPrivileges('amc_balance_report', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'reports' && ($this->action == 'amc_balance_report')) ? 'active' : ''; ?>"><a href="<?php echo site_url('reports/amc_balance_report'); ?>"><span>Ultimate Program Report</span></a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('payments_made_report', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'reports' && ($this->action == 'payments_made_report')) ? 'active' : ''; ?>"><a href="<?php echo site_url('reports/payments_made_report'); ?>"><span>Ultimate Awards Report</span></a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('account_report', 'view', 1)) { ?>
                                                    <!--<li class="<?php echo ($this->controller == 'reports') && (in_array($this->action, array('programs_amc_report', 'awards_report', 'vendor_admin_report'))) ? 'active' : ''; ?>">-->
<!--                                                        <a href="#"><span>Accounts Reports</span></a>
                                                        <ul class="hidden-ul" style="<?php echo (in_array($this->action, array('programs_amc_report', 'awards_report', 'vendor_admin_report'))) ? 'display: block;' : ''; ?>">-->
                                                            <!--<li class="<?php echo ($this->controller == 'reports' && ($this->action == 'programs_amc_report')) ? 'active' : ''; ?>"><a href="<?php echo site_url('reports/programs_amc_report'); ?>"><span>Ultimate Program Report</span></a></li>-->
                                                            <!--<li class="<?php echo ($this->controller == 'reports' && ($this->action == 'awards_report')) ? 'active' : ''; ?>"><a href="<?php echo site_url('reports/awards_report'); ?>"><span>Ultimate Awards Report</span></a></li>-->
                                                            <li class="<?php echo ($this->controller == 'reports' && ($this->action == 'vendor_admin_report')) ? 'active' : ''; ?>"><a href="<?php echo site_url('reports/vendor_admin_report'); ?>"><span>Ultimate Vendors/Admin Report</span></a></li>
                                                        <!--</ul>-->
                                                    <!--</li>-->
                                                <?php } ?>
                                                
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <?php if (checkPrivileges('users', 'view', 1) || (checkPrivileges('donation_split_settings', 'view', 1)) || (checkPrivileges('fund_types', 'view', 1)) || (checkPrivileges('payment_types', 'view', 1)) || (checkPrivileges('program_types', 'view', 1)) || (checkPrivileges('program_status', 'view', 1))) { ?>
                                        <li class="<?php echo ($this->controller == 'settings') ? 'active' : ''; ?>">
                                            <a href="#" class="has-ul"><i class="icon-gear"></i><span>Settings</span></a>
                                            <ul class="hidden-ul" style="<?php echo ($this->controller == 'settings' || $this->controller == 'users' || in_array($this->action, array('fund_types', 'payment_types', 'program_types', 'program_status'))) ? 'display: block;' : ''; ?>">
                                                <?php if ($this->session->userdata('extracredit_user')['role'] == 'admin') { ?>
                                                    <li class="<?php echo ($this->controller == 'users') ? 'active' : ''; ?>"><a href="<?php echo site_url('users'); ?>"><span>Users</span><span class="label bg-warning-400"><?php echo $this->total_users; ?></span></a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('donation_split_settings', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'settings' && ($this->action == 'index')) ? 'active' : ''; ?>"><a href="<?php echo site_url('settings'); ?>">Donation Split Settings</a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('fund_types', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'settings' && ($this->action == 'fund_types')) ? 'active' : ''; ?>" ><a href="<?php echo site_url('settings/fund_types'); ?>">Fund/Account Types</a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('payment_types', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'settings' && ($this->action == 'payment_types')) ? 'active' : ''; ?>" ><a href="<?php echo site_url('settings/payment_types'); ?>">Payment Types</a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('program_types', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'settings' && ($this->action == 'program_types')) ? 'active' : ''; ?>" ><a href="<?php echo site_url('settings/program_types'); ?>">Program/AMC Types</a></li>
                                                <?php } ?>
                                                <?php if (checkPrivileges('program_status', 'view', 1)) { ?>
                                                    <li class="<?php echo ($this->controller == 'settings' && ($this->action == 'program_status')) ? 'active' : ''; ?>" ><a href="<?php echo site_url('settings/program_status'); ?>">Program/AMC Stauts</a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <li class=""><a href="<?php echo site_url('logout') ?>"><i class="icon-switch2"></i> <span>Logout</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /main navigation -->
                    </div>
                </div>
                <!-- /main sidebar -->
                <!-- Main content -->
                <div class="content-wrapper">
                    <!-- Page header -->
                    <?php echo $body; ?>
                </div>
                <!-- /main content -->
            </div>
            <!-- /page content -->
        </div>
        <!-- /page container -->
    </body>
</html>

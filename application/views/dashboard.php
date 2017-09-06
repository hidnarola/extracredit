<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Dashboard</h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href=""><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ul>
    </div>
</div>
<style>.dashboard_layout .dash-icon{margin-top: -5px;}</style>
<div class="content">
    <div class="row dashboard_layout">
        <?php if ($this->session->userdata('extracredit_user')['role'] == 'admin') { ?>

            <div class="col-lg-3">
                <!-- Total Users -->
                <div class="panel bg-indigo-400">
                    <div class="panel-body">
                        <div class="heading-elements icon-dasboard">
                            <div class="icon-object border-white text-white dash-icon"><i class="icon-users4"></i></div>
                        </div>
                        <a href="<?php echo site_url('users') ?>" style="color: white">
                            <h3 class="no-margin"><?php echo $users; ?></h3>
                            Users
                        </a>
                    </div>
                </div>
                <!-- /Total Users -->
            </div>
        <?php } ?>

        <div class="col-lg-3">
            <!-- Total Products -->
            <div class="panel bg-pink-400">
                <div class="panel-body">
                    <div class="heading-elements icon-dasboard">
                        <div class="icon-object border-white text-white dash-icon"><i class="icon-grid6"></i></div>
                    </div>
                    <a href="<?php echo site_url('accounts') ?>"  style="color: white">
                        <h3 class="no-margin"><?php echo $accounts; ?></h3>
                        Accounts
                    </a>
                </div>
            </div>
            <!-- /Total Products -->
        </div>

        <div class="col-lg-3">
            <!-- Total Feedbacks -->
            <div class="panel bg-slate-400">
                <div class="panel-body">
                    <div class="heading-elements icon-dasboard">
                        <div class="icon-object border-white text-white dash-icon"><i class="icon-coins"></i></div>
                    </div>
                    <a href="<?php echo site_url('donors') ?>"  style="color: white">
                        <h3 class="no-margin"><?php echo $donors; ?></h3>
                        Donors
                    </a>
                </div>
            </div>
            <!-- /Total Feedbacks -->
        </div>
        <div class="col-lg-3">
            <!-- Total Feedbacks -->
            <div class="panel bg-warning-400">
                <div class="panel-body">
                    <div class="heading-elements icon-dasboard">
                        <div class="icon-object border-white text-white dash-icon"><i class="icon-people"></i></div>
                    </div>
                    <a href="<?php echo site_url('guests') ?>"  style="color: white">
                        <h3 class="no-margin"><?php echo $guests; ?></h3>
                        Guests
                    </a>
                </div>
            </div>
            <!-- /Total Feedbacks -->
        </div>
    </div>
    <div class="row">               
        <div class="col-lg-4">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Admin Fund</h6>
                    <div class="heading-elements">
                        <span class="heading-text"><i class="icon-history text-warning position-left"></i> <?php echo date('d F, Y');?></span>                       
                    </div>
                    <a class="heading-elements-toggle"><i class="icon-more"></i></a></div>

                <!-- Numbers -->
                <div class="container-fluid">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-clipboard3 position-left text-slate"></i> <?php echo $today_admin_fund['total'] ?></h6>
                                <span class="text-muted text-size-small">Today</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-calendar3 position-left text-slate"></i> <?php echo $week_admin_fund['total'] ?></h6>
                                <span class="text-muted text-size-small">This week</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-cash3 position-left text-slate"></i> <?php echo $total_admin_fund['total'] ?></h6>
                                <span class="text-muted text-size-small">Total Funds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /numbers -->
            </div>
        </div>
    </div>

</div>
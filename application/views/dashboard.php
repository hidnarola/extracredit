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
<div class="content">
    <div class="row">
        <?php if ($this->session->userdata('extracredit_user')['role'] == 'admin') { ?>
            <div class="col-lg-3">
                <!-- Total Users -->
                <div class="panel bg-teal-400">
                    <div class="panel-body">
                        <a href="<?php echo site_url('users') ?>" style="color: white">
                            <h3 class="no-margin"><?php echo $users; ?></h3>
                            <i class="icon-users4"></i> Users
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
                    <a href="<?php echo site_url('accounts') ?>"  style="color: white">
                        <h3 class="no-margin"><?php echo $accounts; ?></h3>
                        <i class="icon-grid6"></i> Accounts
                    </a>
                </div>
            </div>
            <!-- /Total Products -->
        </div>

        <div class="col-lg-3">
            <!-- Total Feedbacks -->
            <div class="panel bg-blue-400">
                <div class="panel-body">
                    <a href="<?php echo site_url('donors') ?>"  style="color: white">
                        <h3 class="no-margin"><?php echo $donors; ?></h3>
                        <i class="icon-grid2"></i> Donors
                    </a>
                </div>
            </div>
            <!-- /Total Feedbacks -->
        </div>
        <div class="col-lg-3">
            <!-- Total Feedbacks -->
            <div class="panel bg-indigo-400">
                <div class="panel-body">
                    <a href="<?php echo site_url('guests') ?>"  style="color: white">
                        <h3 class="no-margin"><?php echo $guests; ?></h3>
                        <i class="icon-grid2"></i> Guests
                    </a>
                </div>
            </div>
            <!-- /Total Feedbacks -->
        </div>
    </div>
</div>
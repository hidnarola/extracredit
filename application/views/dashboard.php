<?php
if (isset($json)) {
    echo "<script> var data=" . $json . "</script>";
} else {
    echo "<script> var data=''</script>";
}
?>
<?php
$get_date = $this->input->get('date');
$start_date = '';
$end_date = '';
if ($get_date != '') {
    $dates = explode('-', $get_date);
    $start_date = date('F j, Y', strtotime(@$dates[0]));
    $end_date = date('F j, Y', strtotime(@$dates[1]));
}
?>
<script type="text/javascript" src="<?php echo base_url('assets/js/plugins/ui/moment/moment.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/plugins/pickers/daterangepicker.js') ?>"></script>
<script type="text/javascript">
    DEFAULT_ADMIN_JS_PATH = base_url + 'assets/js/';
</script>
<script type="text/javascript" src="<?php echo base_url('assets/js/plugins/visualization/echarts/echarts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/charts/echarts/dashboard_chart.js') ?>"></script>
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
        <div class="col-sm-6 col-md-3">
            <div class="panel panel-body bg-teal-400 has-bg-image">
            <!--<div class="panel panel-body bg-blue-400 has-bg-image">-->
                <div class="media no-margin">
                    <div class="media-body">
                        <h3 class="no-margin"><?php echo $donors; ?></h3>
                        <span class="text-uppercase text-size-mini">Donors</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="icon-coins icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <!--<div class="panel panel-body bg-danger-400 has-bg-image">-->
            <div class="panel panel-body bg-pink-400 has-bg-image">
                <div class="media no-margin">
                    <div class="media-body">
                        <h3 class="no-margin"><?php echo $guests; ?></h3>
                        <span class="text-uppercase text-size-mini">Guests</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="icon-people icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <!--<div class="panel panel-body bg-success-400 has-bg-image">-->
            <div class="panel panel-body bg-slate-400 has-bg-image">
                <div class="media no-margin">
                    <div class="media-body">
                        <h3 class="no-margin"><?php echo $accounts; ?></h3>
                        <span class="text-uppercase text-size-mini">Accounts</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="icon-calculator3 icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="panel panel-body bg-indigo-400 has-bg-image">
                <div class="media no-margin">
                    <div class="media-body">
                        <h3 class="no-margin"><?php echo $payments; ?></h3>
                        <span class="text-uppercase text-size-mini">Payments</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="icon-credit-card icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">               
        <!-- Admin Fund -->
        <div class="col-lg-5 col-lg-offset-1">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Admin Fund</h6>
                    <div class="heading-elements">
                        <span class="heading-text"><i class="icon-history text-warning position-left"></i> <?php echo date('d F, Y'); ?></span>                       
                    </div>
                    <a class="heading-elements-toggle"><i class="icon-more"></i></a></div>

                <!-- Numbers -->
                <div class="container-fluid">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-clipboard3 position-left text-slate"></i> <?php echo ($today_admin_fund != '' && $today_admin_fund > 0) ? '$' . round($today_admin_fund, 2) : 0 ?></h6>
                                <span class="text-muted text-size-small">Today</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-calendar3 position-left text-slate"></i> <?php echo ($week_admin_fund != '' && $week_admin_fund > 0) ? '$' . round($week_admin_fund, 2) : 0 ?></h6>
                                <span class="text-muted text-size-small">This week</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-cash3 position-left text-slate"></i> <?php echo '$' . round($this->admin_fund, 2); ?></h6>
                                <span class="text-muted text-size-small">Total Fund</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /numbers -->
            </div>
        </div>
        <!-- Account Fund -->
        <div class="col-lg-5">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Accounts Fund</h6>
                    <div class="heading-elements">
                        <span class="heading-text"><i class="icon-history text-warning position-left"></i> <?php echo date('d F, Y'); ?></span>                       
                    </div>
                    <a class="heading-elements-toggle"><i class="icon-more"></i></a></div>

                <!-- Numbers -->
                <div class="container-fluid">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-clipboard3 position-left text-slate"></i> <?php echo ($today_account_fund != '' && $today_account_fund > 0) ? '$' . round($today_account_fund, 2) : 0 ?></h6>
                                <span class="text-muted text-size-small">Today</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-calendar3 position-left text-slate"></i> <?php echo ($week_account_fund != '' && $week_account_fund > 0) ? '$' . round($week_account_fund, 2) : 0 ?></h6>
                                <span class="text-muted text-size-small">This week</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-group">
                                <h6 class="text-semibold no-margin"><i class="icon-cash3 position-left text-slate"></i> <?php echo '$' . round($total_account_fund, 2); ?></h6>
                                <span class="text-muted text-size-small">Total Fund</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /numbers -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group cst_date col-lg-3 col-offset-lg-9">
            <div class="date_div">
                <button type="button" class="btn bg-slate-400 daterange-ranges" id="date_range_pick">
                    <i class="icon-calendar22 position-left"></i><span>Select Date Range to filter data</span><b class="caret"></b>
                </button>
            </div>
        </div>
    </div>
    <div class="panel panel-flat">
        <div class="panel-body">
            <div class="chart-container">
                <div class="chart has-fixed-height" id="line_point"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var get = '<?php echo $this->input->get('date') ?>';
        if (get != "") {
            var res = get.split('-');
            start = res[0];
            end = res[1];

            $("#date_range_pick").daterangepicker({
                startDate: start,
                endDate: end,
//                maxDate: moment(),
//                opens: 'left',
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
            },
                    function (start, end) {
                        $('.daterange-ranges span').html(start.format('MMMM D, YYYY') + ' &nbsp; - &nbsp; ' + end.format('MMMM D, YYYY'));
                    }
            );
            $('#date_range_pick span').html('<?php echo $start_date ?>' + ' &nbsp; - &nbsp; ' + '<?php echo $end_date ?>');

        } else {
            //var start = moment().subtract(6, 'days');
            //var end = moment();
            $("#date_range_pick").daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                autoUpdateInput: false,
//                maxDate: moment(),
//                opens: 'left',
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
            },
                    function (start, end) {
                        $('.daterange-ranges span').html(moment().startOf('month').format('MMMM D, YYYY') + ' &nbsp; - &nbsp; ' + moment().endOf('month').format('MMMM D, YYYY'));
                    }
            );
            $('#date_range_pick span').html(moment().startOf('month').format('MMMM D, YYYY') + ' &nbsp; - &nbsp; ' + moment().endOf('month').format('MMMM D, YYYY'));

        }

        $('#date_range_pick').on('apply.daterangepicker', function (ev, picker) {
            var url = window.location.href;
            var newurl = updateQueryStringParameter(url, "date", picker.startDate.format('MM/DD/YYYY') + '-' + picker.endDate.format('MM/DD/YYYY'));
            $('#date_range_pick span').html(picker.startDate.format('MMMM D, YYYY') + ' &nbsp; - &nbsp; ' + picker.endDate.format('MMMM D, YYYY'));
            window.location.href = newurl;
        });
        $('#date_range_pick').on('cancel.daterangepicker', function (ev, picker) {
            if ($('#date_range_pick span').html() != '') {
                var url = window.location.href;
                var newurl = updateQueryStringParameter(url, "date", '');
                window.location.href = newurl;
            }
            $('#date_range_pick span').html('');
        });

        $('#date_range_pick').on('cancel.daterangepicker', function (ev, picker) {
            $('date_range_pick span').html('');
        });

    });

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
</script>
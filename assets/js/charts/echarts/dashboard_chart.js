/* ------------------------------------------------------------------------------
 *
 *  # Echarts - lines and areas
 *
 *  Lines and areas chart configurations
 *
 *  Version: 1.0
 *  Latest update: August 1, 2015
 *
 * ---------------------------------------------------------------------------- */

$(function () {


    // Set paths
    // ------------------------------
    require.config({
        paths: {
            echarts: DEFAULT_ADMIN_JS_PATH + 'plugins/visualization/echarts'
        }
    });


    // Configuration
    // ------------------------------
    require(
            [
                'echarts',
                'echarts/theme/limitless',
                'echarts/chart/bar',
                'echarts/chart/line'
            ],
            // Charts setup
                    function (ec, limitless) {

                        // Initialize charts
                        // ------------------------------

                        var line_point = ec.init(document.getElementById('line_point'), limitless);

                        //
                        // Line and point options
                        //
                        if (data != "") {
                            Xindex = [], Yindex1 = [], Yindex2 = [];
                            $.each(data.key_array, function (i, v) {
                                Xindex.push(v[1]);
                            });

                            $.each(data.key_array, function (i, v) {
                                if (data.donors[v[0]] != undefined) {
                                    var obj = data.donors[v[0]];
                                    Yindex1.push(obj[0]);
                                } else {
                                    Yindex1.push(0);
                                }
                            });
                            $.each(data.key_array, function (i, v) {
                                if (data.incoming_money[v[0]] != undefined) {
                                    var obj = data.incoming_money[v[0]];
                                    Yindex2.push(obj[0]);
                                } else {
                                    Yindex2.push(0);
                                }
                            });
                        }
                        line_point_options = {
                            // Setup grid
//                            grid: {
//                                x: 50,
//                                y: 60,
//                            },
                            // Add tooltip
                            tooltip: {
                                trigger: 'axis',
                                axisPointer: {
                                    show: true,
                                    type: 'cross',
                                    lineStyle: {
                                        type: 'dashed',
                                        width: 1
                                    }
                                },
//                                formatter: function (params) {
//                                    return params[0].name + '<br/>'
//                                            + params[0].seriesName + ': ' + params[0].value + '<br/>'
//                                            + params[1].seriesName + ': ' + params[1].value + '<br/>'
//                                            + params[2].seriesName + ': ' + params[2].value + '<br/>'
//                                            + params[3].seriesName + ': ' + params[3].value + '<br/>'
//                                            + params[4].seriesName + ': ' + params[4].value + '<br/>'
//                                            + params[5].seriesName + ': ' + params[5].value + '<br/>'
//                                            + params[6].seriesName + ': ' + params[6].value + '<br/>';
//                                }
                            },
                            // Add legend
                            legend: {
                                x: 'left',
                                data: ['Number of Donors', 'Incoming Money']
                            },
                            // Display toolbox
                            toolbox: {
                                show: true,
                                feature: {
                                    dataView: {
                                        show: true,
                                        readOnly: false,
                                        title: 'View data',
                                        lang: ['View chart data', 'Close', 'Update']
                                    },
                                    magicType: {
                                        show: true,
                                        title: {
                                            line: 'Switch to line chart',
                                            bar: 'Switch to bar chart',
                                        },
                                        type: ['line', 'bar']
                                    },
                                    restore: {
                                        show: true,
                                        title: 'Restore'
                                    },
                                    saveAsImage: {
                                        show: true,
                                        title: 'Same as image',
                                        lang: ['Save']
                                    }
                                }
                            },
                            // Horizontal axis
                            xAxis: [{
                                    type: 'category',
                                    boundaryGap: false,
                                    data: Xindex
                                }],
                            // Vertical axis
                            yAxis: [
                                {
                                    type: 'value',
                                    axisLine: {
                                        lineStyle: {
                                            color: '#dc143c'
                                        }
                                    }
                                }
                            ],
                            // Add series
                            series: [
                                {
                                    name: 'Number of Donors',
                                    type: 'line',
                                    data: Yindex1,
                                },
                                {
                                    name: 'Incoming Money',
                                    type: 'line',
                                    data: Yindex2,
                                },
                            ]
                        };

                        // Apply options
                        // ------------------------------

                        line_point.setOption(line_point_options);

                        // Resize charts
                        // ------------------------------

                        window.onresize = function () {
                            setTimeout(function () {
                                line_point.resize();
                            }, 200);
                        }
                    }
            );
        });

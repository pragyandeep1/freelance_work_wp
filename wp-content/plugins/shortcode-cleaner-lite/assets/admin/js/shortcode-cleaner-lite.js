/**
 * Shortcode Cleaner Lite
 * Clean your WordPress content from unused broken shortcodes.
 *
 * @package   Shortcode_Cleaner_Lite
 * @author    Jozoor, mohamdio [jozoor.com]
 * @link      https://plugins.jozoor.com/shortcode-cleaner
 * @copyright 2017 Jozoor, mohamdio [jozoor.com]
 * @license   GPL2
 * @version   1.0.0
 *
 * @since  1.0.0
 */

/**
 * Common chartjs options
 */

Chart.defaults.global.tooltips.displayColors = false;
Chart.defaults.global.tooltips.cornerRadius = 2;
Chart.defaults.scale.scaleLabel.display = true;
Chart.defaults.scale.scaleLabel.fontSize = 14;
Chart.defaults.scale.ticks.padding = 13;
Chart.defaults.scale.ticks.fontSize = 13;
Chart.defaults.scale.gridLines.display = true;
Chart.defaults.global.elements.line.tension = 0;
Chart.defaults.global.elements.line.fill = false;
Chart.defaults.global.elements.line.spanGaps = false;
Chart.defaults.global.elements.line.borderWidth = 3;
Chart.defaults.global.elements.line.borderCapStyle = 'butt';
Chart.defaults.global.elements.point.hitRadius = 3;
Chart.defaults.global.elements.point.radius = 3;
Chart.defaults.global.elements.point.pointStyle = 'circle';
Chart.defaults.global.elements.point.hoverRadius = 3;
Chart.defaults.global.legend.display = false;

;(function($, window, document, undefined) {

    $(document).ready(function() {

        // save selectors
        var $cs_sections = $('.cs-sections'),
            $cs_header = $('.cs-header'),
            $nav_sections = $('.cs-nav'),
            $dashboard_section = $cs_sections.find('#cs-tab-dashboard'),
            $system_section = $cs_sections.find('#cs-tab-system_info'),
            $support_section = $cs_sections.find('#cs-tab-support'),
            $feedback_section = $cs_sections.find('#cs-tab-feedback');

        var $cleaner_dashboard = $('#shortcode-cleaner-dashboard');

        // if dashboard section active? do actions
        if ($dashboard_section.is(':visible')) {
            $cs_sections.removeClass('normal');
            $cs_header.find('fieldset input').hide();
            $cs_header.find('fieldset a').show();
        } else if ($system_section.is(':visible')) {
            $cs_sections.addClass('normal');
            $cs_header.find('fieldset input').hide();
            $cs_header.find('fieldset a').show();
        } else if ($support_section.is(':visible')) {
            $cs_sections.addClass('normal');
            $cs_header.find('fieldset input').hide();
            $cs_header.find('fieldset a').show();
        } else if ($feedback_section.is(':visible')) {
            $cs_sections.addClass('normal');
            $cs_header.find('fieldset input').hide();
            $cs_header.find('fieldset a').show();
        } else {
            $cs_sections.addClass('normal');
            $cs_header.find('fieldset a').hide();
            $cs_header.find('fieldset input').show();
        }
        
        $nav_sections.find('ul a').on('click', function(e) {
            e.preventDefault();
            if ($dashboard_section.is(':visible')) {
                $cs_sections.removeClass('normal');
                $cs_header.find('fieldset input').hide();
                $cs_header.find('fieldset a').show();
            } else if ($system_section.is(':visible')) {
                $cs_sections.addClass('normal');
                $cs_header.find('fieldset input').hide();
                $cs_header.find('fieldset a').show();
            } else if ($support_section.is(':visible')) {
                $cs_sections.addClass('normal');
                $cs_header.find('fieldset input').hide();
                $cs_header.find('fieldset a').show();
            } else if ($feedback_section.is(':visible')) {
                $cs_sections.addClass('normal');
                $cs_header.find('fieldset input').hide();
                $cs_header.find('fieldset a').show();
            } else {
                $cs_sections.addClass('normal');
                $cs_header.find('fieldset a').hide();
                $cs_header.find('fieldset input').show();
            }
        });

        /**
         * cleaner history data chartjs
         */
        var $chart_id = $($cleaner_dashboard.find('#sc-cleaner-data'));

        // chart data selectors
        var days_data_chart_elem = $($cleaner_dashboard.find('#sc_days_data'));
        var days_data_chart_val = $.parseJSON(days_data_chart_elem.val());

        var numbers_data_chart_elem = $($cleaner_dashboard.find('#sc_numbers_data'));
        var numbers_data_chart_val = $.parseJSON(numbers_data_chart_elem.val());

        var set_chart_min = Math.min.apply(this, numbers_data_chart_val) - 5;
        if (set_chart_min < 5) {
            set_chart_min = 0;
        }
        if (set_chart_min % 5 !== 0) {
            set_chart_min = set_chart_min - (set_chart_min % 5);
        }

        var set_chart_max = Math.max.apply(this, numbers_data_chart_val) + 5;

        var yAxes_label_chart_elem = $('#yAxes_label');
        var yAxes_label_chart_val = yAxes_label_chart_elem.val();

        var xAxes_label_chart_elem = $('#xAxes_label');
        var xAxes_label_chart_val = xAxes_label_chart_elem.val();

        var chart_options = {
            responsive: true,
            defaultFontColor: '#9E9E9E',
            defaultFontFamily: "'Poppins', 'sans-serif'",
            defaultFontSize: 14,
            maintainAspectRatio: false,
            elements: {
                line: {
                    tension: 0,
                    fill: false,
                    spanGaps: false,
                    borderWidth: 2,
                    borderCapStyle: 'butt',
                    borderColor: '#F5F5F5',
                }
            },
            scales: {
                xAxes: [{
                    barPercentage: 0.5,
                    maxBarThickness: 30,
                    scaleLabel: {
                        labelString: xAxes_label_chart_val,
                        fontColor: '#bdbdbd',
                        fontFamily: "'Poppins', 'sans-serif'",
                        fontSize: 11.5,
                    },
                    ticks: {
                        padding: 12,
                        fontSize: 12,
                        fontColor: '#9E9E9E',
                        fontFamily: "'Poppins', 'sans-serif'",
                    },
                    gridLines: {
                        display: false,
                        color: '#F5F5F5',
                        zeroLineWidth: 1,
                        zeroLineColor: '#F5F5F5',
                        drawTicks: false
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        labelString: yAxes_label_chart_val,
                        fontColor: '#bdbdbd',
                        fontFamily: "'Poppins', 'sans-serif'",
                        fontSize: 9.5
                    },
                    ticks: {
                        padding: 12,
                        fontSize: 13,
                        fontColor: '#9E9E9E',
                        stepSize: 5,
                        fontFamily: "'Poppins', 'sans-serif'",
                        min: set_chart_min,
                        //max: set_chart_max,
                    },
                    gridLines: {
                        drawBorder: false,
                        color: '#F5F5F5',
                        zeroLineWidth: 1,
                        zeroLineColor: '#F5F5F5',
                        drawTicks: false
                    }
                }]
            },
            tooltips: {
                enabled: true,
                yPadding: 5,
                xPadding: 6,
                displayColors: false,
                cornerRadius: 2,
                backgroundColor: 'rgba(73,73,73,0.9)',
                //titleFontStyle: 'normal', bodyFontStyle: 'bold',
                // titleFontFamily: "'Poppins', 'sans-serif'",
                // bodyFontFamily: "'Poppins', 'sans-serif'",
                titleFontSize: 11,
                bodyFontSize: 11,
                callbacks: {
                    title: function(tooltipItem, chart) {
                        return tooltipItem[0].xLabel + ' ' + xAxes_label_chart_val;
                    }
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 0, // 50
                    right: 0, // 25
                    left: -6 // 25
                }
            },
            hover: {
                animationDuration: 0
            },
        };

        var cleanerChart = new Chart($chart_id, {
            type: 'bar',
            data: {
                labels: days_data_chart_val,
                datasets: [{
                    label: yAxes_label_chart_val,
                    backgroundColor: "#74AD27",
                    borderColor: "#74AD27",
                    pointBorderColor: "#74AD27",
                    pointBackgroundColor: "#fff",
                    pointHoverBackgroundColor: "#74AD27",
                    pointHoverBorderColor: "#74AD27",
                    data: numbers_data_chart_val
                }]
            },
            options: chart_options
        });

        /**
         * progressbar
         */

        var $broken_shortcodes = $($cleaner_dashboard.find('.broken-shortcodes'));

        var bar = new ProgressBar.Circle('#scan-process', {
            strokeWidth: 4,
            trailWidth: 4,
            easing: 'easeInOut', // easeInOut
            duration: 2500,
            color: '#74AD27',
            trailColor: '#F1F1F1',
            text: {
                autoStyleContainer: false
            },
            // Set default step function for all animate calls
            step: function(state, circle) {

                var value = Math.round(circle.value() * 100) + ' %';
                if (value === '0 %') {
                    if ($cleaner_dashboard.hasClass('sc-run')) {
                        $cleaner_dashboard.find('.sc-data-loading').show();
                    }
                    circle.setText('');
                } else if (value === '100 %') {

                    circle.setText('<i class="fas fa-check" style="font-size:50px"></i>');

                    if ($cleaner_dashboard.hasClass('sc-run')) {

                        $cleaner_dashboard.removeClass('sc-run');
                        $cleaner_dashboard.find('.status-data-number').hide();
                        $cleaner_dashboard.find('.sc-broken-shortcodes').hide();
                        $cleaner_dashboard.find('.cleaner-history-data').hide();
                        $cleaner_dashboard.find('.sc-data-loading').hide();
                        $cleaner_dashboard.find('.status-data-number').fadeIn('slow');
                        $cleaner_dashboard.find('.cleaner-history-data').fadeIn('slow');
                        // we don't have broken shortcodes? hide it
                        if ($broken_shortcodes.hasClass('none') !== true) {
                            setTimeout(function() {
                                $cleaner_dashboard.find('.sc-broken-shortcodes').fadeIn('slow');
                            }, 700);
                        }
                    }

                } else {
                    circle.setText(value);
                }

            }
        });

        bar.text.style.fontFamily = '"Poppins", sans-serif';
        // bar.text.style.fontSize = '34px';

        // refresh scan
        var $refresh_scan = $cleaner_dashboard.find('.sc-refresh-data');
        $refresh_scan.on('click', function(e) {
            e.preventDefault();
            $cleaner_dashboard.find('.status-data-number').hide();
            $cleaner_dashboard.find('.sc-broken-shortcodes').hide();
            $cleaner_dashboard.find('.cleaner-history-data').hide();
            $cleaner_dashboard.addClass('sc-run');
            bar.set(0);
            bar.animate(1.0);
        });

        // we have cached status data? don't run ProgressBar
        if ($cleaner_dashboard.hasClass('sc-run') !== true) {
            bar.set(1.0); // this for final 100% without animate
        }
        // else run ProgressBar if we have class 'sc-run'
        bar.animate(1.0); // Number from 0.0 to 1.0

        /**
         * show broken tag details
         */
        var $show_details = $cleaner_dashboard.find('.sc-show-details');

        $show_details.on('click', function(e) {
            e.preventDefault();

            var shortcode_id = $(this).data('id');
            var shortcode_name = $(this).data('name');

            $('#sc-broken-shortcode-tag').dialog({
                title: '[' + shortcode_name + ']',
                draggable: false,
                width: 900, // 850
                height: 'auto', // 700
                modal: true,
                resizable: false,
                closeOnEscape: true,
                position: {
                    my: 'center',
                    at: 'center',
                    of: window
                },
                open: function() {
                    var data = $(this).find('.load-shortcode-details');
                    data.append($('#' + shortcode_id).html());

                    // set viewpoint
                    $(window).on('resize', function() {
                        var height = $(window).height(),
                            set_height = Math.floor(height - 125);
                        $('#sc-broken-shortcode-tag').dialog('option', 'height', set_height).parent().css('max-height', set_height);
                        $('#sc-broken-shortcode-tag').css('overflow', 'auto');
                    }).resize();

                },
                close: function(e, ui) {
                    var data = $(this).find('.load-shortcode-details');
                    data.html('');
                }
            });

        });

        /**
         * filter broken shortcodes by name
         */
        $("#search-broken-shortcode input").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#broken-shortcodes-list .sc-tag-item").filter(function() {
                $(this).parent().parent().toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

    });

})(jQuery, window, document);
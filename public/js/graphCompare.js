Highcharts.chart('graph', {

    chart: {
        height: 600,
        type: 'spline',
        zoomType: 'x'
    },

    title: {
        text: 'Compare index 8848Invest'
    },

    yAxis: {
        title: {
            text: 'Change'
        },
        labels: {
            formatter: function () {
                return this.value + '%';
            }
        }
    },

    xAxis: {
        categories: dates
    },

    tooltip: {
        shared: true,
        valueSuffix: '%',
        crosshairs: true
    },

    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        spline: {
            lineWidth: 1,
            states: {
                hover: {
                    lineWidth: 2
                }
            },
            marker: {
                enabled: false
            }
        }
    },

    series: [{
        name: 'Index8848',
        data: indexes
    }, {
        name: 'Bitcoin',
        data: prcBtc
    }, {
        name: 'Ethereum',
        data: prcEth,
        visible:false
    }, {
        name: 'XRP',
        data: prcXrp,
        visible:false
    }, {
        name: 'Litecoin',
        data: prcLtc,
        visible:false
    }, {
        name: 'EOS',
        data: prcEos,
        visible:false
    }],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});
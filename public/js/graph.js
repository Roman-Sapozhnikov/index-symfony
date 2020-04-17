Highcharts.chart('graph', {
    chart: {
        height: 600,
        type: 'spline',
        zoomType: 'x'
    },
    title: {
        text: 'Index 8848TOP10'
    },
    xAxis: {
        categories: dates
    },
    yAxis: {
        title: {
            text: 'Index 8848'
        }
    },
    navigation: {
        buttonOptions: {
            enabled: false
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: false
            },
            enableMouseTracking: true
        }
    },
    series: [{
        name: '8848TOP10',
        data: indexes
    }]
});
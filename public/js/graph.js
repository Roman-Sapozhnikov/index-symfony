Highcharts.chart('container', {
    chart: {
        type: 'line'
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
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    series: [{
        name: '8848TOP10',
        data: indexes
    }]
});
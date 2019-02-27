define(['jquery',
    'core/chartjs'], function ($, Chartjs) {

    return {
        init: function (data, labels) {
            console.log(data, labels);

            let processed_data = {labels: labels, datasets: []};

            for (let id in data) {
                processed_data.datasets.push(
                    {
                        hidden: true,
                        fill: false,
                        label: id,
                        data: data[id]
                    });
            }

            let ctx = document.getElementById('report_usage_chart').getContext('2d');
            let chart = new Chartjs(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: processed_data,

                // Configuration options go here
                options: {}
            });
        }
    };
});
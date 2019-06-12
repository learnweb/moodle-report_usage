// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A javascript module to create a chartjs line diagram
 *
 * @package    report_usage
 * @copyright  2019 Justus Dieckmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/chartjs', 'report_usage/color'],
    function($, Chartjs, Colors) {

        var colors;
        var courseid;

        /**
         * Processes the data and configurates Datasets.
         * Dataset visibility will be restored from the last time, new datasets will default to be hidden.
         * @param {array} data Array of Datasets
         * @param {array} names Array of names, indexed by id of activity
         * @returns {Array} the datasets configured for chartjs
         */
        function processData(data, names) {
            var datasets = [];

            var visibility = JSON.parse(window.localStorage.getItem('report_usage_visibility_' + courseid));

            for (var id in data) {
                var hidden = true;
                if (visibility !== null && visibility[id] === false) {
                    hidden = false;
                }
                datasets.push(
                    {
                        objid: id,
                        fill: false,
                        label: names[id],
                        hidden: hidden,
                        data: data[id],
                        borderColor: colors[datasets.length % colors.length]
                    });
            }
            return datasets;
        }

        /**
         * Updates the localStorage with the current dataset visibilities.
         * @param {object} chartjs the Chartjs-Object
         */
        function updateLocalStorage(chartjs) {
            var data = chartjs.config.data.datasets;
            var visibility = {};

            for (var i in data) {
                var dataline = data[i];
                visibility[dataline.objid] = !chartjs.isDatasetVisible(i);
            }
            window.localStorage.setItem('report_usage_visibility_' + courseid, JSON.stringify(visibility));
        }

        /**
         * Function to initialize Chart and pass data
         * @param {array} data Array of Datasets
         * @param {array} labels Array of labels for x-Axis
         * @param {array} names Array of names, indexed by id of activity
         * @param {int} cid the courseid
         */
        function init(data, labels, names, cid) {
            colors = Colors.createColors();
            courseid = cid;

            var canvas = $('#report_usage_chart');
            var ctx = canvas.get(0).getContext('2d');
            var chartjs = new Chartjs(ctx, {
                // The type of chart we want to create.
                type: 'line',
                // The data for our dataset.
                data: {
                    labels: labels,
                    datasets: processData(data, names)
                },
                // Configuration options go here.
                options: {
                    onClick: function() {
                        setTimeout(updateLocalStorage, 200, chartjs);
                    },
                    maintainAspectRatio: false
                }
            });
            // Make chart area (without legend) 400px high.
            canvas.height = chartjs.legend.bottom + 400;
            canvas.css('height', canvas.height + 'px');
            chartjs.resize();
        }

        return {
            init: init
        };
    });
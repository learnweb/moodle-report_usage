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

        /**
         * Processes the data and configurates Datasets
         * @param {array} data Array of Datasets
         * @param {array} names Array of names, indexed by id of activity
         * @returns {Array} the datasets configured for chartjs
         */
        function processData(data, names) {
            var datasets = [];

            for (var id in data) {
                datasets.push(
                    {
                        fill: false,
                        label: names[id],
                        hidden: true,
                        data: data[id],
                        borderColor: colors[datasets.length % colors.length]
                    });
            }
            return datasets;
        }

        /**
         * Function to initialize Chart and pass data
         * @param {array} data Array of Datasets
         * @param {array} labels Array of labels for x-Axis
         * @param {array} names Array of names, indexed by id of activity
         */
        function init(data, labels, names) {
            colors = Colors.createColors();

            var ctx = document.getElementById('report_usage_chart').getContext('2d');
            new Chartjs(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: labels,
                    datasets: processData(data, names)
                },
                // Configuration options go here
                options: {}
            });
        }

        return {
            init: init
        };
    });
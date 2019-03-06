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
define(['jquery', 'core/chartjs'],
    function ($, Chartjs) {

        var colors = [];

        function hsvToRgb(h, s, v) {
            var r, g, b, i, f, p, q, t;
            i = Math.floor(h * 6);
            f = h * 6 - i;
            p = v * (1 - s);
            q = v * (1 - f * s);
            t = v * (1 - (1 - f) * s);
            switch (i % 6) {
                case 0:
                    r = v;
                    g = t;
                    b = p;
                    break;
                case 1:
                    r = q;
                    g = v;
                    b = p;
                    break;
                case 2:
                    r = p;
                    g = v;
                    b = t;
                    break;
                case 3:
                    r = p;
                    g = q;
                    b = v;
                    break;
                case 4:
                    r = t;
                    g = p;
                    b = v;
                    break;
                case 5:
                    r = v;
                    g = p;
                    b = q;
                    break;
            }
            return {
                r: Math.round(r * 255),
                g: Math.round(g * 255),
                b: Math.round(b * 255)
            };
        }

        function componentToHex(c) {
            var hex = c.toString(16);
            return hex.length == 1 ? "0" + hex : hex;
        }

        function hsvToHex(h, s, v) {
            var rgb = hsvToRgb(h, s, v);
            return "#" + componentToHex(rgb.r) + componentToHex(rgb.g) + componentToHex(rgb.b);
        }

        function createColors() {
            for (var i = 0; i < 360; i += 35) {
                colors.push(hsvToHex(i / 360, 1, 0.95));
            }
            for (var i1 = 0; i1 < 360; i1 += 35) {
                colors.push(hsvToHex(i1 / 360, 0.6, 0.95));
            }
            for (var i2 = 0; i2 < 360; i2 += 35) {
                colors.push(hsvToHex(i2 / 360, 1, 0.65));
            }
            for (var i3 = 0; i3 < 360; i3 += 35) {
                colors.push(hsvToHex(i3 / 360, 1, 0.4));
            }
        }

        function processData(data, labels) {
            var processedData = {labels: labels, datasets: []};

            for (var id in data) {
                processedData.datasets.push(
                    {
                        fill: false,
                        label: id,
                        data: data[id],
                        borderColor: colors[processedData.datasets.length % colors.length]
                    });
            }
            return processedData;
        }

        function init(data, labels) {

            createColors();
            var processedData = processData(data, labels);

            var ctx = document.getElementById('report_usage_chart').getContext('2d');
            var chart = new Chartjs(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: processedData,

                // Configuration options go here
                options: {}
            });
        };

        return {
            init: init
        };
    });
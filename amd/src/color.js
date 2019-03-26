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
 * Javascript color helper
 *
 * @package    report_usage
 * @copyright  2019 Justus Dieckmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([],
    function() {

        /**
         *
         * @param {number} h hue bewteen 0 and 1
         * @param {number} s saturation between 0 and 1
         * @param  {number} v value bewteen 0 and 1
         * @returns {{r: number, b: number, g: number}}
         */
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

        /**
         * Converts byte into two digit hex-string
         * @param {int} c
         * @returns {string}
         */
        function componentToHex(c) {
            var hex = c.toString(16);
            return hex.length === 1 ? "0" + hex : hex;
        }

        /**
         * Converts from hsv-space to an html-hex color string
         * @param {number} h hue bewteen 0 and 1
         * @param {number} s saturation between 0 and 1
         * @param {number} v value bewteen 0 and 1
         * @returns {string} html hex color string
         */
        function hsvToHex(h, s, v) {
            var rgb = hsvToRgb(h, s, v);
            return "#" + componentToHex(rgb.r) + componentToHex(rgb.g) + componentToHex(rgb.b);
        }

        /**
         * Creates an Array of Color to use for the Diagram
         * @returns {Array} Array of colors
         */
        function createColors() {
            var colors = [];
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
            return colors;
        }

        return {
            createColors: createColors
        };

    });

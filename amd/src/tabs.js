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
 * A javascript module to modify the url and form data to stay on same tab on reload
 *
 * @package    report_usage
 * @copyright  2019 Justus Dieckmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'],
    function($) {

        /**
         * Function to initialize listeners
         */
        function init() {
            $('.nav-tabs a').on('shown.bs.tab', function(event){
                var id = $(event.target)[0].id;
                // Updates the moodle form, so that the tab will be the same when form is submitted.
                $('input[name="tab"]')[0].value = id;

                // Updates the URL, so that the tab will be the same when page is reloaded.
                var url = new URL(window.location.href);
                url.searchParams.set('tab', id);
                window.history.replaceState({}, "", location.pathname + url.search);
            });
        }

        return {
            init: init
        };
    });
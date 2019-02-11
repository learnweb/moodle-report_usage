<?php
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
 *
 *
 * @package    report_activity_analysis
 * @copyright  2019 Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_activity_analysis\eventhandling;

defined('MOODLE_INTERNAL') || die();

class register_events
{

    public static function get_event_observers()
    {
        $observers = [];

        $pluginman = \core_plugin_manager::instance();
        foreach ($pluginman->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                $name = $plugin->type . "_" . $plugin->name;
                switch ($name) {
                    case "mod_resource":
                    case "mod_chat":
                        $observers[] = array(
                            "eventname" => "\\$name\\event\\course_module_viewed",
                            "callback" => "\\report_activity_analysis\\eventhandling\\handler::handle"
                        );
                        break;
                }
            }
        }

        return $observers;
    }

}
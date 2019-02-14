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
 * Activity analysis renderer
 *
 * @package    report_usage
 * @copyright  2019 Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base
{

    /**
     * Render activity analysis report page
     *
     * @param report_usage_renderable $renderable the element to render
     */
    protected function render_report_usage(report_usage_renderable $renderable) {
        global $DB;
        echo \html_writer::start_tag("table");

        foreach ($renderable->data as $k => $v) {
            $context = \context::instance_by_id($v->id, IGNORE_MISSING);

            echo \html_writer::start_tag("tr");
            echo \html_writer::tag("td", $v->id);
            echo \html_writer::tag("td", $context->get_context_name(false));
            echo \html_writer::tag("td", $v->amount);
            echo \html_writer::end_tag("tr");
        }

        echo \html_writer::end_tag("table");
    }

}
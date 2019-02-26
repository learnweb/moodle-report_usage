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

class renderer extends \plugin_renderer_base {

    /**
     * Render activity analysis report page
     *
     * @param report_usage_renderable $renderable the element to render
     */
    protected function render_report_usage(report_usage_renderable $renderable) {
        $days = $renderable->days;

        $dt = new \DateTime($days . "days ago");

        echo \html_writer::start_tag("table", array('class' => 'table table-sm table-hover'));
        echo \html_writer::start_tag('thead');
        echo \html_writer::start_tag("tr");

        echo \html_writer::tag("th", get_string('file', 'report_usage'));
        for ($i = 0; $i < $days; $i++) {
            $dt->add(new \DateInterval("P1D"));
            echo \html_writer::tag("th", $dt->format("d.m"));
        }

        echo \html_writer::end_tag("tr");
        echo \html_writer::end_tag('thead');
        echo \html_writer::start_tag('tbody');
        list($data, $max) = $renderable->get_data();
        foreach ($data as $k => $v) {
            $context = \context::instance_by_id($k, IGNORE_MISSING);
            echo \html_writer::start_tag("tr");
            echo \html_writer::tag("td", $context->get_context_name(false));
            for ($i = 0; $i < $days; $i++) {
                $amount = isset($v[$i]) ? $v[$i]->amount : 0;
                $percentage = $amount / $max;
                echo \html_writer::tag("td", $amount,
                        array('style' => 'background-color:' . $this->get_color_by_percentage($percentage)));
            }
            echo \html_writer::end_tag("tr");
        }
        echo \html_writer::end_tag("tbody");
        echo \html_writer::end_tag("table");
    }

    protected function get_color_by_percentage($per) {
        $r = 255;
        $g = $b = 255 - intval($per * 215);

        $str = "#";
        $str .= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
        $str .= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
        $str .= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
        return $str;
    }

    public function render_report_usage_chart(report_usage_chart_renderable $renderable) {
        global $OUTPUT;

        $chart = new \core\chart_line();
        $data = $renderable->get_data();
        foreach ($data as $k => $v) {
            $name = \context::instance_by_id($k, IGNORE_MISSING)->get_context_name(false);
            $series = new \core\chart_series($name, array_values($v));
            $chart->add_series($series);
        }
        $chart->set_labels($renderable->create_labels());

        echo $OUTPUT->render($chart);
    }

}
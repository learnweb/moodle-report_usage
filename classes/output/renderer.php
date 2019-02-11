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
 * @package    report_activity_analysis
 * @copyright  2019 Justus Dieckmann <justusdieckmann@wwu.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_activity_analysis\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base
{

    /**
     * Render activity analysis report page
     *
     * @param report_activity_analysis_renderable $renderable the element to render
     */
    protected function render_report_activity_analysis(report_activity_analysis_renderable $renderable) {
        global $DB;

        var_dump($renderable);
    }

}
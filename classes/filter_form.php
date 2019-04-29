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
 * Form to filter the usage report
 *
 * @package   report_usage
 * @copyright 2019 Justus Dieckmann
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_usage;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir.'/formslib.php');

/**
 * Class filter_form form to filter the results by date
 * @package report_outline
 */
class filter_form extends \moodleform {

    /**
     * Form definition
     * @throws \HTML_QuickForm_Error
     * @throws \coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'tab');
        $mform->setType('tab', PARAM_ALPHANUMEXT);
        $mform->setDefault('tab', 'table-tab');

        $opts = array(
                'optional' => false,
                'startyear' => $this->_customdata['startyear'],
                'stopyear' => $this->_customdata['stopyear']
        );
        $mform->addElement('date_selector', 'startdate', get_string('from'), $opts);
        $mform->addElement('date_selector', 'enddate', get_string('to'), $opts);

        // Add the filter/cancel buttons (without 'closeHeaderBefore', so they collapse with the filter).
        $buttonarray = [
            $mform->createElement('submit', 'submitbutton', get_string('filter')),
            $mform->createElement('cancel'),
        ];
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
    }

    /**
     * Expand the form contents if the filter is in use.
     * @throws \HTML_QuickForm_Error
     */
    public function definition_after_data() {
        $mform = $this->_form;
        $startdate = $mform->getElement('startdate')->getValue();
        $enddate = $mform->getElement('enddate')->getValue();
        if (!empty($startdate['enabled']) || !empty($enddate['enabled'])) {
            $mform->setExpanded('filterheader', true);
        }
    }

    public function validation($data, $files) {
        // End date should be later than the start date.
        if ($data['enddate'] < $data['startdate']) {
            return array('enddate' => get_string('error_endbeforestart', 'report_usage'));
        }
        return array();
    }
}

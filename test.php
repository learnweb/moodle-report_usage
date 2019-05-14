<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

var_dump(\report_usage\db_helper::get_data_from_course(2, 25, array(1, 5), '20190500', '20200000'));
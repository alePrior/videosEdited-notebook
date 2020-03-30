<?php
/**
 * Format the passed date string
 */
function setDateFormat ($string = "") {
    $formatted = strtotime($string);
    return date("d M Y", $formatted);
}
<?php
function filter_tuners($tuners) {
    $filtered = [];

    foreach ($tuners as $key => $value) {
        if (is_string($value)) {
            $filtered[$key] = $value;
        } else {
            $filtered[$key] = filter_tuners($value);

            if (empty($filtered[$key])) unset($filtered[$key]);
        }
    }

    return (object)$filtered;
}
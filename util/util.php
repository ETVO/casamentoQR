<?php

define('GUESTS_STORAGE', BASE_DIR . '/guests.json');

function get_data_from_dir($dir)
{
    $data_json = file_get_contents($dir);
    if (!$data_json) return null;
    return json_decode($data_json, true);
}

function is_valid_md5($md5 = '')
{
    return preg_match('/^[a-f0-9]{32}$/', $md5);
}

function get_filter_query($filter_key, $query_array, $values)
{
    if (!is_array($values))
        return false;

    $current_value = $query_array[$filter_key];
    $index = array_search($current_value, $values);

    $new_value = $values[0];

    if ($index !== false && $index != count($values) - 1) {
        $new_value = $values[$index + 1];
    }

    $query_array[$filter_key] = $new_value;

    return '?' . http_build_query($query_array);
}

function get_guest_data($id = null)
{
    if (!$id)
        return false;

    $id = filter_var(trim($id));

    $stored_guests = json_decode(file_get_contents(GUESTS_STORAGE), true) ?? [];

    // push to file
    if (!empty($id) && $guest = get_guest_by_id($id, $stored_guests)) {
        return $guest;
    } else {
        return false;
    }
}

function guest_exists($id, $stored_guests)
{
    if ($stored_guests)
        foreach ($stored_guests as $guest) {
            if ($id == $guest['id']) return true;
        }
    return false;
}

function filter_guests_that_checked_in($bool, $stored_guests, $checkin_guests)
{
    if (!$stored_guests || !$checkin_guests)
        return false;

    $filtered_guests = [];
    foreach ($stored_guests as $guest) {
        if (guest_exists($guest['id'], $checkin_guests) == $bool)
            array_push($filtered_guests, $guest);
    }
    return $filtered_guests;
}

function order_guests_by_nome($stored_guests, $order = 'AZ')
{
    // Define the custom sorting function

    if ($order == 'AZ') {
        $sortFunction = function ($a, $b) {
            return strcmp($a['nome'], $b['nome']);
        };
    } else {
        $sortFunction = function ($a, $b) {
            return strcmp($b['nome'], $a['nome']);
        };
    }

    // Use usort to sort the guests array using the custom sorting function
    usort($stored_guests, $sortFunction);

    return $stored_guests;
}



function get_guest_by_id($id, $stored_guests)
{
    if ($stored_guests)
        foreach ($stored_guests as $guest) {
            if ($id == $guest['id']) return $guest;
        }
    return false;
}

function filter_guests_key_value($filter_key, $filter_value, $type, $stored_guests)
{
    if (!$stored_guests || !isset($filter_key) || !isset($filter_value))
        return false;

    $filtered_guests = [];
    foreach ($stored_guests as $guest) {
        if ($type == 'search') {
            if (stripos($guest[$filter_key], $filter_value) !== false) {
                array_push($filtered_guests, $guest);
            }
        } else if ($type == 'bool') {
            if (!$filter_value && (!isset($guest[$filter_key]) || !$guest[$filter_key]))
                array_push($filtered_guests, $guest);
            else if ($filter_value && isset($guest[$filter_key]) && $guest[$filter_key])
                array_push($filtered_guests, $guest);
        }
    }
    return $filtered_guests;
}

function filter_guests($search_term, $stored_guests)
{
    if (!$stored_guests)
        return false;

    if (strpos($search_term, ':')) {
        $terms = explode(':', $search_term);
        $search_key = $terms[0];
        $search_value = $terms[1];

        return filter_guests_key_value($search_key, $search_value, 'search', $stored_guests);
    }

    $filtered_guests = [];
    foreach ($stored_guests as $guest) {
        foreach ($guest as $key => $value) {
            if (stripos($value, $search_term) !== false) {
                array_push($filtered_guests, $guest);
                break;
            }
        }
    }
    return $filtered_guests;
}

function replace_guest_by_id($id, $new_guest, &$stored_guests)
{
    if ($stored_guests)
        foreach ($stored_guests as &$guest) {
            if ($id == $guest['id']) {
                $guest = $new_guest;
                return true;
            }
        }
    return false;
}

function remove_guest_by_id($id, &$stored_guests)
{
    if ($stored_guests)
        foreach ($stored_guests as $key => $guest) {
            if ($id == $guest['id']) {
                unset($stored_guests[$key]);
                return true;
            }
        }
    return false;
}

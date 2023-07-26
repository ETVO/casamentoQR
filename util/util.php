<?php

define('GUESTS_STORAGE', BASE_DIR . '/guests.json');

function get_data_from_dir($dir)
{
    $data_json = file_get_contents($dir);
    if (!$data_json) return null;
    return json_decode($data_json, true);
}

function is_valid_md5($md5 ='')
{
    return preg_match('/^[a-f0-9]{32}$/', $md5);
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

function get_guest_by_id($id, $stored_guests)
{
    if ($stored_guests)
        foreach ($stored_guests as $guest) {
            if ($id == $guest['id']) return $guest;
        }
    return false;
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
<?php

/*
 * Copyright (C) 2015 ioerror
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Status: 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/app/config.php';
require __DIR__ . '/app/Json.php';
require __DIR__ . '/app/Mailer.php';

$json = new \Mailer\Json();
$result = $json->readData(array_merge($_POST, $TO_ADDRESS_NAME));
if ($result) {
    $json->process();
}

echo $json->getStatusAndMessages();

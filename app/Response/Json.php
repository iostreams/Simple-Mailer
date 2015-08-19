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

namespace Mailer\Response;

/**
 * Description of JSON
 *
 * @author ioerror
 */
final class Json extends \Mailer\Response\Response {

    /**
     *
     * @return string JSON
     */
    public function getOutput() {
        if (empty($this->errors)) {
            $result = ['status' => 'ok', 'messages' => ['Wiadomość została wysłana']];
        } else {
            $result = ['status' => 'error', 'messages' => $this->errors, 'errorsIn' => $this->errorsIn];
        }
        return json_encode($result);
    }

}

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

namespace Mailer;

/**
 * Description of JSON
 *
 * @author ioerror
 */
class Json {

    /**
     * Mailer object
     * @var Mailer
     */
    protected $mailer;

    /**
     * Array of error messages
     * @var array
     */
    protected $errors = [];

    /**
     * Array with the names of incorrect fields.
     * @var array
     */
    protected $errorsIn = [];

    /**
     *
     */
    public function __construct() {
        $this->mailer = new \Mailer\Mailer();
    }

    /**
     *
     * @param array $data
     * @return boolean TRUE on success or FALSE on failure
     */
    public function readData($data) {
        $result = FALSE;
        if (empty($data)) {
            $this->errors[] = 'Błąd aplikacji: Brak danych';
        } elseif (!is_array($data)) {
            $this->errors[] = 'Błąd aplikacji: Błędny format danych';
        } elseif (empty($data['toAddress'])) {
            $this->errors[] = 'Błąd aplikacji: Nie ustawiono odbiorcy wiadomości';
        } elseif (empty($data['message'])) {
            $this->errors[] = 'Wpisz treść wiadomości';
            $this->errorsIn[] = 'message';
        } else {

            $toName = (empty($data['toName'])) ? '' : $data['toName'];
            if (empty($data['toAddress'])) {
                $result = FALSE;
                $this->errors[] = 'Błąd aplikacji: Brak adresu e-mail odbiorcy wiadomości';
            } else {
                $result = $this->mailer->addToAddress($data['toAddress'], $toName);

                if (!$result) {
                    $this->errors[] = 'Błąd aplikacji: Błędny adres e-mail odbiorcy wiadomości';
                }
            }

            if ($result) {
                $subject = (empty($data['subject'])) ? '' : $data['subject'];
                $this->mailer->setMailContent($subject, $data['message']);

                $name = (empty($data['name'])) ? '' : $data['name'];
            }

            if ($result && !empty($data['email'])) {
                $result = $this->mailer->addReplyToAddress($data['email'], $name);

                if (!$result) {
                    $this->errors[] = 'Błędny adres e-mail';
                    $this->errorsIn[] = 'email';
                }
            }
        }

        return $result;
    }

    /**
     *
     * @return boolean TRUE on success or FALSE on failure
     */
    public function process() {
        $result = $this->mailer->sendMail();
        if (!$result) {
            $this->errors[] = 'Błąd wysyłania maila';
        }

        return $result;
    }

    /**
     *
     * @return string JSON
     */
    public function getStatusAndMessages() {
        if (empty($this->errors)) {
            $result = ['status' => 'ok', 'messages' => ['Wiadomość została wysłana']];
        } else {
            $result = ['status' => 'error', 'messages' => $this->errors, 'errorsIn' => $this->errorsIn];
        }
        return json_encode($result);
    }

}

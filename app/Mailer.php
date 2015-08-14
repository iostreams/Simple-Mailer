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
 * Simple mailer
 * @author ioerror
 */
class Mailer {

    /**
     *
     */
    const DEFAULT_HEADERS = ['MIME-Version' => '1.0', 'X-Mailer' => 'PHP Mailer', 'Content-type' => 'text/plain; charset="utf-8"', 'Content-Transfer-Encoding' => '8bit'];

    /**
     * Mail subject
     * @var type string
     * @access protected
     */
    protected $subject = '';

    /**
     * Mail message
     * @var type string
     * @access protected
     */
    protected $message = '';

    /**
     *
     * @var type string
     * @access protected
     */
    protected $toAddress = '';

    /**
     *
     * @var type string
     * @access protected
     */
    protected $toName = '';

    /**
     *
     * @var type string
     * @access protected
     */
    protected $replyToAddress = '';

    /**
     *
     * @var type string
     * @access protected
     */
    protected $replyToName = '';

    /**
     * Mail headers
     * @var type array
     * @access private
     */
    private $headers = self::DEFAULT_HEADERS;

    /**
     * Remove line breaks and strip whitespace
     * @param string $str
     * @return string
     */
    protected function rmLineBreak($str) {
        return \trim(str_replace(array("\r", "\n"), '', $str));
    }

    /**
     *
     * @todo Description
     * @param string $text UTF-8 string
     * @return string
     */
    private function quotedPrintableEncode($text) {
        $quotedPrintable = quoted_printable_encode($text);
        if ($text === $quotedPrintable) {
            return $text;
        } else {
            return '=?UTF-8?Q?' . $quotedPrintable . '?=';
        }
    }

    /**
     *
     * @param string $header
     * @param string $value
     * @return boolean TRUE on success or FALSE on failure
     */
    public function setHeader($header, $value) {
        $header = $this->rmLineBreak($header);
        $value = $this->rmLineBreak($value);

        $result = !empty($header) && !empty($value);

        if ($result) {
            $this->headers[$header] = $this->rmLineBreak($value);
        }

        return $result;
    }

    /**
     *
     * @return string Mail headers string
     */
    protected function getHeadersString() {
        $headersString = '';
        foreach ($this->headers as $header => $value) {
            $headersString .= $header . ': ' . $value . "\r\n";
        }

        if (!empty($this->replyToAddress)) {
            $headersString .= 'Reply-To: ' . $this->getAddressString('Reply-To') . "\r\n";
        }

        return $headersString;
    }

    /**
     *
     * @param string $subject
     * @param string $message
     */
    public function setMailContent($subject, $message) {
        $this->subject = $this->rmLineBreak($subject);
        $this->message = wordwrap($message, 70, "\r\n");
    }

    /**
     *
     * @param string $type
     * @param string $address
     * @param string $name
     * @return boolean
     * @access protected
     */
    protected function setAddress($type, $address, $name = '') {

        $address = \trim($address);
        $result = (boolean) \filter_var($address, \FILTER_VALIDATE_EMAIL);

        if ($result) {
            $name = \trim(str_replace(array("\r", "\n"), '', $name));


            switch ($type) {
                case 'To':
                    $this->toAddress = $address;
                    $this->toName = $name;
                    break;

                case 'Reply-To':
                    $this->replyToAddress = $address;
                    $this->replyToName = $name;
                    break;

                default:
                    $result = FALSE;
                    break;
            }
        }

        return $result;
    }

    /**
     *
     * @param type $type
     * @return string Name <email@example.com>
     */
    protected function getAddressString($type) {
        $addressString = '';

        switch ($type) {
            case 'To':
                $address = $this->toAddress;
                $name = $this->toName;
                break;

            case 'Reply-To':
                $address = $this->replyToAddress;
                $name = $this->replyToName;
                break;

            default:
//                $addressString = FALSE;
                break;
        }

        if (!empty($address)) {
            if (empty($name)) {
                $addressString = $address;
            } else {
                $addressString = $this->quotedPrintableEncode($name) . ' <' . $address . '>';
            }
        }

        return $addressString;
    }

    /**
     * Add a "To" address.
     * @param string $address
     * @param string $name
     * @return boolean TRUE on success or FALSE on failure
     */
    public function addToAddress($address, $name = '') {
        return $this->setAddress('To', $address, $name);
    }

    /**
     * Add a "Reply-To" address.
     * @param string $address
     * @param string $name
     * @return boolean TRUE on success or FALSE on failure
     */
    public function addReplyToAddress($address, $name = '') {
        return $this->setAddress('Reply-To', $address, $name);
    }

    /**
     * Clear all data
     */
    protected function clear() {
        $this->subject = '';
        $this->message = '';
        $this->toAddress = '';
        $this->toName = '';
        $this->replyToAddress = '';
        $this->replyToName = '';
        $this->headers = self::DEFAULT_HEADERS;
    }

    /**
     *
     * @return boolean TRUE on success or FALSE on failure
     */
    public function sendMail() {
        return \mail($this->getAddressString('To'), $this->quotedPrintableEncode($this->subject), $this->message, $this->getHeadersString());
//        return (boolean) (rand(0, 1));
    }

}

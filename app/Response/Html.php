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
 * Description of HTML
 *
 * @author ioerror
 */
final class Html extends \Mailer\Response\Response {

    /**
     *
     * @var string
     */
    private $templateCode = '';

    /**
     *
     * @var boolean
     */
    private $templateError = FALSE;

    public function __construct() {
        parent::__construct();
        // (new \ReflectionClass($this))->getShortName() - class name without namespace
        $templateFile = __DIR__ . '/' . (new \ReflectionClass($this))->getShortName() . '.tpl';
        $templateCode = file_get_contents($templateFile);
        if ($templateCode === FALSE) {
            $this->templateCode = 'Błąd odczytu szablonu strony<br>';
            $this->templateError = TRUE;
        } else {
            $this->templateCode = $templateCode;
        }
    }

    /**
     *
     * @param string $tag
     * @param string $value
     * @param boolean $valIsMsg
     */
    private function replaceTag($tag, $value, $valIsMsg = FALSE) {
        if (!$this->templateError) {
            $this->templateCode = str_replace('[@' . $tag . ']', $value, $this->templateCode);
        } elseif ($valIsMsg) {
            $this->templateCode .= $value . '<br>';
        }
    }

    /**
     *
     * @return string HTML
     */
    public function getOutput() {
        if (empty($this->errors)) {
            $this->replaceTag('alertType', 'success');
            $this->replaceTag('alertTxt', 'Wiadomość została wysłana', TRUE);
        } else {
            $this->replaceTag('alertType', 'danger');
            $this->replaceTag('alertTxt', implode('<br>', $this->errors), TRUE);
        }

        return $this->templateCode;
    }

}

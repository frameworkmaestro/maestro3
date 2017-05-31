<?php

/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada 
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL 
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a 
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

class MAction {

    public static function isAction($string) {
        if ($string == '') {
            return false;
        }
        $modifier = $string{0};
        if (strpos('>@!:+^=', $modifier) === false) {
            return preg_match("/^(SUBMIT|PRINT|PDF|FILE|REPORT|POST|OPEN(.*)|CLOSE|PROMPT(.*))$/", strtoupper($string));
        }
        return true;
    }

    private static function getHrefAction($href) {
        $app = Manager::getApp();
        $re = '#^(\/?)' . $app . '\/#';
        if (preg_match($re, $href)) {
            $href = preg_replace($re, '', $href);
        }
        return Manager::getURL($href);
    }

    public static function getHref($href) {
        if ($href != '') {
            if ($href{0} == '#') {
                $href = Manager::getStaticURL(Manager::getApp(), substr($href, 1));
            } else {
                $href = MAction::getHrefAction($href);
            }
        }
        return $href;
    }

    public static function getOnClick($action, $name = '', $target = '') {
        $upper = strtoupper($action);
        if (!MAction::isAction($upper)) {
            $action = '>' . $action;
        }
        if ($upper == 'SUBMIT') {
            $onclick = MUI::doPostBack($name);
        } elseif ($upper == 'PRINT') {
            $onclick = MUI::doPrintForm();
        } elseif ($upper == 'REPORT') {
            if ($name != '') {
                $onclick = MUI::doPrintFile($name);
            }
        } elseif ($upper == 'FILE') {
            if ($name != '') {
                $onclick = MUI::doPostBack($name);
            }
        } elseif ($upper == 'PDF') {
            if ($name != '') {
                $onclick = MUI::doShowPDF($name);
            }
        } elseif ($upper == 'POST') {
            $onclick = MUI::doPostBack($name);
        } elseif (substr($upper, 0, 4) == 'OPEN') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                $onclick = MUI::openWindow($id);
            }
        } elseif (substr($upper, 0, 5) == 'CLOSE') {
            if (strpos($action, ';') !== false) {
                list($close, $postAction) = explode(';', $action);
                $onclick = MUI::closeWindow($id) . ';' . MAction::getOnClick($postAction);
            } else {
                $onclick = MUI::closeWindow($id);
            }
        } elseif (substr($upper, 0, 6) == 'PROMPT') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                $onclick = MUI::doPrompt($id);
            }
        } elseif (substr($upper, 0, 4) == 'HELP') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                $onclick = MUI::showHelp($id);
            }
        } elseif ($action{0} == '+') {
            $url = MAction::getHrefAction(substr($action, 1));
            $onclick = MUI::doWindow($url, $target);
        } elseif ($action{0} == '^') {
            $url = MAction::getHrefAction(substr($action, 1));
            $onclick = MUI::doDialog($name, $url);
        } elseif ($upper == 'NONE') {
            return "";
        } elseif (substr($upper, 0, 4) == 'HTTP') {
            $onclick = MUI::doGet($action);
        } elseif (substr($upper, 0, 11) == 'JAVASCRIPT:') {
            $onclick = $action;
        } elseif ($action{0} == '!') {
            $onclick = substr($action, 1);
        } elseif ($action{0} == ':') {
            if (strpos($action, '|') !== false) {
                list($action, $name, $updateElement) = explode('|', $action);
            }
            $url = MAction::getHrefAction(substr($action, 1));
            $onclick = MUI::doAjaxText($url, $name, $updateElement);
        } elseif ($action{0} == '=') {
            $url = MAction::getHrefAction(substr($action, 1));
            $onclick = MUI::doRedirect($url);
        } elseif ($action{0} == '@') {
            $goto = MAction::getHrefAction(substr($action, 1));
            $onclick = MUI::doLinkButton($goto, $name);
        } elseif ($action{0} == '>') {
            if (strpos($action, '|') !== false) {
                list($action, $target) = explode('|', $action);
            }
            $goto = MAction::getHrefAction(substr($action, 1));
            $onclick = MUI::doGet($goto, $target);
        } else {
            $onclick = $action;
        }
        return $onclick;
    }

    /**
     * Used by Maestro 2.0
     * @param $action
     * @return string
     */
    public static function parseAction($action) {
        if ($action == '') {
            return $action;
        }
        $upper = strtoupper($action);
        $modifier = $action{0};
        if (strpos(self::$modifiers, $modifier) !== false) {
            if ($modifier == '!') {
                return $action;
            }
            $goto = self::getAction(substr($action, 1));
            return $modifier . $goto;
        } elseif ($upper == 'POST') {
            return "POST";
        } elseif (substr($upper, 0, 6) == 'PROMPT') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                return "p#" . $id;
            }
            return '';
        } elseif (substr($upper, 0, 4) == 'HELP') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                return "h#" . $id;
            }
            return '';
        } elseif (substr($upper, 0, 6) == 'DIALOG') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                return "d#" . $id;
            }
            return '';
        } elseif (substr($upper, 0, 4) == 'FILE') {
            if (strpos($action, ':') !== false) {
                list($action, $url) = explode(':', $action);
                return "f#" . self::getAction($url);
            }
            return '';
        } else {
            return $action;
        }
    }

    public static function generate($control, $action) {
        $isAction = MAction::isAction($action);
        if ($isAction) {
            if ((!$control->hasEvent('onClick')) && (!$control->hasEvent('click'))) {
                $control->addEvent('click', MAction::getOnClick($action, $control->getId(), $control->target));
            }
            return '';
        } else {
            return MAction::getHref($action);
        }
    }

}

?>
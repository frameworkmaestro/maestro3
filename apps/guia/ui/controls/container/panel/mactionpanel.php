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

class MActionPanel extends MPanel {

    public $iconType = 'normal'; // 32x32

    public function onCreate() {
        parent::onCreate();
        $this->setIconType('normal');
    }

    public function setIconType($type = 'normal') {
        $this->iconType = $type;
    }

    protected function _getControl($label, $image, $actionURL, $target = NULL, $params = array()) {
        $control = new MImageLinkLabelAction('', $label, $actionURL, $image, $this->iconType, $target);
        return $control;
    }

    public function addAction($label, $image, $action, $args = array(), $item = '', $params = array()) {
        $query = http_build_query($args);
        $actionURL = $action . ($query ? '?' . $query : '');
        $control = $this->_getControl($label, $image, $actionURL, NULL, $params);
        $class = 'mPanelCell' . ucfirst($this->iconType);
        $this->add($control, '', 'left', $class);
    }

    public function addLink($label, $image, $link, $target = NULL) {
        $actionURL = ($link instanceof MLink) ? $link->href : $link;
        $control = $this->_getControl($label, $image, $actionURL, $target);
        $class = 'mPanelCell' . ucfirst($this->iconType);
        $this->add($control, '', 'left', $class);
    }

    public function insertAction($pos, $label, $image, $module = 'main', $action = '', $args = array(), $item = '') {
        $actionURL = Manager::getActionURL($module, $action, $item, $args);
        $control = $this->_getControl($label, $image, $actionURL);
        $class = 'mPanelCell' . ucfirst($this->iconType);
        $this->insert($pos, $control, '', 'left', $class);
    }

    public function addUserAction($transaction, $access, $label, $image, $action = '', $args = array(), $item = '', $params = array()) {
        if (Manager::checkAccess($transaction, $access)) {
            $this->addAction($label, $image, $action, $args, $item, $params);
        }
    }

    public function insertUserAction($pos, $transaction, $access, $label, $image, $action = '', $args = array(), $item = '', $params = array()) {
        if (Manager::checkAccess($transaction, $access)) {
            $this->insertAction($pos, $label, $image, $action, $args, $item, $params);
        }
    }

    public function addGroupAction($transaction, $access, $label, $image, $action = '', $args = array(), $item = '', $params = array()) {
        if (Manager::checkAccess($transaction, $access, false, true)) {
            $this->addAction($label, $image, $action, $args, $item, $params);
        }
    }

    public function insertGroupAction($pos, $transaction, $access, $label, $image, $action = '', $args = array(), $item = '', $params = array()) {
        if (Manager::checkAccess($transaction, $access, false, true)) {
            $this->insertAction($pos, $label, $image, $action, $args, $item, $params);
        }
    }

    public function setActions($action, $level = 1) {
        $actions = Manager::getAction($action);
        if ($level == 1) {
            $this->addActions($actions[ACTION_ACTIONS]);
        } else {
            foreach ($actions as $group) {
                $this->addControl(new MSeparator($group[ACTION_CAPTION]));
                $this->addActions($group[ACTION_ACTIONS]);
            }
        }
    }

    private function addActions($actions) {
        foreach ($actions as $index => $action) {
            $transaction = $action[ACTION_TRANSACTION];
            if ($action instanceof MControl) {
                $this->addControl($action);
            } else if ($action == '-') {
                $this->addBreak();
            } else if (!empty($action[ACTION_GROUP])) {
                $this->addGroupActions($transaction, $action[ACTION_ACCESS], $action[ACTION_CAPTION], $action[ACTION_ACTIONS]);
            } else {
                if ($transaction) {
                    $this->addUserAction($transaction, $action[ACTION_ACCESS], $action[ACTION_CAPTION], $action[ACTION_ICON], $action[ACTION_PATH]);
                } else {
                    $this->addAction($action[ACTION_CAPTION], $action[ACTION_ICON], $action[ACTION_PATH]);
                }
            }
        }
    }

    public function addBreak() {
        $this->add(new MSpacer(), '0', 'clear');
    }

    public function addGroupActions($transaction, $access, $label, $actions) {
        if (Manager::checkAccess($transaction, $access)) {
            $this->addBreak();
            $this->addControl(new MLabel($label, '', true));
            $this->addBreak();
            $this->addControl(new MSeparator());
            $this->addActions($actions);
        }
    }
}

?>

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

/**
 * Base class for controls which owns others controls.
 */
class MContainerControl extends MBaseDiv implements IContainer {

    protected $controls;

    public function onCreate() {
        parent::onCreate();
        $this->controls = new MObjectList();
    }

    /**
     * The clone method.
     * It is used to clone controls, avoiding references to same controls.
     */
    public function __clone() {
        parent::__clone();
        $this->controls = clone $this->controls;
    }

    //
    // Controls
    //
    protected function _addControl($control, $pos = 0, $op = 'add') {
        if (is_null($control)) {
            return;
        } elseif (is_array($control)) {
            foreach ($control as $c) {
                $this->_addControl($c);
            }
            return;
        } elseif (!is_object($control)) {
            $control = new MRawControl($control);
        }
        if ($control instanceof MControl) {
            if ($control->getAjax() == null) {
                $control->setAjax($this->getAjax());
            }
            if ($op == 'add') {
                $this->controls->add($control);
            } elseif ($op == 'ins') {
                $this->controls->insert($control, $pos);
            } elseif ($op == 'set') {
                $this->controls->set($pos, $control);
            }
            $control->owner = $this;
        } else {
            throw new EControlException(_M('Using non-control with MContainerControl::_addControl: ' . $control));
        }
    }

    public function addControl($control, $pos = NULL) {
        $this->_addControl($control, $pos);
    }

    public function insertControl($control, $pos = 0) {
        $this->_addControl($control, $pos, 'ins');
    }

    public function setControl($control, $pos = 0) {
        $this->_addControl($control, $pos, 'set');
    }

    public function setControls($controls) {
        if (is_array($controls)) {
            $this->clearControls();
            foreach ($controls as $c) {
                $this->addControl($c);
            }
        } else {
            $this->addControl($controls);
        }
    }

    public function getControls() {
        return $this->controls->items;
    }

    public function getControl($pos) {
        return $this->controls->get($pos);
    }

    public function findControlById($id) {
        $k = NULL;
        $controls = $this->controls->items;
        foreach ($controls as $control) {
            if ($control->id == $id) {
                return $control;
            } elseif ($control instanceof MContainerControl) {
                if (($k = $control->findControlById($id)) != NULL) {
                    break;
                }
            }
        }
        return $k;
    }

    public function clearControls() {
        $this->controls->clear();
    }

    public function hasItems() {
        return $this->controls->hasItems();
    }

    public function generateInner() {
        if ($this->controls->hasItems()) {
            $this->inner = $this->controls->items;
        }
    }

}

?>
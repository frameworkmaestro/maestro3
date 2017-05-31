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

/*
  A container for elements where each one can be a MOptionGroup or a MOption
 */

class MListControl extends MContainerControl {

    protected $showValues;
    protected $type;
    protected $options;

    public function init($id = '', $options = '', $showValues = false) {
        parent::init($id);
        if ($options) {
            $this->setControls($options);
        }
        $this->showValues = $showValues;
    }

    public function onCreate() {
        parent::onCreate();
        $this->showValues = false;
        $this->type = 'filter';
    }

    public function setShowValues($show) {
        if (!is_bool($show)) {
            throw new EControlException(_M($this->className . ':: setShowValues() - This method expects an boolean as parameter!'));
        }
        $this->showValues = $show;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($value) {
        $this->type = $value;
    }

    function setOptions($options) {
        $this->options = $options;
        $controls = array();
        foreach (array_keys($options) as $k) {
            $o = $options[$k];
            if ($o instanceof MOptionGroup) {
                $this->type = 'group';
                $controls[$k] = $o;
            } elseif ($o instanceof MOption) {
                $controls[$k] = $o;
            } elseif (is_array($o)) { // optiongroup
                $this->type = 'group';
                $optionGroup = new MOptionGroup($k, $k);
                $optionGroup->setOptions($o);
                $controls[$k] = $optionGroup;
            } else { // option
                $controls[$k] = new MOption(trim($k), trim($k), $o);
            }
        }
        $this->setControls($controls);
    }

    function getOptions() {
        return $this->options;
    }

    function getOption($value) {
        $options = $this->getControls();
        foreach ($options as $o) {
            if ($o instanceof MOptionGroup) {
                if ($oo = $o->getOption($value)) {
                    return $oo->getLabel();
                }
            } elseif ($o instanceof MOption) {
                if (trim($value) == trim($o->getValue()))
                    return $o->getLabel();
            }
        }
        return NULL;
    }

    public function generateOptions($value = '') {
        $content = '';
        $options = $this->getControls();

        foreach ($options as $o) {
            $o->setShowValues($this->showValues);
            if ($o instanceof MOptionGroup) {
                $content .= $o->generateOptions($value);
            } else {
                $o->check($value);
                $content .= $o->generate();
            }
        }
        return $content;
    }

}

?>
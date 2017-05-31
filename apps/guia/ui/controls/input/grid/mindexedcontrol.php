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

class MIndexedControl extends MInputControl {

    public $index;
    public $container;

    public function __construct($name = '', $label = '', $controls = NULL) {
        $this->container = new MVContainer($name);

        parent::__construct($name, '', $label);

        $this->index = 0;

        if ($controls != NULL) {
            foreach ($controls as $c) {
                $this->addControl($c);
            }
        }
    }

    public function setIndex($control, $index) {
        $control->setId($this->name . '_' . $index);
        $control->setName($this->name . '[' . $index . ']');
    }

    public function addControl($control, $index = NULL) {
        if ($index == NULL) {
            $index = $this->index++;
        }

        if (is_array($control)) {
            foreach ($control as $c) {
                $this->addControl($c, $index);
            }
        } else {
            $this->container->insertControl($control, $index);
            $this->setIndex($control, $index);
        }
    }

    public function setValue($value) {
        $controls = $this->container->getControls();

        foreach ($controls as $k => $c) {
            $c->setValue($value[$k]);
        }

        $this->value = $value;
    }

    public function setDisposition($disposition) {
        $this->container->setDisposition($disposition);
    }

    public function generateInner() {
        $t = array();

        $controls = $this->container->getControls();

        foreach ($controls as $control) {
            $a = array(new MLabel($control->label != '' ? $control->label . ':&nbsp;' : ''), $control);
            $t[] = new MDiv('', $a);
        }

        $this->inner = $t;
    }

}

?>
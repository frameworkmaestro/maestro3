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

class MAttributes {

    public $attrs;
    public $dojoType;
    public $dojoProps;
    public $objAttr;

    public function __construct() {
        $this->attrs = array();
        $this->objAttr = array();
        $this->dojoType = '';
        $this->dojoProps = new MDojoProps();
    }

    public function __clone() {
        $this->dojoProps = clone $this->dojoProps;
    }

    public function addAttribute($name, $value = NULL) {
        if ($name !== '') {
            $this->attrs[$name][] = !is_null($value) ? $value : '';
        }
    }

    public function setDojoType($dojoType) {
        $this->dojoType = $dojoType;
    }

    public function addDojoProp($name, $value = NULL) {
        $this->dojoProps->add($name, $value);
    }

    public function getAttribute($name) {
        return $this->attrs[$name][0];
    }

    public function setAttributes($attr) {
        if ($attr != NULL) {
            if (is_array($attr)) {
                foreach ($attr as $ak => $av) {
                    $this->addAttribute($ak, $av);
                }
            } else if ($attr instanceof MAttributes) {
                $this->objAttr[] = $attr;
            } else if (is_string($attr)) {
                $attr = str_replace("\"", '', trim($attr));

                foreach (explode(' ', $attr) as $a) {
                    $a = explode('=', $a);
                    $this->addAttribute($a[0], $a[1]);
                }
            }
        }
    }

    public function attributes($mergeDuplicates = false) {
        return $this->getAttributes($mergeDuplicates);
    }

    public function getAttrs($mergeDuplicates = false) {
        $attributes = '';
        if (count($this->attrs)) {
            foreach ($this->attrs as $name => $value) {
                if ($mergeDuplicates) {
                    $attributes .= ' ' . $name . '= "';
                    foreach ($value as $v) {
                        $attributes .= $v . ';';
                    }
                    $attributes .= '"';
                } else {
                    $attributes .= ' ' . $name . '="' . $value[0] . '"';
                }
            }
        }
        return $attributes;
    }

    public function getAttributes($mergeDuplicates = false) {
        $attributes = $this->getAttrs($mergeDuplicates);
        $dojoType = $props = '';
        if (count($this->objAttr)) {
            foreach ($this->objAttr as $attr) {
                $attributes .= ' ' . $attr->getAttrs();
                $props .= $props ? ',' . $attr->dojoProps->getProps() : $attr->dojoProps->getProps();
                $dojoType = $attr->dojoType;
            }
        }
        $dojoType = $dojoType ? : $this->dojoType;
        $myProps = $this->dojoProps->getProps();
        $props .= (($props != '') && ($myProps != '')? ',' : '')  . $myProps;
        $attributes .= ($dojoType != '') ? ' ' . "data-dojo-type='" . $dojoType . "'" : '';
        $attributes .= ($props != '') ? ' ' . "data-dojo-props='" . $props . "'" : '';
        return $attributes;
    }

}

?>
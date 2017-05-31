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

class MSimpleTable extends MBaseTable {

    public $cell;
    public $attr;
    protected $renderAs;
    protected $columnStyle;

    public function init($id = '', $attributes = array(), $array = array()) {
        parent::init();
        $this->setId($id);
        if (!count($attributes)) {
            $attributes = array("cellspacing" => "0", "cellpadding" => "0", "border" => "0");
        }
        $this->setAttributes($attributes);
        $this->setClass($this->attributes->items['class']);
        $this->setFromArray($array);
    }

    public function onCreate() {
        parent::onCreate();
        $this->setRender('simpletable');
    }

    public function setFromArray($array = array()) {
        for ($i = 0; $i < count($array); $i++) {
            $this->attr['row'][$i] = '';
            for ($j = 0; $j < count($array[$i]); $j++) {
                $this->attr['cell'][$i][$j] = '';
                $this->cell[$i][$j] = $array[$i][$j];
            }
        }
    }

    private function setTableAttribute($area, $i, $j = NULL, $name, $attr) {
        $at = ($attr != '') ? " $name=\"$attr\" " : " $name ";
        if (is_null($j)) {
            $this->attr[$area][$i] .= $at;
        } else {
            $this->attr[$area][$i][$j] .= $at;
        }
    }

    private function setTableClass($area, $i, $j = NULL, $class) {
        if (is_null($j)) {
            $this->attr[$area][$i] .= " class=\"$class\" ";
        } else {
            $this->attr[$area][$i][$j] .= " class=\"$class\" ";
        }
    }

    public function setRowAttribute($i, $name, $attr) {
        $this->setTableAttribute('row', $i, NULL, $name, $attr);
    }

    public function setCellAttribute($i, $j, $name, $attr = '') {
        $this->setTableAttribute('cell', $i, $j, $name, $attr);
    }

    public function setHeadAttribute($i, $name, $attr = '') {
        $this->setTableAttribute('head', $i, NULL, $name, $attr);
    }

    public function setFootAttribute($i, $name, $attr = '') {
        $this->setTableAttribute('foot', $i, NULL, $name, $attr);
    }

    public function setRowClass($i, $class) {
        $this->setTableClass('row', $i, NULL, $class);
    }

    public function setCellClass($i, $j, $class) {
        $this->setTableClass('cell', $i, $j, $class);
    }

    public function setHeadClass($i, $class) {
        $this->setTableClass('head', $i, NULL, $class);
    }

    public function setFootClass($i, $class) {
        $this->setTableClass('foot', $i, NULL, $class);
    }

    public function setCell($i, $j, $content, $attrs = '') {
        $this->cell[$i][$j] = $content;
        if ($attrs != '') {
            $this->attr['cell'][$i][$j] .= $attrs;
        }
    }

    public function setHead($i, $content, $attrs = '') {
        $this->head[$i] = $content;
        if ($attrs != '') {
            $this->attr['head'][$i] .= $attrs;
        }
    }

    public function setFoot($i, $content, $attrs = '') {
        $this->foot[$i] = $content;
        if ($attrs != '') {
            $this->attr['foot'][$i] .= $attrs;
        }
    }

    public function setColGroup($i, $attrs = '') {
        $this->colgroup[$i]['attr'] = $attrs;
    }

    public function setColGroupCol($i, $j, $attrs = '') {
        $this->colgroup[$i]['col'][$j] = $attrs;
    }

    public function generate() {
        $n = count($this->head);
        for ($i = 0; $i < $n; $i++) {
            $head[$i] = $this->painter->generateToString($this->head[$i]);
        }
        $n = count($this->foot);
        for ($i = 0; $i < $n; $i++) {
            $foot[$i] = $this->painter->generateToString($this->foot[$i]);
        }
        $n = count($this->cell);
        $body = [];
        for ($i = 0; $i < $n; $i++) {
            $k = count($this->cell[$i]);
            for ($j = 0; $j < $k; $j++) {
                $body[$i][$j] = $this->painter->generateToString($this->cell[$i][$j]);
                $attr[$i][$j] = $this->attr['cell'][$i][$j];
            }
        }

        $this->setBody($body);
        $this->setAttr($this->attr);
        $this->generateEvent();
        return $this->render();
    }

}

?>
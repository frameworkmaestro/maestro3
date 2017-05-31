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

class MGridColumn extends MControl {

    public $grid; // grid which this columns belongs to
    public $title; // column title
    public $footer; // column footer
    public $options; // array for map data value to display value
    public $align; // column align - rigth, center, left
    public $nowrap; // column wrap/nowrap
    public $width; // column width in pixels or percent
    public $order; // column position on the grid
    public $value; // value at current row
    public $baseControl; // base Control to render value
    public $control; // array of Control clonning of basecontrol
    public $index; // column index (position) in the data array
    public $render; // a method from grid to replace the method generateControl
    public $type; // type of column (what control to render): 'label','link','control','render'
    public $action;
    public $href; //compatibilidade
    public $field; // field of query ("field" or "table.field")
    public $table; // owner of field

    public function init($title = '', $align = 'left', $nowrap = false, $width = 0, $visible = true, $options = null, $order = false, $filter = false, $index = -1) {
        parent::init();
        $this->visible = $visible;
        $this->title = $title;
        $this->options = $options;
        $this->align = $align;
        $this->nowrap = $nowrap;
        $this->width = $width;
        $this->order = $order;
        $this->index = $index;
    }

    public function onCreate() {
        parent::onCreate();
        $this->value = '';
        $this->index = -1;
        $this->footer = null;
        $this->control = array();
        $this->render = '';
        $this->type = 'label';
    }

    public function setField($field) {
        $this->field = $field;
        $f = explode('.', $field);
        if (count($f) == 2) {
            $this->field = $f[1];
            $this->table = $f[0];
        }
    }

    public function addControl($control) {
        $this->baseControl = $control;
    }

    public function generateControl($i, $row) {
        $method = $this->render ? 'render' : $this->type;
        $this->control[$i] = MGridColumnControl::$method($this, $i, $row);
        return $this->control[$i];
    }

}


?>
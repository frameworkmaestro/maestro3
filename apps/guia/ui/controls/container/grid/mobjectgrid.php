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

class MObjectGridColumn extends MGridColumn {

    public $attribute; // attribute of object

    public function init($attribute, $title = '', $align = 'left', $nowrap = false, $width = 0, $visible = true, $options = null, $order = false, $filter = false) {
        parent::init($title, $align, $nowrap, $width, $visible, $options, $order, $filter);
        $this->attribute = $attribute;
    }

}

class MObjectGridHyperlink extends MGridHyperlink {

    public $attribute; // attribute of object

    public function init($attribute, $title = '', $href, $width = 0, $visible = true, $options = null, $order = false, $filter = false) {
        parent::init($title, $href, $width, $visible, $options, $order, $filter);
        $this->attribute = $attribute;
    }

}

class MObjectGridControl extends MGridControl {

    public $attribute; // attribute of object

    public function init($control, $attribute, $title = '', $alinhamento = null, $nowrap = false, $width = 0, $visible = true) {
        parent::init($control, $title, $alinhamento, $nowrap, $width, $visible);
        $this->attribute = $attribute;
    }

}

class MObjectGridAction extends MGridAction {

    public function init($type, $alt, $value, $href, $index = null, $enabled = true) {
        parent::init($type, $alt, $value, $href, $enabled, $index);
    }

}

class MObjectGrid extends MGrid {

    /**
      ObjectGrid constructor
      $array - the object array
      $columns - array of columns objects
      $href - base url of this grid
      $pagelength - max number of rows to show (0 to show all)
     */
    public $objArray;

    public function init($name = '', $array, $columns, $href, $pagelength = 15, $index = 0) {
        parent::init($name, NULL, $columns, $href, $pagelength, $index);
        if ($array) {
            $this->objArray = $array;
        }
    }

    public function onAfterCreate() {
        parent::onAfterCreate();
        if (is_array($this->data)) {
            $this->objArray = $this->data;
            $this->data = array();
        }
        $this->rowCount = count($this->objArray);
    }

    public function generateData() {
        if ($this->objArray == NULL) {
            $this->data = array();
        } else {
            $this->data = array();
            foreach ($this->objArray as $i => $row) {
                foreach ($this->columns as $k => $col) {
                    if (strpos(strtolower(get_class($col)), 'mobject') !== false) {
                        $method = 'get' . $col->attribute;
                        $v = (method_exists($row, $method)) ? $row->$method() : $row->{$col->attribute};
                        $this->data[$i][$k] = $v;
                    }
                }
            }
        }
        parent::generateData();
    }

    public function callRowMethod() {
       if (isset($this->rowMethod)) {
            if ($this->rowMethod[0] == 'form') {
                $this->rowMethod[0] = get_class($this->form);
            }
            $i = $this->currentRow;
            $row = $this->data[$i];
            call_user_func($this->rowMethod, $i, $row, $this->actions, $this->columns, $this->objArray);
        }
    }

}

?>

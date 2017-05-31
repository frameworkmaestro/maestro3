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

class MTextTable extends MFormControl {

    protected $buttons;
    protected $fields;
    protected $showcode;
    protected $shownav;
    protected $layout;
    protected $codevalue;
    protected $columnWidth;
    protected $columnStyle;
    protected $rawWidth;
    protected $width;
    private $tableId;
    public $info;
    protected $numRows;
    private $scrollHeight;
    private $scrollWidth;
    private $table;
    private $select;
    private $index;
    private $zebra;
    private $title;

    public function init($id = '', $value = array(), $label = '', $select = '', $zebra = true) {
        parent::init($id, $value, $label);
        $this->tableId = $id . '_table';
        $this->select = $select;
        $this->caption = $caption;
        $this->zebra = $zebra ? 'true' : 'false';
    }

    public function onCreate() {
        parent::onCreate();
        $this->scrollHeight = '';
        $this->scrollWidth = '';
        $this->index = 0;
        $this->rawWidth = 300;
        $this->width = '300px';
        $this->columnWidth = array();
        $this->columnStyle = array();
        $this->zebra = 'true';
    }

    public function onAfterCreate() {
        parent::onAfterCreate();
        if (count($this->columnWidth) == 0) {
            $this->columnWidth[0] = $this->width;
        }
    }

    public function __set($name, $value) {
        $property = strtolower($name);
        if ($property == 'width') {
            $this->setWidth($value);
        } elseif ($property == 'title') {
            $this->setTitle($value);
        } elseif ($property == 'id') {
            $this->setId($value);
        } else {
            parent::__set($name, $value);
        }
    }

    public function setId($id) {
        $this->tableId = $id . '_table';
        parent::setId($id);
    }

    public function setWidth($width) {
        $this->width = $width;
        $this->rawWidth = str_replace('px', '', $width);
    }

    public function setScrollHeight($height) {
        $this->scrollHeight = $height;
    }

    public function setScrollWidth($width) {
        $this->scrollWidth = $width;
        $this->setWidth($width);
    }

    public function setTitle($title = array()) {
        $title = (is_array($title)) ? $title : explode(',', $title);
        foreach ($title as $i => $c) {
            $column = explode('|', $c);
            $this->title[$i] = $column[0];
            $this->columnStyle[$i] = $column[2];
        }
    }

    public function setColWidth($colWidth = array()) {
        $this->colWidth = $colWidth;
    }

    public function setIndex($value) {
        $this->index = $value;
    }

    public function getTableId() {
        return $this->tableId;
    }

    public function addCode($code) {
        $this->codevalue[] = $code;
    }

    public function generateLink($value, $row) {
        $index = $row[$this->index];
        $href = ereg_replace('\$id', $index, $value);
        $n = count($row);
        for ($r = 0; $r < $n; $r++) {
            $href = str_replace("%$r%", urlencode($row[$r]), $href);
            $href = str_replace("#$r#", $row[$r], $href);
        }
        return $href;
    }

    public function generate() {
        if (!count($this->columnWidth)) {
            return '';
        }
        // default code for row selection
        if ($this->select != '') {
            $select = MActionControl::getHref($this->select);
            $this->addCode("{$this->tableId}.customSelect = function() { " .
                    "var cells = this.get(this.rowSelected);" .
                    "var url = '{$select}'; " .
                    "for (c = 0; c < cells.length; ++c) {" .
                    "   url = url.replace('%' + c + '%', cells[c]);" .
                    "}" .
                    "manager.doLinkButton(url,'','','{$this->tableId}'); " .
                    "};");
        }
        $data = json_encode($this->value);
        $cols = json_encode($this->title);

        foreach ($this->columnStyle as $i => $style) {
            $cssCode .= ".field-" . $this->title[$i] . ' ' . ($style ? : '{}') . ' ';
        }
        $cssCode .= '.dgrid-row-even {background-color: #EEE}';
        Manager::getPage()->addStyleSheetCode($cssCode);

        $template = new MTemplate(__DIR__);
        $template->context('manager', Manager::getInstance());
        $template->context('data', $data);
        $template->context('cols', $cols);
        $template->context('id', $this->id);
        $js = $template->fetch('mtexttable.js');
        $this->page->onLoad($js);
        $div = new MContentpane($this->id);
        $div->setWidth($this->width);
        $div->setHeight($this->scrollHeight);
        return $div->generate();
    }

}

?>

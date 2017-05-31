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

class MGridField extends MFormControl {

    protected $buttons;
    protected $fields;
    protected $codeValue;
    protected $info;
    public $actionInsert;
    public $actionDelete;
    public $actionGrid;
    public $arrayData;
    public $arrayTitle;

    public function init($id = '', $value = null, $label = '', $fields = array()) {
        parent::init($id, $value, $label, $size, $hint);
        $this->setFields($fields);
    }

    public function onCreate() {
        parent::onCreate();
        $this->setFieldWidth('250px');
    }

    public function setCaption($value) {
        $this->property->caption = $value;
    }

    public function getCaption() {
        return $this->property->caption;
    }

    public function setFields($fields) {
        $this->fields = $fields;
    }

    public function addField($field) {
        $this->fields[] = $field;
    }

    public function setFieldWidth($width) {
        $this->fieldWidth = str_replace('px', '', $width);
    }

    // for use with XML Forms
    public function addControl($field) {
        $this->addField($field);
    }

    public function getGrid() {
        return $this->grid;
    }

    public function generateInner() {
        $id = $this->getId();
        $value = $this->getValue();
        $numFields = count($this->fields);
        // fields
        $fields = '';
        $n = 1;
        $ref = '';

        $totalWidth = 0;
        foreach ($this->fields as $f) {
            $ref .= ( $ref ? ',' : '') . $f->getName();
            $f->form = $this->form;
            $f->setLabel(htmlspecialchars($f->label));
            //$f->formMode = 2;
            $fields[] = $f;
            $totalWidth += ( $width[] = $f->width ? : '15');
            $n++;
        }
        $btnInsert = new MButton("{$id}_btnInsert", 'Inserir', "!{$id}_ajaxInsert();");
        $actionInsert = MAction::getHref($this->actionInsert);
        $this->page->addJsCode("{$id}_ajaxInsert = function() {  manager.doAjaxText('{$actionInsert}', '{$id}_divGrid', '{$id}_btnInsert') };");
        $fields[] = $btnInsert;

        $field = new MContentPane("{$id}_divGrid");
        $select = $field;

        $btnDelete = new MButton("{$id}_btnDelete", 'Remover selecionados', "!{$id}_ajaxDelete();");
        $actionDelete = MAction::getHref($this->actionDelete);
        $this->page->addJsCode("{$id}_ajaxDelete = function() {  manager.doAjaxText('{$actionDelete}', '{$id}_divGrid', '{$id}_btnDelete') };");

        // layout

        $t = array();
        $array[] = new MDiv('', new MHContainer("{$id}_containerFields", $fields));
        $array[] = new MDiv('', $select);
        $array[] = new MDiv('', $btnDelete);
        $container = new MVContainer('', $array);
        $group = new MBaseGroup('', $this->getCaption(), $container);

		if (strpos($this->actionGrid,Manager::getBaseURL()) !== false) {
			$actionGrid = $this->actionGrid;
		} else {
	        $actionGrid = MAction::getHref($this->actionGrid . ($value ? '/' . $value : ''));
		}
        $this->page->onLoad(MUI::set("{$id}_divGrid", 'href', $actionGrid) . ';');

        $div = new MDiv($this->name, $group);
        $div->attributes = clone $this->attributes;
        $div->style = clone $this->style;
		$div->setClass('mGridField');

        $this->inner = array(
            $div,
        );

        return $this->inner;
    }

}

?>
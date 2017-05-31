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

class MDragDropControl {

    protected $control;
    protected $options;
    public $containerId;

    public function __construct($control) {
        $this->control = $control;
        $this->options = new MStringList();
    }

    public function addOption($option, $value) {
        $this->options->addValue($option, $value);
    }

}

class MDraggable extends MDragDropControl {

    public function generate() {
        $div = new MDiv("ddm_{$this->control->id}", $this->control);
        $this->control->setClass("dojoDndItem");
        $js = "ddm_{$this->control->id} = new dojo.dnd.Source('ddm_{$this->control->id}'";
        $js .= $this->options->hasItems() ? ",{" . $this->options->getText(':', ',') . "}" : '';
        $js .= ");";
        $js .= "dojo.parser.parse(\"dojo.byId('ddm_{$this->control->id}')\");";
        $this->page->onLoad($js);
        return $div->generate();
    }

    public function addRevertNotDropped() {
    }

}

class MDroppable extends MDragDropControl {

    private $onDrop;

    public function generate() {
        $this->addOption("isSource", "false");
        $js = "ddm_{$this->control->id} = new dojo.dnd.Source('{$this->control->id}'";
        $js .= $this->options->hasItems() ? ",{" . $this->options->getText(':', ',') . "}" : '';
        $js .= ");";
        $js .= "dojo.parser.parse(\"dojo.byId('{$this->control->id}')\");";
        return $js;
    }

    public function onDrop($jsCode) {
        $this->onDrop = $jsCode;
    }

}

class MDragDrop extends MFormControl {

    private $draggable = array();
    private $dropZone = array();

    public function addDraggable($control, $options = array()) {
        $js = "dnd_{$control->id} = new dojo.dnd.Source('{$control->id}'";
        $js .= ",{";
        if (count($options)) {
            foreach ($options as $a => $v) {
                $js .= $a . ':' . $v . ',';
            }
        }
        $js .= "creator: function(item, hint){ if(hint == 'avatar'){ return {node: dojo.dnd._createSpan(item.data)};} else { mynode = dojo.byId(item.data); } return {node: mynode, data: item}; } ";
        $js .= "}";
        $js .= ");";
        foreach ($control->getControls() as $c) {
            if ($c instanceof MControl) {
                $js .= "dnd_{$control->id}.insertNodes(false,[{data: '{$c->id}'} ]);";
            }
        }
        $this->page->onLoad($js);
    }

    public function addDropZone($control, $options = array()) {
        $js = "dnd_{$control->id} = new dojo.dnd.Source('{$control->id}'";
        $js .= ",{";
        if (count($options)) {
            foreach ($options as $a => $v) {
                $js .= $a . ':' . $v . ',';
            }
        }
        $js .= "isSource: false ";
        $js .= "}";
        $js .= ");";
        $js .= "dojo.connect(dnd_{$control->id}, 'onDrop', function (s,n,c) { ddm_{$this->id}.onDrop('{$control->id}',s,n,c);});";
        $this->page->onLoad($js);
    }

    public function getValue() {
        parse_str($this->value, $v);
        return $v;
    }

    public function generate() {
        $this->page->addScript('m_dragdrop.js');
        $this->page->addJsCode("var ddm_{$this->id} = new Manager.DnD('{$this->id}');");
        $this->page->onSubmit("ddm_{$this->id}.onSubmit()");
        $this->page->addDojoRequire("dojo.dnd.Source");
        return $this->getRender('inputhidden');
    }

}

?>
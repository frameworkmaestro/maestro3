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

class MSelection extends MInputControl {

    protected $list;
    protected $size;
    protected $type; // filter, group, multiple, input
    public $required;
    protected $showValues;
    public $content;

    function init($id = '', $value = '', $label = '', $options = '', $size = '', $showValues = false, $hint = '') {
        parent::init($id, $value, $label, '', $hint);
        $this->setOptions($options);
        $this->setShowValues($showValues);
        $this->size = $size;
    }

    public function onCreate() {
        parent::onCreate();
        $this->type = 'filter';
        $this->autoPostBack = false;
        $this->list = new MListControl($this->id);
        $this->required = false;
        $this->setOptions(array());
        $this->setShowValues(false);
        $this->setRender('select');
    }

    public function setShowValues($value) {
        $this->showValues = MUtil::getBooleanValue($value);
        $this->list->setShowValues($this->showValues);
    }

    public function getType() {
        return $this->type;
    }

    public function setType($value) {
        $this->type = $value;
    }

    public function getRequired() {
        return $this->required;
    }

    public function setRequired($value) {
        $this->required = $value;
    }

    function setOptions($options) {
        if (!is_array($options)) {
            $options = array(_M('No'), _M('Yes'));
        }
        if (is_array($options[0])) {
            $aux = $options;
            $options = array();
            foreach ($aux as $option) {
                $options[$option[0]] = $option[1];
            }
        }
        $this->list->SetOptions($options);
        $this->type = $this->list->getType();
    }

    function getOption($value = '') {
        return $this->list->getOption($value ? : $this->getValue());
    }

    public function setOption($option, $value) {
        $this->list->addControl(($option instanceof MOption) ? $option : new MOption($value, $value, $option));
    }

    public function addEvent($event, $handler, $preventDefault = false) {
        parent::addEvent($event, $handler, $preventDefault, true);
    }

    public function generateValidator() {
        $validators = $this->getValidator();
        foreach ($validators as $validator) {
            if ($validator instanceof MValidator) {
                $attributes = $validator->get();
                $this->setAttributes($attributes);
                $attributes->setDojoType('');
                $this->setRequired($attributes->attrs['required'][0]);
            }
        }
    }

    public function generateInner() {
        if ($this->autoPostBack) {
            $this->addEvent('onChange', "manager.doPostBack('{$this->id}');");
        }
        $this->content = $this->list->generateOptions($this->getValue());

        $this->addAttribute('placeHolder', _M('-- Selecione --'));

        $this->generateValidator();

        if ($this->getValue() == '') {
            $this->setAttribute('value', '');
        }
        $hidden = NULL;
        if ($this->readonly) {
            $hidden = new MHiddenField($this->getId(), $this->getValue());
            $this->setId($this->getId() . '_ro');
            //$this->setName($this->getName() . '_ro');
            $this->setValue($this->getOption($this->getValue()));
            if (($this->type == 'group') || ($this->type == 'multiple')) { // widgets uses native HTML <selection> and the readonly attribute will not work
                $this->addAttribute('disabled', 'disabled');
            } else {
                $this->addAttribute('readonly');
            }
            $this->size = $this->cols ? $this->cols : strlen(trim($this->getValue())) + 10;
        } else {
            $this->addEvent("onChange", "dojo.publish('{$this->getId()}ChangeValue',[manager.byId('{$this->getId()}').get('value')]);", false);
        }
        $required = ($this->getRequired() ? 'true' : 'false');
        if ($this->type == 'filter') {
            $this->page->addDojoRequire('dijit.form.FilteringSelect');
            $this->page->addDojoRequire('dijit.form.Select');
            $this->setDojoType('dijit.form.FilteringSelect');
            if ($this->getValue() == '') {
                $this->page->onLoad("manager.byId(\"" . $this->getId() . "\").set(\"value\",\"\");");
            }
            $this->page->onLoad("manager.byId(\"" . $this->getId() . "\").set(\"required\",{$required});");
        } elseif ($this->type == 'multiple') {
            $this->page->addDojoRequire('dijit.form.MultiSelect');
            $this->setDojoType('dijit.form.MultiSelect');
            $this->addDojoProp('size', $this->size ? $this->size : '3');
        } elseif ($this->type == 'group') {
            
        } elseif ($this->type == 'input') {
            $this->page->addDojoRequire('dijit.form.ComboBox');
            $this->setDojoType('dijit.form.ComboBox');
            $this->addDojoProp('size', $this->size ? $this->size : '3');
        } elseif ($this->type == 'selectOnly') {
            $this->page->addDojoRequire('dijit.form.Select');
            $this->setDojoType('dijit.form.Select');
            $this->addDojoProp('size', $this->size ? $this->size : '3');
        }

        $this->inner = ($hidden ? $hidden->generate() : '') . $this->render();
    }

    public function __clone() {
        parent::__clone();
        $this->list = clone $this->list;
    }

}

?>

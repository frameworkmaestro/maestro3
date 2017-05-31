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

class MTextField extends MInputControl {

    protected $type; //text, multiline, password, file
    protected $required;
    protected $overrideType = true;

    public function init($id = '', $value = '', $label = '', $size = 10, $hint = '', $validator = NULL, $isReadOnly = false) {
        parent::init($id, $value, $label, '', $hint);
        $this->setReadOnly($isReadOnly);
        $this->setValidator($validator);
        $this->setSize($size);
    }

    public function onCreate() {
        parent::onCreate();
        $this->type = 'text';
        $this->setRender('inputtext');
    }

    public function setSize($value) {
        $this->property->size = $value;
        $this->setWidth($value . 'em');
    }

    public function getSize() {
        return $this->property->size;
    }

    public function setMaxlength($value){
         $this->addAttribute('maxlength', $value);
    }

    public function getMaxlength(){
        return $this->getAttribute('maxlength');
    }
    public function getType() {
        return $this->type;
    }

    public function setMask($mask) {
        $this->property->mask = $mask;
    }

    public function getMask() {
        return $this->property->mask;
    }

    public function setPlaceholder($value) {
        $this->property->placeholder = $value;
    }

    public function getPlaceHolder() {
        return $this->property->placeholder;
    }

    protected function setRequired($required = true) {        
        $this->required = $this->required || $required;
    }

    public function generateValidator() {
        $validators = $this->getValidator();
        foreach ($validators as $validator) {
            if ($validator instanceof MValidator) {
                $attributes = $validator->get();
                if (!$this->overrideType) {
                    $attributes->setDojoType($this->getAttribute('dojoType'));
                }
                $this->setAttributes($attributes);
            }
        }
    }

    public function generateInner() {

        if ($this->autoPostBack) {
            $this->addEvent('blur', "manager.doPostBack('{$this->getId()}');");
        }

        if ($this->getReadOnly()) {
            $this->setClass('mReadOnly');
            $this->addAttribute('readonly');
        }

        if ($this->getPlaceHolder()) {
            $this->addAttribute('placeholder', $this->getPlaceHolder());
        }
        
        if ($this->required) {
            $this->setValidator(new MRequiredValidator($this->id, "Informar o valor do campo"));
        }

        $this->generateValidator();        
        $this->inner = $this->render();
        parent::generateInner();
    }

}

?>
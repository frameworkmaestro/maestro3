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

class MInputField extends MInputControl {

    private $typeClasses = array();
    private $set;
    private $input;
    
    public function onCreate() {
        parent::onCreate();
        $this->typeClasses = array(
            "date" => "MCalendarField",
            "spinner" => "MNumberSpinner",
        );
        $this->set = new stdClass();
    }
    
    
    
    public function __set($name, $value) {
        parent::__set($name, $value);
        $this->set->$name = $value;
    }
    
    public function generate(){
        $type = $this->typeClasses[$this->set->type] ? : 'm' . $this->set->type . 'field';
        $this->input = new $type;
        $this->input->style = clone $this->style;
        if ($this->fieldLabel) {
            $this->input->fieldLabel = clone $this->fieldLabel;
        }
        foreach($this->property as $name=>$value) {
            $this->input->property->$name = $value;
        }
        $this->input->event = $this->event;
        $this->input->ajax = $this->ajax;
        $this->input->form = $this->form;
        foreach($this->set as $name=>$value) {
            $this->input->$name = $value;
        }
        return $this->input->generate();
    }

}

?>
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

class MTimestampField extends MTextField {

    public function init($name = '', $value = '', $label = '', $hint = '') {
        parent::init($name, $value, $label, $size, $hint);
    }

    public function onCreate() {
        parent::onCreate();
        $this->overrideType = false;
        $this->setRender('inputtimestamp');
    }
    
    public function setValue($value) {
        if (($value instanceof MDate) || ($value instanceof MTimestamp)) {
            $value = $value->format();
        }
        parent::setValue($value);
    }

    public function setRange($rangeDate = array()) {
        $this->property->rangeDate = $rangeDate;
    
    }

    public function setIncrement($increment) {
        $this->property->increment = $increment;
    }

    public function setlockTimeUserInput($value){
        $this->property->lockTimeUserInput=$value;
    }

    public function generateInner() {
        $code = <<< HERE
manager.onSubmit['{$this->id}'] = function() {
	var date = manager.byId('{$this->id}Date');
	var time = manager.byId('{$this->id}Time');
	var datetime = date + ' ' + time;
	manager.byId('{$this->id}').value = datetime.replace('T','');
	return true;
}

HERE;
        $this->form->addJsCode($code);
        $this->form->onSubmit("manager.onSubmit['{$this->id}']()");

        $controls[0] = new MCalendarField($this->id . 'Date', substr($this->value, 0, 10), '');
        $controls[1] = new MTimeField($this->id . 'Time', substr($this->value, 11, 5), '', 6);
        $controls[2] = new MHiddenField($this->id, $this->value);

        $controls[1]->setIncrement($this->property->increment);
        $controls[1]->setLockUserInput($this->property->lockTimeUserInput);

        
        if ($this->readonly) {
            $controls[0]->addAttribute('readonly');
            $controls[1]->addAttribute('readonly');
        }

        $validators = $this->getValidator();
        foreach ($validators as $validator) {
            $controls[0]->setValidator($validator);
            $controls[1]->setValidator($validator);
        }

        $container = new MHcontainer('', $controls);
        $this->inner = $container->generate();
    }

}

?>
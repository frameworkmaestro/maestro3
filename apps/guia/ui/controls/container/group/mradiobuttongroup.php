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

class MRadioButtonGroup extends MBaseGroup {

    /**
     * An array of MRadioButton objects
     * @var array
     */
    public $options;
    public $default;
    public $value;

    public function onload() {
        $value = func_get_arg(0);
        if (isset($value)) {
            if (is_object($value)) {
                $id = $this->id;
                if (isset($value->$id)) {
                    $this->setValue($value->$id);
                }
            }
        }
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        foreach ($this->getControls() as $radio) {
            $radio->check($value);
        }
    }

    // override default addControl because for radio group $name is different from $id
    // $name must be the name of group not the name of control
    public function addControl($control) {
        $control->addEvent("click", "dojo.publish('{$this->getName()}ChangeValue',[manager.byId('{$control->getId()}').get('value')]);",false);
        $control->setName($this->name);
        parent::addControl($control);
        $value = $this->value ?: mrequest($this->name) ?: $this->default;
        $control->setChecked( $control->getValue() == $value ) ;
    }

    public function onCreate(){
        parent::onCreate();
        $this->setShowLabel(false);
    }

    public function init($id = '', $label = '', $options = array(), $default = false, $disposition = MContainer::DISPOSITION_VERTICAL) {
        $this->name = $id;
        $this->default = $default;
        parent::init($id, $label, $options, $disposition, $border);
        $this->setValue($default);
    }
    
}

?>
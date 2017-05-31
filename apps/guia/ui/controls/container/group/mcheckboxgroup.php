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

class MCheckBoxGroup extends MBaseGroup {

    public function onCreate(){
        parent::onCreate();
        $this->setShowLabel(false);
        
    }

    public function init($id = '', $label = '', $options = array(), $disposition = MContainer::DISPOSITION_VERTICAL) {
        parent::init($id, $label, $controls, $disposition);
    }

    public function setOptions($value){
        $this->setControls($this->getControlsFromOptions($value));
    }
    
    public function getControlsFromOptions($options) {
        $name = $this->name;
        $controls = array();

        if (!$options) {
            $options = array();
        }

        if (!is_array($options)) {
            $options = array($options);
        }

        $n = count($options);

        for ($i = 0; $i < $n; $i++) {
            // we will accept an array of MCheckBox ...
            if ($options[$i] instanceof MCheckBox) {
                $controls[] = clone $options[$i];
            } else {
                // we will accept an array of MOption ...
                if ($options[$i] instanceof MOption) {
                    $oName = $name . '_' . $options[$i]->name;
                    $oLabel = $options[$i]->label;
                    $oValue = $options[$i]->value;
                    $oChecked = $options[$i]->checked || ( $oValue == mrequest($oName) );
                }
                // or an array of label/value pairs ...
                elseif (is_array($options[$i])) {
                    $oName = $name . '_' . $i;
                    $oLabel = $options[$i][0];
                    $oValue = $options[$i][1];
                    $oChecked = ($oValue == mrequest($oName));
                }
                // or a simple array of values
                else {
                    $oName = $name . '_' . $i;
                    $oLabel = $oValue = $options[$i];
                    $oChecked = ($oValue == mrequest($oName));
                }

                $option = new MCheckBox($oName, $oValue, $oLabel, $oChecked, $oLabel);
                if ($options[$i] instanceof MOption) {
                    $option->attrs = $options[$i]->attrs;
                }
                $controls[] = $option;
            }
        }
        return $controls;
    }

    

    /**
     * Array with values for controls
     * @param array $value
     */
    public function setValue($value){
        foreach ($value as $key=>$val) {
            $control = $this->findControlById($key);
            if ($control) {
                $control->setValue($val);
            }
        }
    }

    public function getValue() {
        $value = array();
        $controls = $this->getControls();
        foreach ($controls as $control) {
            $value[$control->getName()] = $control->checked ? $control->getValue() : NULL;
        }
        return $value;
    }

}

?>
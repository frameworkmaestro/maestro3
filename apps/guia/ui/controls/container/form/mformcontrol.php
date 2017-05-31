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

/*
  MFormControls can be used inside MBaseForm
  - It can be a Input or Output control
 */

class MFormControl extends MControl {

    public function onCreate() {
        parent::onCreate();
        $this->setValue(NULL);
    }

    public function init($id = '', $value = '', $label = '', $hint = '') {
        parent::init($id);
        $this->setHint($hint);
        $this->setValue($value);
        $this->setLabel($label);
    }

    public function onload() {
        if (func_num_args() > 0) {
            $value = func_get_arg(0);
            if (isset($value)) {
                if (is_object($value)) {
                    $id = $this->id;
                    if (isset($value->$id)) {
                        $newValue = $value->$id;
                    }
                } else {
                    $newValue = $value;
                }
            }
            if (isset($newValue)) {
                $this->setRawValue($newValue);
                $this->setValue($newValue);
            }
        }
    }

    public function getRawValue() {
        return $this->property->rawValue;
    }

    public function setRawValue($value) {
        $this->property->rawValue = $value;
    }

    public function setForm(MBaseForm $form) {
        $this->form = $form;
    }

    public function getFormId() {
        return $this->form ? $this->form->getId() : '';
    }

    public function getFormTagId() {
        return $this->form ? $this->form->getTagId() : '';
    }

}

?>
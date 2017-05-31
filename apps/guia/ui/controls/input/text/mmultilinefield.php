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

class MMultiLineField extends MTextField {

    public $rows;
    public $cols;

    public function init($id = '', $value = '', $label = '', $rows = 1, $cols = 20, $hint = '', $validator = null) {
        parent::init($id, $value, $label, $cols, $hint, $validator);
        $this->rows = $rows;
        $this->cols = $cols;
    }

    public function onCreate() {
        parent::onCreate();
        if ($this->getClass() == '') {
            $this->setClass('mMultilineField');
        }
        $this->setRender('inputtextarea');
    }

    /*
     * Override generateValidator in order to avoid to change dojoType
     */

    public function generateValidator() {
        $validators = $this->getValidator();
        foreach ($validators as $validator) {
            if ($validator instanceof MValidator) {
                $attributes = $validator->get();
                if ($validator instanceof MRequiredValidator) {
                    $this->page->addDojoRequire('Manager.ValidationTextarea');
                    $attributes->dojoType = 'Manager.ValidationTextarea';
                }
                $this->setAttributes($attributes);
            }
        }
    }

    public function generateInner() {
        if ($this->getReadOnly()) {
            $this->setClass('mReadOnly');
            $this->addAttribute('readOnly');
        }
        $this->generateValidator();
        $this->type = 'multiline';
        $this->inner = $this->render();
    }

}

?>
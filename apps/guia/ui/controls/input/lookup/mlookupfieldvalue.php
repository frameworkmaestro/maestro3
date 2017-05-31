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

class MLookupFieldValue extends MLookupField {

    public function init($name = '', $value = '', $label = '', $size = 10, $action = '', $related = '', $filter = '', $autocomplete = false, $hint = '') {
        parent::init($name, $value, $label, $action, $related, $filter, $autocomplete, $hint);
        $this->size = $size;
    }

    public function generateInner() {
        $field = new MTextField($this->name, $this->value, '', $this->size, $this->hint, $this->validator);
        $field->setAttributes($this->attributes);
        if ($this->autocomplete) {
            $field->addEvent('change', "{$this->lookupName}.start();");
        }

        $field->setWidth($this->style->get('width'));
        $field->validator = $this->validator;
        $field->form = $this->form;
        $field->showLabel = $this->showLabel;
        $field->setClass('mReadOnly');
        $field->addAttribute('readonly');

        parent::generateInner();
//        $lookupField = $this->getInner();
//        $container = new MHContainer('', array(( $this->readonly ? '' : $lookupField), $field));
//        $container->setClass('mLookupField');
//        $container->setShowLabel(false);
//        $this->inner = $container;

        $lookupField = $this->getInner();
        //$container = new MHContainer('', array($field, ( $this->readonly ? '' : $lookupField)));
        //$container->setShowLabel(false);
        $field = new MDiv('', $field, 'textfield');
        $lookup = $this->readonly ? '' : $lookupField;
        $container = new MDiv('', array($lookup, $field), 'mLookupFieldValue mContainer');
        //$container->setClass('mLookupField');
        $this->inner = $container;
    }

}

?>
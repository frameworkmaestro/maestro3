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

class MBaseGroup extends MContainer {
    /*
      property $scrollable;
      property $scrollHeight;
      property $caption;
      property $fieldset;
     */

    public function onCreate() {
        parent::onCreate();
        $this->setScrollable(false);
        $this->setFieldSet(true);
        $this->setRender('fieldset');
    }

    public function init($id = '', $caption = '', $controls = array(), $disposition = MContainer::DISPOSITION_VERTICAL, $labelMode = MFieldLabel::LABEL_ABOVE) {
        parent::init($id, $controls, $disposition, $labelMode);
        $this->setCaption($caption);
    }

    public function setScrollable($value) {
        $this->property->scrollable = $value;
    }

    public function getScrollable() {
        return $this->property->scrollable;
    }

    public function setCaption($value) {
        $this->property->caption = $value;
    }

    public function getCaption() {
        return $this->property->caption;
    }

    public function setFieldSet($value) {
        $this->property->fieldset = $value;
    }

    public function getFieldSet() {
        return $this->property->fieldset;
    }

    public function setScrollHeight($height) {
        $this->property->scrollable = true;
        $this->property->scrollHeight = $height;
    }

    public function getScrollHeight() {
        return $this->property->scrollHeight;
    }

    public function addControl($control) {
        parent::addControl($control, $control->getId());
    }

    public function onload() {
        if (func_num_args() > 0) {
            $value = func_get_arg(0);
            if (isset($value)) {
                if (is_object($value)) {
                    $id = $this->id;
                    if (is_array($value->$id)) {
                        $this->setValue($value->$id);
                    }
                }
            }
        }
    }

    public function generate() {
        $this->generateInner();
        if ($this->getScrollable()) {
            $f[] = new MDiv('', $this->getCaption(), 'mScrollableLabel');
            $html = $this->getInnerToString();
            $f[] = $div = new MDiv('', $html, 'mScrollableField');
            $div->height = $this->getScrollHeight();
        } elseif ($this->property->fieldset) {
            $this->setClass('mBaseGroup', false);
            $f = $this->render();
        } else {
            $f = $this->inner;
        }
        $outer = new MDiv('', $f);
        return $outer->generate();
    }

}

?>
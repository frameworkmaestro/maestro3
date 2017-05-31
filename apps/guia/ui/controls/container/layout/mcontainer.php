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

class MContainer extends MContainerControl {

    const DISPOSITION_VERTICAL = 1;
    const DISPOSITION_HORIZONTAL = 2;

    protected $hasLabel = false;
    protected $hasHint = false;
    protected $ajax = NULL;

    public function addControlsFromXML($file) {
        $xmlControls = new MXMLControls();
        $xmlControls->loadFile($file, $this);
        $xmlControls->process();
    }

    public function onCreate() {
        parent::onCreate();
        $this->setLabelMode(MFieldLabel::LABEL_ABOVE);
        $this->setShowLabel(true);
        $this->setDisposition(MContainer::DISPOSITION_VERTICAL);
    }

    public function init($id = '', $controls = array(), $disposition = MContainer::DISPOSITION_VERTICAL, $labelMode = MFieldLabel::LABEL_ABOVE, $showLabel = true) {
        parent::init($id);
        $this->setLabelMode($labelMode);
        $this->setShowLabel($showLabel);
        $this->setDisposition($disposition);
        $this->setControls($controls);
    }

    public function setDisposition($disposition) {
        $this->property->disposition = $disposition;
    }

    public function getDisposition() {
        return $this->property->disposition;
    }

    public function setShowLabel($visible = true, $recursive = true) {
        $this->property->showLabel = $visible;
        if ($recursive) {
            $controls = $this->getControls();
            foreach ($controls as $control) {
                if ($control instanceof MContainer) {
                    $control->setShowLabel($visible, true);
                }
            }
        }
    }

    public function isShowLabel() {
        return $this->property->showLabel;
    }

    public function setLabelMode($value) {
        $this->property->labelMode = $value;
    }

    public function getLabelMode() {
        return $this->property->labelMode;
    }

    protected function getLayout() {
        $layout = array();
        $hidden = array();
        $controls = $this->getControls();
        foreach ($controls as $control) {
            if ($control->visible) {
                if (is_array($control)) {
                    $array = array();
                    foreach ($control as $field) {
                        if ($field->visible) {
                            $array[] = $field;
                        }
                    }
                    $control = new MHContainer('', $array);
                    $control->setShowLabel(true);
                } elseif ($control instanceof MHiddenField) {
                    $hidden[] = $control;
                } else {
                    $this->hasLabel |= ( $control->getLabel() != '');
                    $this->hasHint |= ( $control->getHint() != '');
                    $layout[] = $control;
                }
            }
        }
        return array($layout, $hidden);
    }

    public function generateLayoutHorizontal() {
        $id = $this->id ?: 'mh'.uniqid();
        list($layout, $hidden) = $this->getLayout();
        $array = array();
        $i = 0;
        $j = count($layout) - 1;
        foreach ($layout as $control) {
            if ($control->checkAccess()) {
                $label = $hint = NULL;
                $pos = (($i == 0) ? ' firstCell' : (($i == $j) ? ' lastCell' : '')) . ' cell' . $i;
                if ($control instanceof MContainer) {
                    $attributes = ' ' . $control->getAttributes();
                } else {
                    if ($this->isShowLabel()) {
                        if (($this->hasLabel) && ($control->getLabel() == '')) {
                            $control->setLabel('&nbsp;');
                        }
                        $label = $control->generateLabel();
                    }
                }
                if (($this->hasHint) && ($control->getHint() == '')) {
                    $control->setHint('&nbsp;');
                }
                $hint = $control->generateHint();

                if ($control instanceof MDiv) {
                    $div = $control;
                    //$div->setId($id . '_col' . $i);
                    $div->setClass('mContainerCell' . $pos);
                } else {
                    $div = new MDiv($id . '_col' . $i, array($label, $control->generate(), $hint), 'mContainerCell' . $pos);
                }
                $width = $control->getColumnWidth();
                if ($width != '') {
                    $div->setWidth($width);
                }
                $array[] = $div;
                $i++;
            }
        }

        $div = new MDiv($id, $array, 'mContainer mHContainer');
        $div->cloneStyle($this);
        $div->setClass('mContainer mHContainer');
        return array($div, $hidden);
    }

    public function generateLayoutVertical() {
        $id = $this->id ?: 'mv'.uniqid();
        list($layout, $hidden) = $this->getLayout();
        $array = array();
        $row = $i = $j = 0;
        foreach ($layout as $control) {
            if ($control->checkAccess()) {
                $label = $hint = NULL;
                $pos = (($i == 0) ? ' firstCell' : (($i == $j) ? ' lastCell' : '')) . ' cell' . $i;
                if (!($control instanceof MContainer)) {
                    if ($this->isShowLabel()) {
                        $label = $control->generateLabel();
                    }
                    $hint = $control->generateHint();
                }
                if ($this->labelMode == MFieldLabel::LABEL_ABOVE) {
                    $div = new MDiv($id . '_row' . $i, array($label, $control->generate(), $hint), 'mContainerRow' . $pos);
                } else { // MFieldLabel::LABEL_SIDE
                    $div = new MHContainer($id . '_row' . $i, array($label, $control->generate() . $hint));
                }
                $height = $control->getRowHeight();
                if ($height != '') {
                    $div->setHeight($height);
                }
                $array[] = $div;
                $i++;
            }
        }
        $div = new MDiv($id, $array, 'mVContainer');
        $div->cloneStyle($this);
        $div->setClass('mVContainer');
        return array($div, $hidden);
    }

    public function generateLayout() {
        if ($this->property->disposition == MContainer::DISPOSITION_VERTICAL) {
            return $this->generateLayoutVertical();
        } else {
            return $this->generateLayoutHorizontal();
        }
    }

    public function generateInner() {
        $this->inner = $this->generateLayout();
    }

    public function generate() {
        $this->generateInner();
        $this->generateEvent();
        return $this->getInnerToString();
    }

}

?>
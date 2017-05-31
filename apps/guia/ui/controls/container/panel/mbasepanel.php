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

class MBasePanel extends MContainer {

    protected $box;

    public function onCreate() {
        parent::onCreate();
        $this->setBox();
        $this->setClass('mPanelBody');
        $this->setDisposition(MContainer::DISPOSITION_HORIZONTAL);
    }

    public function init($id = '', $caption = '', $close = '') {
        parent::init($id);
        $this->setTitle($caption);
        $this->setClose($close);
    }

    public function setBox($caption = '', $close = '', $icon = '') {
        $this->box = new MBox($caption, $close, $icon);
    }

    public function setTitle($title) {
        $this->box->setCaption($title);
    }

    public function setIcon($icon) {
        $this->box->boxTitle->setIcon($icon);
    }

    public function setClose($close) {
        $this->box->setClose($close);
    }

    public function add($control, $width = '', $float = 'left', $class = '') {
        if (is_array($control)) {
            foreach ($control as $c) {
                $this->add($c, $width, $float);
            }
        } else {
            $cell = ($control instanceof MDiv) ? $control : new MDiv('', $control);
            $cell->setClass($class . ' ' . 'mPanelCellBox mPanelCell' . ucfirst($float));
            parent::addControl($cell);
        }
    }

    public function insert($pos, $control, $width = '', $float = 'left', $class = '') {
        $cell = new MDiv('', $control, 'mPanelCellBox mPanelCell' . ucfirst($float) . ' ' . $class);
        parent::insertControl($cell, $pos);
    }

    public function generate() {
        $body = new MDiv($this->id, $this->getControls(), 'mPanelBody');
        $this->box->setControls(array($body));
        return $this->box->generate();
    }

}
?>
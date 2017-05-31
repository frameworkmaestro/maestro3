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

class MFieldLabel extends MControl {

    const LABEL_NONE = 0;
    const LABEL_SIDE = 1;
    const LABEL_ABOVE = 2;

    public function init($id = '', $text = NULL) {
        parent::init($id);
        $this->setText($text);
        $this->setMode(MFieldLabel::LABEL_SIDE);
    }

    public function setMode($mode) {
        $this->property->mode = $mode;
    }

    public function getMode() {
        return $this->property->mode;
    }

    public function setWidth($value) {
        if ($value) {
            if ((strpos($value, '%') === false) && (strpos($value, 'px') === false)) {
                $value = "{$value}%";
            }
            $this->property->width = $value;
        }
    }

    public function getWidth() {
        return $this->property->width;
    }

    public function setHelp($help) {
        $this->property->help = $help;
    }

    public function generateInner() {
        $this->setClass('mFieldLabel');
        $this->setRender('label');
        if ($this->property->width) {
            $this->addStyle('width', $this->property->width);
        }
        if ($this->property->help) {
            $id = $this->getId() . 'help';
            $this->page->addDojoRequire("dijit.Tooltip");
            $help = new MImage($id, '&nbsp;', 'managerIconButtonHelp');
            $this->text .= '  ' . $help->generate();
            $this->getPage()->onLoad("new dijit.Tooltip({connectId: [\"{$id}\"], label: \"{$this->property->help}\"});");
        }
        $this->inner = ( trim($this->text) != '' ) ? $this->render() : '';
    }

}

?>
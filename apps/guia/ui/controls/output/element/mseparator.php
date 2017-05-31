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

class MSeparator extends MDiv {
    /*
      private $margin;
      private $height;
      private $color;
     */

    public function init($text = NULL, $margin = '', $color = '', $height = '1px') {
        parent::init();
        $this->setText($text);
        $this->setColor($color);
        $this->setMargin($margin);
        $this->setHeight($height);
    }

    public function getMargin() {
        return $this->property->margin;
    }

    public function setMargin($margin) {
        $this->property->margin = $margin;
    }

    public function getHeight() {
        return $this->property->height;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function getColor() {
        return $this->property->color;
    }

    public function setColor($color) {
        $this->property->color = $color;
    }

    public function generateInner() {
        $this->setRender('hr');
        if ($margin = $this->getMargin()) {
            $this->addStyle('margin-top', "{$margin}");
        }
        if ($this->getText() instanceof MControl) {
            $this->setClass('mSeparator');
            $this->inner = $this->getText()->generate() . $this->render();
        } elseif (trim($this->getText()) != '') {
            $this->setClass('mSeparator');
            $text = new MLabel($this->getText(), $color);
            $this->inner = $text->generate() . $this->render();
        } else {
            $this->setClass('mHr', false);
        }
    }

}

?>
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

class MImage extends MFormControl {

    public $source;
    public $css;

    public function init($id = '', $label = '', $css = '', $source = '') {
        parent::init($id, '', $label);
        $this->source = $source;
        $this->css = $css;
    }

    public function onCreate() {
        parent::onCreate();
        $this->setRender('image');
    }

    public function setSource($value) {
        $this->source = $value;
    }

    public function getSource() {
        return $this->source;
    }

    public function setCSS($value) {
        $this->css = $value;
    }

    public function getCSS() {
        return $this->css;
    }

    public function generateInner() {
        if ($this->source == '') {
			$this->source = Manager::getAbsoluteURL('public/images/1x1px.png');
            if ($this->css == '') {
                $this->setClass('mNoImage');
                $this->setLabel('');
            } else {
                $this->setClass('mImageCSS ' . $this->css);
            }
        }
        $this->inner = $this->render();
    }

}

?>
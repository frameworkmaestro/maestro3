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

class MImageLinkLabel extends MImageLink {

    private $imageType = 'normal';

    public function setImageType($type = 'normal') {
        $this->imageType = $type;
    }

    public function onBeforeGenerate() {
        if ($this->imageType == 'icon') {
            $this->setClass('mImageIcon');
        } elseif ($this->getCSS()) {
            $this->setClass('mImageCentered');
        }
    }

    public function generateLink($text) {
        $spanClass = 'mImageLinkLabel';
        if ($this->imageType == 'normal') {
            $spanClass .= ' mImageLabel';
        }
        $span = new MSpan('label' . $this->getId(), $this->getLabel(), $spanClass);
        if ($this->imageType == 'icon') {
            $span->setClass('mImageLinkLabelIcon');
            $text .= $span->generate();
        } else {
            $text .= $span->generate();
            if ($this->getSource()) {
                $text = new MDiv('', $text);
                $text->setClass('mImageSource');
            }
        }
        return new MLink('link' . $this->getId(), '', $this->getAction(), $text, $this->getTarget());
    }

}

?>
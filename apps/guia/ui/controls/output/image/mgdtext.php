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

class MGDText extends MText {

    public $ttfFont;
    public $key;
    public $location;
    public $size;
    public $foreColor;
    public $backColor;

    public function __construct($name = '', $text = 'default', $size = 0, $fColor = array(0, 0, 0), $bColor = array(255, 255, 255), $ttfFont = 'arial', $bold = false) {
        parent::__construct($name, $text, '', $color, NULL, $bold);
        $this->ttfFont = $ttfFont;
        $this->size = $size;
        $this->foreColor = $fColor;
        $this->backColor = $bColor;
        $this->manager->conf->loadConf('', $this->manager->getConf('home.classes') . '/etc/gdtext.xml');
        $this->key = $this->manager->getConf("gdtext.key");
    }

    public function generateInner() {
        $qs = 'key=' . $this->key;
        $qs .= '&text=' . $this->value;
        $qs .= '&size=' . $this->size;
        $qs .= '&fcolor=' . implode(',', $this->foreColor);
        $qs .= '&bcolor=' . implode(',', $this->backColor);
        $qs .= '&font=' . $this->ttfFont;
        $qs = 'qs=' . base64_encode($qs);
        $a = $this->location = $this->manager->getConf('home.url') . '/gdtext.php?' . $qs;
        $this->inner = $this->getRender('image');
    }

}

?>
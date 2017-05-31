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

Manager::import('core::classes::extensions::geshi::geshi', 'GeSHi');

class MSyntax extends MOutputControl {

    public function init($name = NULL, $text = NULL, $language = 'php', $line = '') {
        parent::init($name);
        $this->setText($text);
        $this->setLanguage($language);
        $this->setLine($line);
    }

    public function setLanguage($value) {
        $this->property->language = $value;
    }

    public function getLanguage() {
        return $this->property->language;
    }

    public function setLine($value) {
        $this->property->line = $value;
    }

    public function getLine() {
        return $this->property->line;
    }

    public function onCreate() {
        parent::onCreate();
        $this->setRender('text');
    }

    public function generateInner() {
        $css = Manager::getAbsolutePath('core/classes/extensions/geshi/geshi.css');
        Manager::getPage()->addStyleSheet($css);
        $cssCustom = Manager::getAppPath('public/css/geshiCustom.css');
        if (file_exists($cssCustom)){
            Manager::getPage()->addStyleSheet($cssCustom);
        }
        $source = $this->getText();
        $language = $this->getLanguage();
        $geshi = new GeSHi($source, $language);
        $this->setText($geshi->parse_code());
        $this->inner = $this->render();
    }

    public function sanitize($value)
    {
        return $value;
    }
}

?>
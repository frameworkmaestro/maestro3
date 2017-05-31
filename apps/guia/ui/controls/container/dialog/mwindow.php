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

class MWindow extends MControl {

    public $text;
    public $url;
    public $link;

    public function init($id, $url, $text='', $options = array()) {
        parent::init($id);
        $this->url = $url;
        $this->text = $text;
        if (count($options)) {
            foreach ($options as $option => $value) {
                $this->{$option} = $value;
            }
        }
    }

    public function setStatusBar($control) {
        
    }
    
    public function open() {
        return "!manager.getWindow('{$this->getId()}').open();";
    }

    public function close() {
        return "!manager.getWindow('{$this->getId()}').close();";
    }

    public function getLink($modal = false, $reload = false, $inset = false, $params = array()) {
        $this->link = Manager::getUI()->getWindow("{$this->id}", $modal, $reload);
        return $this->link;
    }

    public function generate(){
        $id = $this->getId();
        $url = MAction::getHref($this->url);
        $onload = <<< HERE
var {$id} = manager.addWindow('{$id}');
manager.getWindow('{$id}').setHref('{$url}');

HERE;

        $this->page->addJsCode($onload);
        return '';
    }

}

?>
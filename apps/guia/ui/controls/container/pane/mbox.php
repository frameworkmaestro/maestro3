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

class MBox extends MContainerControl {

    public $close;
    public $icon;
    public $actionBar;
    public $toolBar;
    public $extendClass;
    public $caption;

    public function __construct($caption = '', $close = '', $icon = ''){
        parent::__construct();
        $this->caption = $caption;
        $this->actionBar = new MContainerControl();
        $this->toolBar = new MSimpleToolBar('toolBar' . uniqid());
        $this->close = $close;
        $this->extendClass = array('title' => '', 'content' => '', 'action' => '');
    }
    
    public function onCreate() {
        parent::onCreate();
        $this->setRender('box');
    }    
    
    public function setClose($close) {
        $this->close = $close;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
    }

    public function setCaption($caption) {
        $this->caption = $caption;
    }

    public function getCaption() {
        return $this->caption;
    }

    public function setText($text) {
        $this->setCaption($text);
    }

    public function addAction($action) {
        $this->actionBar->addControl($action);
    }

    public function addTool($title = '', $action = '', $icon = '') {
        $this->toolBar->addItem($title, $action, $icon);
    }

    public function setExtendClass($title = '', $content = '', $action = '') {
        $this->extendClass['title'] = $title;
        $this->extendClass['content'] = $content;
        $this->extendClass['action'] = $action;
    }
    
    function onGenerate() {
        if ($this->getEnabled()) {
            $this->generateInner();
            $this->generateEvent();
            $this->result = $this->render();
            $this->inner = $this->result;
        }
    }

    public function generateInner(){
        if ($this->close){
            $this->toolBar->addItem('Fechar', $this->close, 'close');
        }
    }
}

?>
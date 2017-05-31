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

class MToolBarItem extends MDiv {

    public $label;
    public $icon;
    public $action;

    public function __construct($title = '', $action = '', $icon = '') {
        parent::__construct();
        $this->setHTMLTitle($title);
        $this->icon = $icon;
        $this->action = $action;
    }

    public function onCreate() {
        parent::onCreate();
        $this->setId('tb'.uniqid());
        $this->setRender('toolbaritem');
    }

    public function generate() {
        $onclick = MAction::getOnClick($this->action, $this->property->id);
        $this->addEvent('click', $onclick);
        $this->setContent($this->label);
        return parent::generate();
    }

}

?>
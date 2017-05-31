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

class MToolIcon extends MFormControl {

    public $icon;
    public $action;

    public function init($icon, $text = '', $action = 'SUBMIT') {
        parent::init();
        $this->action = $action;
        $this->icon = $icon;
        $this->text = $text;
    }

    public function setIcon($iconClass) {
        $this->icon = $iconClass;
    }

    public function generate() {
        $icon = new MImage('', '', 'managerIcon managerIconCursor managerIcon' . ucfirst($this->icon));
        if ($this->text) {
            $text = new MSpan('', $this->text, 'managerIconText managerIconCursor');
        }
        $div = new MDiv($this->id, array($icon, $text), 'mToolIcon');


        $action = MAction::getOnClick($this->action, $div->id);
        $div->addEvent('click', $action);
        if ($this->title) {
            $div->setHTMLTitle($this->title);
        }
        return $div->generate();
    }

}

?>
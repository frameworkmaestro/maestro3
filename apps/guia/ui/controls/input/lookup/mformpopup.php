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

class MFormPopup extends MLookupField {

    public $popupName;

    public function onAfterCreate() {
        $this->popupName = "popup" . ucfirst($this->name);
    }

    public function generateInner() {
        $js = "!{$this->popupName}.start();";
        $text = $this->getText();
        if($text == '' || !$text){
            $button = new MButtonIcon($js);
            $button->setIcon('managerIconButtonNew');
        }
        else if($text == 'hidden'){
            $button = new MLabel('');
        }
        else{
            $button = new MButton('', $text, $js);        
        }
        $html = $button->generate();
        $action = MAction::getHref($this->action);
        $jsCode = <<< HERE
        {$this->popupName}.setContext({
             name    : '{$this->popupName}',
             action  : '{$action}',
             related : '{$this->related}',
             filter  : '{$this->filter}',
             field   : '{$this->name}'
        });
HERE;

        $this->page->addJsCode("{$this->popupName} = new Manager.FormPopup();");
        $this->page->addJsCode($jsCode);
        $this->inner = new MDiv('', $html, '');
    }

}

?>

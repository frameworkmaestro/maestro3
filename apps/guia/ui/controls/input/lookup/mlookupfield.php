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

class MLookupField extends MTextField {

    public $lookupName;
    public $action;
    // context
    public $filter;
    public $related;
    public $autocomplete = false;
    //compatibilidade
    public $item;
    public $module;

    public function init($name = '', $value = '', $label = '', $action = '', $related = '', $filter = '', $autocomplete = false, $hint = '') {
        parent::init($name, $value, $label, 0, $hint);
        $this->setAction($action);
        $this->setContext($filter, $related, $autocomplete);
    }

    public function onCreate() {
        parent::onCreate();
    }

    public function onAfterCreate() {
        $this->lookupName = "lookup" . ucfirst(str_replace(':', '', $this->name));
    }

    public function setName($name) {
        $this->name = str_replace(array(':','[',']'), '', $name);
        $this->lookupName = "lookup" . ucfirst($this->name);
        parent::setName($this->name);
    }
    
    public function setContext($filter, $related, $autocomplete = false) {
        $this->setRelated($related);
        $this->setFilter($filter);
        $this->setAutocomplete($autocomplete);
    }

    public function setRelated($related) {
        if (is_array($related)) {
            $related = implode(',', $related);
        }
        $this->related = str_replace(' ', '', $related);
    }

    public function setFilter($filter) {
        if (is_array($filter)) {
            $filter = implode(',', $filter);
        }
        $this->filter = str_replace(' ', '', $filter);
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function setAutoComplete($autocomplete = true) {
        $this->autocomplete = $autocomplete;
    }

    public function setItem($value) {
        $this->item = $value;
    }

    public function setModule($value) {
        $this->module = $value;
    }

    public function setIdHidden($value) {
        $this->idHidden = $value;
    }

    public function generateInner() {
        $this->label = $this->label ? '&nbsp;' : '';
        if (!$this->readonly) {
            $button = new MButtonFind("!{$this->lookupName}.start();");
            $action = MAction::getHref($this->action);

            $jsCode = <<< HERE
        {$this->lookupName}.setContext({
             name    : '{$this->lookupName}',
             action  : '{$action}',
             related : '{$this->related}',
             filter  : '{$this->filter}',
             form    : '{$this->getFormId()}',
             field   : '{$this->name}',
             autocomplete : '{$this->autocomplete}'
        });
HERE;

            $this->page->addJsCode("{$this->lookupName} = new Manager.Lookup();");
            $this->page->addJsCode($jsCode);
        } else {
            $button = new MButtonFind('!return false;');
        }
//        $content[] = $button->generate();
//        $html = $this->painter->generateToString($content);
//        $this->inner = new MDiv('', $html, '');
        $this->inner = $button;
    }

}

?>

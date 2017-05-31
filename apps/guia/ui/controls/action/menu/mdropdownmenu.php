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

/**
 * Menus em MenuBar ou Menus independentes (para navegação)
 */

class MDropDownMenu extends MMenuList {

    public function onCreate() {
        parent::onCreate();
        $this->setRender('dropdownmenu');
    }
    
    public function generateInner() {
        if ($this->inner == '') {
            if ($this->controls->hasItems()) {
                if ($this->useStore) {
                    $this->page->addDojoRequire("dojo/store/Memory");
                    $controls = $this->controls->items;
                    $jsData = '[';
                    if (count($controls)) {
                        $firstRowAdded = false;
                        foreach ($controls as $control) {
                            $this->inner[] = $control->generate();

                            if ($firstRowAdded)
                                $jsData .= ",";

                            $jsData .= "{id:\"{$control->getId()}\",action:\"{$control->action}\"}\n";
                            $firstRowAdded = true;
                        }
                    }
                    $jsData .= "]";
                    $jsCode = $this->getId() . "Store = new dojo.store.Memory({ data: {$jsData}});";
                    $this->page->addJsCode($jsCode);
                } else {
                    $this->inner = $this->controls->items;
                }
            }
        }
    }

}

?>
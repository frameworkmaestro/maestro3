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
 * MScripts Class.
 * An auxiliary class to MPage to handle Javascript scripts.
 */
class MScripts extends MComponent {

    public $form;
    public $scripts;
    public $customScripts;
    public $onload;
    public $onsubmit;
    public $onunload;
    public $onfocus;
    public $onerror;
    public $jsCode;
    public $dojoRequire;
    public $events;

    public function __construct($form) {
        parent::__construct();
        $this->form = $form;
        $this->onsubmit = array();
        $this->onload = new MStringList(false);
        $this->onerror = new MStringList(false);
        $this->onunload = new MStringList();
        $this->onfocus = new MStringList();
        $this->jsCode = new MStringList(false);
        $this->scripts = new MStringList(false);
        $this->customScripts = new MStringList(false);
        $this->dojoRequire = array();
        $this->events = new MStringList(false);
    }

    public function addScript($url, $module = null) {
        $url = Manager::getAbsoluteURL("public/scripts/{$url}", $module);
        if ($this->scripts->find($url) === false) {
            $this->scripts->add($url);
        }
    }

    public function addScriptURL($url) {
        if ($this->scripts->find($url) === false)
            $this->scripts->add($url);
    }

    public function insertScript($url) {
        $url = Manager::getAbsoluteURL('html/scripts/' . $url);
        $this->scripts->insert($url);
    }

    public function addOnSubmit($jsCode, $formId) {
        if (!$this->onsubmit[$formId]) {
            $this->onsubmit[$formId] = new MStringList();
        }
        $this->onsubmit[$formId]->add($jsCode);
    }

    public function addDojoRequire($dojoModule) {
        $this->dojoRequire[$dojoModule] = $dojoModule;
    }

    public static function tag($content) {
        return "<script type=\"text/javascript\">{$content}</script>\n";
    }

    public function getArray() {
        $events = $this->events->getValueText('', ",\n");
        if ($events != '') {
            $this->onload->add("manager.registerEvents([\n " . $events . "\n]);");
        }

        $scripts[0] = $this->scripts->getTextByTemplate("<script type=\"text/javascript\" src=\"/:v/\"></script>\n");
        if (count($this->dojoRequire)) {
            $i = 0;
            foreach ($this->dojoRequire as $module) {
                $moduleList .= ($i++ ? ',' : '') . "\"{$module}\"";
            }
            $scripts[1] = "require([" . $moduleList . "]);\n";
        }
        $scripts[1] .= $this->jsCode->getValueText('', "\n");
        $scripts[2] = ($onload = $this->onload->getValueText('', "\n    ")) ? "    {$onload}" : '';
        $onsubmit = '';
        if (count($this->onsubmit)) {
            foreach ($this->onsubmit as $formId => $list) {
                $onsubmit .= "manager.onSubmit[\"{$formId}\"] = function() { \n" .
                        "    form = manager.byId(\"{$formId}\");\n " . $list->getValueText('', " \n    ") .
                        "    return result;\n};\n";
            }
        }
        $scripts[3] = $onsubmit;
        $scripts[4] = ($onerror = $this->onerror->getValueText('', "\n    ")) ? "{$onerror}" : '';

        return $scripts;
    }

    public function generate($id) {
        $isAjax = Manager::isAjaxCall();
        $scripts = $this->getArray();
        $hasCode = $scripts[0] . $scripts[1] . $scripts[2] . $scripts[3] . $scripts[4];
        if ($hasCode != '') {
            $code = "";

            if ($scripts[0] != '') {
                $code .= <<< HERE
$scripts[0]
                    
HERE;
            }
            $code .= "\n<script type=\"text/javascript\">\n";

            if ($scripts[1] != '') {
                $code .= <<< HERE
$scripts[1]

HERE;
            }

            if ($isAjax) {
                if (Manager::isAjaxEvent()) {
                    $code .= <<< HERE
{$scripts[2]}

HERE;
                } else {
                    $code .= <<< HERE
manager.onLoad["{$id}"] = function() {
    console.log("inside onload {$id}");
{$scripts[2]}
};
HERE;
                }
            } else {
                $code .= <<< HERE
require(["dojo/parser", "dojo/ready"], function(parser, ready){
  ready(function(){
    console.log("inside onload {$id}");
{$scripts[2]}
  });
});   

HERE;
            }
            $code .= <<< HERE
{$scripts[3]}
{$scripts[4]}
HERE;
            $code .= <<< HERE
//-->
</script>

HERE;
            return "<div id=\"{$id}\" class=\"mScripts\">{$code}</div>";
//            return $code;
        } else {
            return '';
        }
    }

}

?>
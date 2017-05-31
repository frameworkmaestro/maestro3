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

class mEditor extends MTextField {
    
    public function onCreate() {
        parent::onCreate();
		$this->setName($this->getId());

        $this->setDojoType('dijit.Editor');
        $this->page->addDojoRequire('dijit.Editor');
        $this->page->addDojoRequire("dijit.Toolbar");
        $this->page->addDojoRequire("dijit._editor.plugins.FontChoice");  // 'fontName','fontSize','formatBlock'
        $this->page->addDojoRequire("dijit._editor.plugins.TextColor");
        $this->page->addDojoRequire('dojox.editor.plugins.PasteFromWord');
        $this->page->addDojoRequire("dojox.editor.plugins.TablePlugins");
        $this->page->addDojoRequire("dojox.editor.plugins.ResizeTableColumn");
        

        $this->attributes->dojoProps->dojoProps["extraPlugins"] = "@[\"PasteFromWord\"]";
       
        $this->attributes->dojoProps->dojoProps["plugins"] = "@[\"cut\",\"copy\"
            ,\"paste\",\"|\",\"bold\",\"italic\",\"underline\",\"strikethrough\"
            ,\"subscript\",\"superscript\",\"|\", \"indent\", \"outdent\"
            ,\"justifyLeft\", \"justifyCenter\", \"justifyRight\",\"|\",
            {name:\"dijit._editor.plugins.FontChoice\", command:\"fontName\", generic:true},
            {name: \"insertTable\"},
            {name: \"modifyTable\"},
            {name: \"insertTableRowBefore\"},
            {name: \"insertTableRowAfter\"},
            {name: \"insertTableColumnBefore\"},
            {name: \"insertTableColumnAfter\"},
            {name: \"deleteTableRow\"},
            {name: \"deleteTableColumn\"},
            {name: \"colorTableCell\"},
            {name: \"tableContextMenu\"}]";


        $urlCSS = Manager::getAbsoluteURL('public/scripts/dojox/editor/plugins/resources/editorPlugins.css');
        $this->page->addJsCode("dojo.create(\"link\", {href:'{$urlCSS}', type:'text/css', rel:'stylesheet'}, document.getElementsByTagName('head')[0]);");

        $urlPasteFromWord = Manager::getAbsoluteURL('public/scripts/dojox/editor/plugins/resources/css/PasteFromWord.css');
        $this->page->addJsCode("dojo.create(\"link\", {href:'{$urlPasteFromWord}', type:'text/css', rel:'stylesheet'}, document.getElementsByTagName('head')[0]);");

        $this->setRender('div');
    }
	
    public function setHeight($value = '300px'){
        $this->setAttribute('height', $value);
    }
    
	public function setValue($value){
		if ($value){
			$this->setAttribute('value', str_replace( '&nbsp;', ' ', htmlentities($value)));
		}
    }

    public function generateInner() {
        $this->form->onSubmit("(dojo.byId(\"{$this->getId()}\").value = dijit.byId(\"{$this->getId()}_iframe\").getValue())");
        $hidden = new MHiddenField($this->getId(), $this->getValue());
        $this->setId($this->getId() . '_iframe');
        
        $this->inner = $hidden->generate() . $this->render();
    }

}

?>
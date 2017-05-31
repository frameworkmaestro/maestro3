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

class MTransferBox extends MFormControl {

    protected $list;    

    public function init($id = '', $value = '', $label = '', $list = array(), $hint = '') {
        parent::init($id, $value, $label, $hint);
        $this->list = $list;
    }

    public function setList($value) {
        $this->list = $value;
    }

    public function getList() {
        return $this->list;
    }

    public function setCaption($value) {
        $this->property->caption = $value;
    }

    public function getCaption() {
        return $this->property->caption;
    }

    public function setSort($value){
        $this->sort = $value;
    }
    
    public function onCreate() {
        parent::onCreate();
        $this->setRender('div');
        if (is_null($this->sort))
            $this->setSort(true);
        
    }

    public function generateInner() {
		$this->page->addDojoRequire('manager.TransferBox');
        $id = $this->getId();
        $idDiv = $id . '_div';
        $isReadOnly = $this->getReadOnly() ? 'none' : 'inline-block';
        // array Javascript com a lista
        $idListData = $id . '_data';
        $listData = "var {$idListData} = [];\n";
        if (!is_array($this->list)) {
            $this->list = array('0' => $this->list);
        }
        $i = 0;
        foreach ($this->list as $index => $value) {
            $listData .= "{$idListData}[{$i}] = { id: '{$index}', name: '{$value}', value: '{$value}' };\n";
            $i++;
        }
        // array Javascript com a lista de valores correntes
        $idListValue = $id . '_value';
        $listValue = "var {$idListValue} = [];\n";
        if (!is_array($this->value)) {
            if (!is_null($this->value)) {
                $this->value = array('0' => $this->value);
            }
        }
        if (is_array($this->value)) {
            $i = 0;
            foreach ($this->value as $index => $value) {
                $listValue .= "{$idListValue}[{$i}] = '{$index}';\n";
                $i++;
            }
        }
        $uniq = "store" . uniqid();
        // instancia MTransferBox
        $sort = $this->sort ? "name" : "";
        $code = <<< HERE
		var {$id}_transferbox;
		require([
			"manager/TransferBox",
			"dojo/store/Memory",
			"dojo/store/Observable",
                        "dojo/dom-construct",
			"dojo/domReady!"
		], function(TransferBox, Memory, Observable, domConstruct){
            
                    var l = dojo.query("input[type='hidden']");
                    l.forEach(function(item){
                        if (item.id.search('{$id}') > -1){
                            domConstruct.destroy(item.id);
                        }
                    });
                    {$listData}
                    {$listValue}
                    var {$uniq} = new Memory({	identifier: "id", data: {$idListData} });
                    {$uniq} = Observable({$uniq});                    
                    {$id}_transferbox{$uniq} = new TransferBox({store: {$uniq}, value: {$idListValue}, idHidden:'{$id}', sortProperty:'{$sort}'}, "{$idDiv}");
		});
        
HERE;

        $this->page->onLoad($code);
        if (!$this->width) {
            $this->width = '200px';
        }
        if (!$this->height) {
            $this->height = '150px';
        }
        $css = "#{$idDiv} .dgrid {width: {$this->width}; height: {$this->height}; display: inline-block; vertical-align: middle;} " .
                "#{$idDiv} .buttons {width: 27px; display: {$isReadOnly};}";
        Manager::getPage()->addStyleSheetCode($css);
        $controls = array(new MDiv($idDiv));
        $group = new MBaseGroup($id . 'BaseGroup', $this->getCaption(), $controls);
        $this->inner = $group;
    }

}

?>
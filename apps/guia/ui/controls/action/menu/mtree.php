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

class MTree extends MControl {

    static $order = 0;
    private $nOrder;
    private $template;
    private $action;
    private $target;
    private $items;
    private $jsItems;
    private $arrayItems;
    private $selectEvent = "";
    private $jsId;

    public function init($id = '', $template = '', $action = '', $target = '_blank') {
        parent::init($id);
        $this->template = $template;
        $this->action = $action;
        $this->target = $target;
        $this->jsId = 'tree' . ucfirst($this->getId());
    }

    public function onCreate() {
        parent::onCreate();
        $this->jsId = 'tree' . ucfirst($this->getId());
        $this->items = NULL;
        $this->page->addDojoRequire("dojo.data.ItemFileReadStore");
        $this->page->addDojoRequire("dijit.tree.ForestStoreModel");
        $this->page->addDojoRequire("dijit.Tree");
        $this->selectEvent = '';
        $this->nOrder = MTree::$order++;
        $this->property->items = array();
        $this->property->key = '3';
        $this->property->data = '0,1,2';
    }

    public function setItems($value) {
        $this->property->items = $value;
    }

    public function getItems() {
        return $this->property->items;
    }

    public function setKey($value) {
        $this->property->key = $value;
    }

    public function getKey() {
        return $this->property->key;
    }

    public function setData($value) {
        $this->property->data = $value;
    }

    public function getData() {
        return $this->property->data;
    }

    private function getJsItems($items) {
        if ($items != NULL) {
            foreach ($items as $it) {
                $i .= ( $i != '' ? ',' : '') . "{description:'{$it[1]}',";
                $i .= "id: " . ($it[0] !== NULL ? "'{$it[0]}'" : ',0');
                $action = ($it[2] !== NULL ? MAction::getOnClick($it[2]) : '');
                $i .= ",action: '{$action}'";

                if (count($this->items[(int) $it[0]])) {
                    $i .= ", children: [" . $this->getJsItems($this->items[(int) $it[0]]) . "]";
                }
                $i .= "}";
            }

            return $i;
        }
    }

    public function setItemsFromArray($array, $key = '3', $data = '0,1,2') {
        $this->arrayItems = array();
        foreach ($array as $a) {
            $this->arrayItems[$a[0]] = $a;
        }

        $tree = MUtil::arrayTree($array, $key, $data);
        $this->items = $tree;
        $this->jsItems = "identifier: 'id', label: 'description', items: [" . $this->getJsItems($tree['root']) . "]";
    }

    public function setItemsFromResult($result, $basename, $key = '0', $data = '1') {
        // for while, only for bi-dimensional results
        // column 0 - key used to group data
        // column 1 - data
        $otree = MUtil::arrayTree($result, $key, $data);
        $this->items['root'][] = array(0, $basename, '');
        $i = 0;
        foreach ($otree as $key => $tree) {
            $this->items[0][] = array(++$i, $key, '');
            $j = $i;
            foreach ($tree as $t) {
                $this->items[$j][] = array(++$i, $t[0], '');
            }
        }
        $this->jsItems = "identifier: 'id', label: 'description', items: [" . $this->getJsItems($this->items['root']) . "]";
    }

    public function getArrayItems() {
        return $this->arrayItems;
    }

    public function setSelectEvent($jsCode) {
        $this->selectEvent .= $jsCode;
    }

    public function setAction($action) {
        $action = MAction::getOnClick($action, $this->jsId);
        $action = str_replace("#0#", '" + item.id + "', $action);
        $this->selectEvent .= "console.log(item);" . $action . ";\n";
    }

    public function setEventHandler($eventHandler = '') {
        $form = $this->page->getFormId();
        if ($eventHandler == '') {
            $eventHandler = $this->name . '_click';
        }
        $this->selectEvent .= "manager.doPostBack('{$eventHandler}', item.id,'{$form}');\n";
    }

    public function getIconClass() {
        $code = "function {$this->formId}_{$this->name}_getIconClass(item,opened) {\n" .
                "    var cls = (!item || this.model.mayHaveChildren(item)) ? opened ? 'dijitFolderOpened':'dijitFolderClosed' : 'dijitLeaf';\n" .
                "    return cls + this.layout;\n}\n";
        return $code;
    }

    public function getOnClick() {
        $code = "function {$this->formId}_{$this->name}_onClick(item,node) {\n" .
                $this->selectEvent .
                "\n}\n";
        return $code;
    }

    public function generateInner() {
        if ($this->items == NULL) {
            $this->setItemsFromArray($this->getItems(), $this->getKey(), $this->getData());
        }
        $tree = $this->nOrder;
        if ($this->selectEvent != '') {
            $selectEvent = $this->selectEvent;
        } else {
            $selectEvent = "var eventHandler = new Function('event',item.action); eventHandler.call();";
        }

        $code = <<< HERE
{$this->jsId}Object = {
    model: new dijit.tree.ForestStoreModel({rootLabel: 'BaseControl', store: new dojo.data.ItemFileReadStore({data: { $this->jsItems }})}),
    getIconClass: function (item,opened) {
        var cls = (!item || this.model.mayHaveChildren(item)) ? opened ? 'dijitFolderOpened':'dijitFolderClosed' : 'dijitLeaf';
        return cls + this.layout;
    },
    onClick: function (item,node) {
        {$selectEvent}
    },
    layout:'{$this->template}',
    showRoot:false
}    

HERE;

        $this->page->addJsCode($code);
        $onload = <<< HERE
new dijit.Tree({$this->jsId}Object,dojo.byId('{$this->jsId}'));
HERE;

        $this->page->onload($onload);
        $this->inner = new MDiv("{$this->jsId}");
    }

}

?>
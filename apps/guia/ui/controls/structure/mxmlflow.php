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

class MXMLFlow {

    public $root;
    public $params;

    public function __construct() {
        
    }

    public function loadFile($xmlFile, $params = NULL) {
        libxml_use_internal_errors(true);
        $this->root = simplexml_load_file($xmlFile);
        if (!$this->root) {
            foreach (libxml_get_errors() as $error) {
                //mdump($error);
            }
            libxml_clear_errors();
        }
        $this->params = $params;
    }

    public function loadString($xmlString, $params = NULL) {
        $this->root = simplexml_load_string($xmlString);
        $this->params = $params;
    }

    public function get($nodeName) {
        $node = $this->getNodesFromDOM($this->root->$nodeName);
        return $node;
    }

    private function getNodesFromDOM($node) {
        $actions = array();
        if ($node) {
            foreach ($node->children() as $class => $n) {
                $action = new MDataObject;
                $actions[] = $action;
                $this->getNodeFromDOM($action, $class, $n);
            }
        }
        return $actions;
    }

    private function getNodeFromDOM($action, $c, $node) {
        $params = $this->params;
        $flow = $this->params->__flow;
        foreach ($node->attributes() as $k => $v) {
            $v = utf8_decode((string) $v);
            if ($k == "method") {
                $v = substr(substr($v, 2), 0, -1);
                eval("{$v};");
            } else if (substr($v, 0, 1) == '$') {
                $v = substr(substr($v, 2), 0, -1);
                $action->$k = eval('return ' . $v . ';');
            } else {
                $action->$k = $v;
            }
        }
        $i = 0;
        foreach ($node->children() as $f => $v) {
            $obj = new MDataObject;
            $this->getNodeFromDOM($obj, $f, $v);
            $action->flow[$i++] = $obj;
        }
    }

}

?>

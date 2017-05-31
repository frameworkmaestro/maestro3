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

class MAutoComplete extends MBusiness {

    public $module;
    public $item;
    public $value;
    public $config;
    public $sql;
    public $defaults;
    public $result;

    public function __construct($module, $item, $value, $defaults = null) {
        $this->value = $value;
        $this->module = $module;
        $this->item = $item;
        $this->defaults = $defaults;
        parent::__construct($module);

        if (( file_exists(Manager::getModulePath($module, 'db/lookup.class')) && Manager::uses('/db/lookup.class', $module) ) || Manager::uses('/classes/lookup.class', $module)) {
            eval("\$object = new Business{$module}Lookup();");
            eval("\$info   = \$object->autoComplete{$item}(\$this,\$defaults);");
            parent::__construct($this->module);

            if ($info) {
                $this->result = $info;
            } else {
                //faz consulta
                $sql = new Msql('');
                $sql->createFrom($this->sql);
                $sql->prepare($value);
                $db = Manager::getDatabase($this->module);
                //$this->sql = MSql::prepare($this->sql,$value);
                //$result = $this->_db->query($value ? $sql->command : str_replace('?','NULL',$this->sql));
                $result = $db->query($value ? $sql->command : str_replace('?', 'NULL', $this->sql));
                $this->result = $result[0];
            }
        }
    }

    public function getResult() {
        return $this->result;
    }

    public function setContext($config, $sql) {
        $this->config = $config;
        $this->module = $config;
        $this->sql = $sql;
    }

}

?>

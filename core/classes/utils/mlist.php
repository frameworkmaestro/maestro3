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

class MList {

    public $items;
    private $count;

    public function __construct() {
        $this->items = array();
        $this->count = 0;
    }

    public function add($item, $key = NULL) {
        if (is_null($key)) {
            $this->items[$this->count++] = $item;
        } else {
            $this->items[$key] = $item;
            ++$this->count;
        }
    }

    public function clear() {
        $this->items = array();
        $this->count = 0;
    }

    public function delete($key) {
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
            --$this->count;
        }
    }

    public function insert($item, $key = 0) {
        if (is_null($key)) {
            $key = 0;
        }
        if (is_numeric($key)) {
            if ($key < $this->count) {
                for ($i = ($this->count - 1); $i >= $key; $i--)
                    $this->items[$i + 1] = $this->items[$i];
            } else {
                $key = $this->count;
            }
        }

        $this->add($item, $key);
    }

    public function get($key) {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }
    }

    public function getItems() {
        return $this->items;
    }

    public function setItems($items) {
        $this->items = $items;
    }

    public function set($key, $item) {
        if (array_key_exists($key, $this->items)) {
            $this->items[$key] = $item;
        }
    }

    public function hasItems() {
        return ($this->count > 0);
    }

}

class MStringList extends MList {

    private $duplicates;

    public function __construct($duplicates = true) {
        parent::__construct();
        $this->duplicates = $duplicates;
    }

    public function add($item, $key = NULL) {
        $exists = $key ? $this->items[$key] : (array_search($item, $this->items) !== false); 
        if (!$exists || $this->duplicates) {
            parent::add($item, $key);
        }    
    }

    public function insert($item, $key = NULL) {
        $exists = $key ? $this->items[$key] : (array_search($item, $this->items) !== false); 
        if (!$exists || $this->duplicates) {
            parent::insert($item, $key);
        }    
    }

    public function addValue($name, $value) {
        $this->add($value, $name);
    }

    public function find($value) {
        return array_search($value, $this->items);
    }

    public function getText($separator = '=', $delimiter = ',', $prefix = '') {
        $s = '';
        foreach ($this->items as $name => $value) {
            $s .= ( ($s != '') ? $delimiter : '') . (($value != '') ? "{$prefix}$name{$separator}$value" : $name);
        }

        return $s;
    }

    public function getValueText($separator = '=', $delimiter = ',', $prefix = '') {
        $s = '';

        foreach ($this->items as $value) {
            $s .= ( ($s != '') ? $delimiter : '') . $prefix . $value;
        }

        return $s;
    }

    public function getTextByTemplate($template) {
        $s = '';

        foreach ($this->items as $name => $value) {
            $s .= str_replace('/:n/', $name, str_replace('/:v/', $value, $template));
        }

        return $s;
    }

}

class MObjectList extends MList {

}

?>

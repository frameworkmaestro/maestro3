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

class MGridAction extends MControl {

    public $grid; // grid which this action belongs to
    public $alt; // image alt
    public $value; // image/text label for on
    public $action; // link pattern - replaces
    public $enabled;
    public $control; // array of Controls
    public $type; // type of action (what control to render): 'text','icon','select'

    public function init($grid, $type, $alt, $value, $action, $enabled = true) {
        parent::init();
        $this->grid = $grid;
        $this->type = $type;
        $this->alt = $alt;
        $this->value = $value;
        $this->action = $action;
        $this->enabled = $enabled;
        $this->controls = array();
    }

    public function enable() {
        $this->enabled = true;
    }

    public function disable() {
        $this->enabled = false;
    }

    public function generateLink($row) {
        $index = $row[$this->grid->index];
        $action = preg_replace('/\$id/', $index, $this->action);
        $action = str_replace("%r%", $this->grid->currentRow, $action);
        $n = count($row);
        // substitute positional parameters

        if (preg_match('/%(.*)%/', $action, $matches)) {
            $r = $matches[0][1];
            if (is_object($row[$r])) {
                $row[$r] = $row[$r]->generate();
            }
            $action = preg_replace('/%(.*)%/', urlencode($row[$r]), $action);
        }

        if (preg_match_all('/#(.*?)#/', $action, $matches)) {
            foreach ($matches[1] as $r) {
                if (is_object($row[$r])) {
                    $row[$r] = $row[$r]->generate();
                }
                $action = preg_replace("/#{$r}#/", $row[$r], $action);
            }
        }

        return $action;
    }

    public function generateControl($i, $row) {
        $method = $this->type;
        $this->control[$i] = MGridActionControl::$method($this, $i, $row);
        return $this->control[$i];
    }

    public function setTarget($target) {
        $this->target = $target;
    }

}
?>
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

class MGridActionDetail extends MGridActionIcon {

    public function generateControl($i, $row) {
        $class = "mGridActionIconDetail";
        $row = $this->grid->data[$this->grid->currentRow];
        $n = count($row);

        $href = $this->href;
        if (preg_match('/%(.*)%/', $href, $matches)) {
            $value = preg_replace('/%(.*)%/', $row[$matches[0][1]], $href);
        }
        $href = str_replace("%r%", $this->grid->currentRow, $href);
        $hrefOn = str_replace("%s%", '1', $href);
        $hrefOff = str_replace("%s%", '0', $href);
        $controlOn = new MImage('', '', $this->path[true]);
        $controlOff = new MImage('', '', $this->path[false]);
        $controlOn->addAttribute('onclick', $hrefOn);
        $controlOn->setClass($class);
        $controlOff->addAttribute('onclick', $hrefOff);
        $controlOff->setClass($class);
        $this->control[$i] = new MDiv('', array($controlOn, $controlOff), 'detail');
        return $this->control[$i];
    }

}

?>
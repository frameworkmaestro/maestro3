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

class MBooleanField extends MCheckBox {

    public function setValue($value) {
        $this->value = (isset($value) && ($value != '0') ? '1' : '0');       
    }

    public function getValue() {
        return ($this->value != '0' ? '1' : '0');
    }

    public function generateInner() {
        $id = $this->id;
        $this->id = '___' . $id;
        $this->checked = ($this->value == '1');
        parent::generateInner();

        $hidden = new MHiddenField($id, $this->value);
        $this->addEvent('change', "dojo.byId('{$id}').value = dojo.byId('{$this->id}').checked ? '1' : '0';", false);
        $this->inner .= $hidden->generate();
    }

}

?>

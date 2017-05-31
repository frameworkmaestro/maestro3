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

class MLookupObject extends MLookupFieldValue {

    protected $object;
    protected $idHidden;

    public function onAfterCreate() {
        parent::onAfterCreate();
        $id = $this->getId();
        if (!$this->idHidden) {
            $this->idHidden = 'id' . ucfirst($id);
        }
        $this->setFilter($id);
    }

    public function getFieldValue() {
        return '';
    }

    public function getObjectId() {
        return '';
    }

    public function setValue($value = NULL) {
        if (is_object($value)) {
            $this->object = $value;
            $this->property->value = $this->getFieldValue();
        }
    }

    public function generateInner() {
        $hidden = new MHiddenField($this->idHidden, $this->getObjectId());
        parent::generateInner();
        $this->inner = array($hidden, $this->inner);
    }

}

?>
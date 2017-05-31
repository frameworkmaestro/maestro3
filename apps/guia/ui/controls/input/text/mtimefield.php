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

class MTimeField extends MTextField {
    /*
     *             constraints: {
      timePattern: 'HH:mm:ss',
      clickableIncrement: 'T00:15:00',
      visibleIncrement: 'T00:15:00',
      visibleRange: 'T01:00:00'
      }
     *
     */

    public function init($id = '', $value = '', $label = '', $size = 10, $hint = '') {
        parent::init($id, $value, $label, $size, $hint);
    }

    public function onCreate() {
        parent::onCreate();
        $this->overrideType = false;
        $this->setRender('inputtime');
    }

    public function setRange($rangeDate = array()) {
        $this->property->rangeDate = $rangeDate;
    }

    public function setIncrement($increment) {
        $this->property->increment = $increment;
    }

    public function setLockUserInput($lockUserInput){
     $this->property->lockUserInput = $lockUserInput; 
    }

    public function generateInner() {
        $this->value = 'T' . $this->value;
        parent::generateInner();
    }

}

?>
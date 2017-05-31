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

class MButton extends MActionControl {

    public $type;
    public $image;

    public function init($name = '', $value = '', $action = NULL, $image = NULL) {
        parent::init($name, $value);
        $this->setAction($action ? : 'submit');
        $this->image = $image;
    }

    public function onCreate(){
        parent::onCreate();
        $this->setRender('button');
        $this->setAction('submit');
    }

    public function setImage($iconClass) {
        $this->image = $iconClass;
    }

    public function generateButton() {
        if ($this->value == '') {
            $this->value = $this->text;
        }
        $action = $this->action;
        $this->type = ( strtoupper($action) == 'RESET' ) ? 'reset' : 'button';

        // if it has a event registered, it's not necessary calculate $onclick
        if (($this->hasEvent('click')) || ($this->getAjax() != null)) {
            return;
        }
        if ($this->type == 'reset') {
            return;
        }
        parent::generateAction();
    }

    public function generateInner() {
        if ($this->visible) {
            $this->generateButton();
            $this->inner = $this->render();
        }
    }

}

?>

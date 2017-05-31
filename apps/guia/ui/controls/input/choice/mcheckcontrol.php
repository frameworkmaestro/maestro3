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

class MCheckControl extends MInputControl {

    public $checked;
    public $type; // checkbox, radio, option

    public function init($id = '', $value = '', $label = '', $checked = false, $text = NULL) {
        parent::init($id, $value, $label);
        $this->checked = $checked;
        $this->setText($text);
    }

    public function onCreate() {
        parent::onCreate();
        $this->checked = false;
        $this->setText('');
        $this->setRender('inputcheck');
    }

    public function getType() {
        return $this->type;
    }

    public function setType($value) {
        $this->type = $value;
    }

    public function check($value) {
        $v = $this->value;
        $checked = ($v === 0) || ($v === '0') ? ($value == '0') : ((trim($v) == trim($value))/* && (trim($value) !== '') */);
        $this->setChecked($checked);
    }

    public function setChecked($checked) {
        if (!is_bool($checked)) {
            throw new EControlException(_M($this->className . ':: setChecked() - This method expects an boolean as parameter!'));
        }
        $this->checked = $checked;
    }

    public function generateInner() {
        if ($this->readonly) {
            return $this->text;
        }
        $this->inner = $this->render();
    }

}

?>
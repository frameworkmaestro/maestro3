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

/*
  A group of MOption controls
 */

class MOptionGroup extends MContainerControl {

    public $content;
    protected $showValues;

    public function init($id = '', $text = '', $options = array()) {
        parent::init($id, $options);
        $this->setText($text);
    }

    public function onCreate(){
        parent::onCreate();
        $this->setText('OptionGroup');
        $this->setRender('optiongroup');
    }

    public function setShowValues($show) {
        if (!is_bool($show)) {
            throw new EControlException(_M($this->className . ':: setShowValues() - This method expects an boolean as parameter!'));
        }
        $this->showValues = $show;
    }

    /**
     * Set controls from a simple associative array.
     * @param array $options
     */
    function setOptions($options) {
        foreach ($options as $value=>$label) {
            $key = trim($value);
            $option = new MOption($key, trim($value), $label);
            $this->addControl($option, $key);
        }
    }

    function getOption($value) {
        $key = trim($value);
        return $this->getControl($key);
    }

    public function generateOptions($value = '') {
        foreach ($this->getControls() as $o) {
            $o->setShowValues($this->showValues);
            $o->check($value);
            $this->content .= $o->generate();
        }
        $this->setClass('mCombo');
        return $this->render();
    }

}

?>
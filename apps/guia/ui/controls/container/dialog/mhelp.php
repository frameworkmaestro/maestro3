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

/**
 * Help dialogs.
 *
 */
class MHelp extends MInputControl {

    public $caption;
    public $message;
    public $buttons;
    public $box;
    public $close;

    public function init($caption = '', $message = '') {
        parent::init();
        $this->setCaption($caption ? : 'Help');
        $this->setMessage($message ? : '');
        $this->setId('help' . uniqid());
    }

    public function onLoad() {
        $this->setClose();
    }

    public function setCaption($caption) {
        $this->caption = $caption;
    }

    public function setClose($action = '') {
        $this->close = '!' . ($action ? MAction::getOnClick($action) : '') . "manager.byId('{$this->id}Help').hide();";
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setCDATA($value) {
        $this->setMessage($value);
    }

    /**
     * Adds a button to the help
     *
     * @param (string) $label Button label
     * @param (string) $href  Url address which will be open when the button is clicked
     * @param (string) $event A event which will be attached to the button
     */
    public function addButton($label, $action = '', $event = '') {
        $this->buttons[] = array($label, $action, $event);
    }

    public function generate() {
        $content = '';
        if (!is_array($this->message)) {
            $this->message = array($this->message);
        }
        $this->box = new MBox($this->caption, $this->close, '');

        $textBox = new MDiv($this->id . 'BoxText', $this->message);
        $this->box->setControls($textBox);
        $form = ($this->form instanceof MBaseForm) ? $this->form->getTagId() : '';
        $b = new MButton("{$this->id}HelpButton", 'Fechar',"!manager.byId('{$this->id}Help').hide();");
        $this->box->addAction($b);

        $help = new MDiv("{$this->id}Help", new MDiv("{$this->id}Box", $this->box, "mHelp"));
        $help->addStyle('display', 'none');
        $help->setDojoType('Manager.DialogSimple');

        return $help->generate();
    }

}
?>


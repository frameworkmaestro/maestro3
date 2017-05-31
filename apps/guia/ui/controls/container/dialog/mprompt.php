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
 * Prompt dialogs.
 * Implementation of the prompt class for generating common dialogs.
 *
 */
class MPrompt extends MInputControl {

    /**
     * Information type message
     */
    const MSG_TYPE_INFORMATION = 'information';

    /**
     * Error type message
     */
    const MSG_TYPE_ERROR = 'error';

    /**
     * Alert type message
     */
    const MSG_TYPE_ALERT = 'alert';

    /**
     * Confirmation type message
     */
    const MSG_TYPE_CONFIRMATION = 'confirmation';

    /**
     * Question type message
     */
    const MSG_TYPE_QUESTION = 'question';

    /**
     * Prompt type message
     */
    const MSG_TYPE_PROMPT = 'prompt';

    public $caption;
    public $message;
    public $buttons;
    public $box;
    public $close;
    public $defaultCaption;
    public $promptType;
    public $controls;
    public $action1;
    public $action2;

    /**
     * This is the constructor of the class.
     * Use the setType method to specify the type of the dialog.
     *
     * @see setType
     *
     * @param (string) $caption Title of the box
     * @param (string) $message Message for the prompt message
     * @param (string) $icon    css class icon
     *
     * @example
     * \code
     *     $dialog = new MPrompt('Information', 'Maestro is a nice framework :-)' );
     * \endcode
     *
     * @return (void)
     */
    public function onCreate() {
        parent::onCreate();
        $this->defaultCaption[MPrompt::MSG_TYPE_INFORMATION] = _M('Informação');
        $this->defaultCaption[MPrompt::MSG_TYPE_ERROR] = _M('Erro');
        $this->defaultCaption[MPrompt::MSG_TYPE_CONFIRMATION] = _M('Confirmação');
        $this->defaultCaption[MPrompt::MSG_TYPE_QUESTION] = _M('Questão');
        $this->defaultCaption[MPrompt::MSG_TYPE_ALERT] = _M('Alerta');
        $this->type = MPrompt::MSG_TYPE_PROMPT;
    }

    public function init($caption = '', $message = '') {
        parent::init();
        $this->setCaption($caption ? : _M('Alerta'));
        $this->setMessage($message ? : _M('Razão desconhecida.'));
        $this->setId('prompt' . uniqid());
    }

    public function onLoad() {
        $this->setClose();
    }

    public function __set($name, $value) {
        $property = strtolower($name);
        if ($property == 'type') {
            $this->setType($value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Sets the type of the message. Use the MPrompt::MSG_TYPE_??? constants as parameter
     *
     * @param (string) $type
     */
    public function setType($type = MPrompt::MSG_TYPE_INFORMATION) {
        $this->promptType = $type;
        $this->caption = $this->defaultCaption[$type];
    }

    public function setCaption($caption) {
        $this->caption = $caption;
    }

    public function setClose($action = '') {
        $this->close = '!' . ($action ? MAction::getOnClick($action) : '') . "manager.byId(\"$this->id\").hide();";
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setOk($action) {
        $this->addButton(_M('OK'), $action);
    }

    public function setCancel($action) {
        $this->addButton(_M('Cancelar'), $action);
    }

    public function setYes($action) {
        $this->addButton(_M('Sim'), $action);
    }

    public function setNo($action) {
        $this->addButton(_M('Não'), $action);
    }

    public function setAction1($action) {
        $this->action1 = $action;
    }

    public function setAction2($action) {
        $this->action2 = $action;
    }
    /**
     * Adds a button to the prompt dialog.
     *
     * @param (string) $label Button label
     * @param (string) $href  Url address which will be open when the button is clicked
     * @param (string) $event A event which will be attached to the button
     */
    public function addButton($label, $action = '', $event = '') {
        $this->buttons[] = array($label, $action, $event);
    }

    public static function error($msg = '', $action = '', $caption = '', $event = '') {
        if ($caption == '') {
            $caption = _M('Erro');
        }
        $prompt = new MPrompt($caption, $msg);
        $prompt->setType(MPrompt::MSG_TYPE_ERROR);
        $prompt->setClose($action);
        $prompt->setOk($action);
        $prompt->setAction1($action);
        $prompt->setAction2($action);
        return $prompt;
    }

    public static function information($msg, $action = '', $event = '') {
        $prompt = new MPrompt(_M('Informação'), $msg);
        $prompt->setType(MPrompt::MSG_TYPE_INFORMATION);
        $prompt->setClose($action);
        $prompt->setOk($action);
        $prompt->setAction1($action);
        $prompt->setAction2($action);
        return $prompt;
    }

    public static function alert($msg, $action = '', $event = '') {
        $prompt = new MPrompt(_M('Alerta'), $msg);
        $prompt->setType(MPrompt::MSG_TYPE_ALERT);
        $prompt->setClose($action);
        $prompt->setOk($action);
        $prompt->setAction1($action);
        $prompt->setAction2($action);
        return $prompt;
    }

    public static function confirmation($msg, $actionOK = '', $actionCancel = '', $eventOk = '', $eventCancel = '') {
        $prompt = new MPrompt(_M('Confirmação'), $msg);
        $prompt->setType(MPrompt::MSG_TYPE_CONFIRMATION);
        $prompt->setClose($actionCancel);
        $prompt->setOk($actionOK, $eventOk);
        $prompt->setCancel($actionCancel, $eventCancel);
        $prompt->setAction1($actionOK);
        $prompt->setAction2($actionCancel);
        return $prompt;
    }

    public static function question($msg, $actionYes = '', $actionNo = '', $eventYes = '', $eventNo = '') {
        $prompt = new MPrompt(_M('Questão'), $msg);
        $prompt->setType(MPrompt::MSG_TYPE_QUESTION);
        $prompt->setClose($actionNo);
        $prompt->setYes($actionYes, $eventYes);
        $prompt->setNo($actionNo, $eventNo);
        $prompt->setAction1($actionYes);
        $prompt->setAction2($actionNo);
        return $prompt;
    }

    public function generate() {
        $jsLib = Manager::getConf('theme.js') ?: 'dojo';
        if ($jsLib == 'dojo') {
            $internalForm = false;
            if (!is_array($this->message)) {
                $this->message = array($this->message);
            }
            $this->box = new MBox($this->caption, $this->close, '');

            $type = ucfirst($this->promptType);
            $m = new MUnorderedList($this->id . 'Message', $this->message);
            $m->type = 'none';
            $textBox = new MDiv($this->id . 'BoxText', $m, "text");
            $iconBox = new MDiv($this->id . 'BoxIcon', '', "icon icon{$type}");
            $imageTextBox = new MHContainer('', array($iconBox, $textBox));
            $imageTextBox->setClass('mPromptBox');
            $this->box->setControls($imageTextBox);
            if (!$this->form instanceof MBaseForm) {
                $this->form = new MSimpleForm();
                $this->controls[0] = $this->form;
                $internalForm = true;
            }
            if ($this->buttons) {
                foreach ($this->buttons as $button) {
                    list($label, $action, $event) = $button;
                    $name = $this->name . trim($label);
                    $spanLabel = new MDiv('', $label, 'button');
                    $b = new MButton($name, $spanLabel->generate());
                    $onclick = "manager.byId(\"$this->id\").hide();" . ($action ? MAction::getOnClick($action, $this->form->getTagId()) : '');
                    $b->addEvent('click', $onclick, true, false);
                    $this->box->addAction($b);
                }
            }

            $this->box->setExtendClass("title title{$type}");

            $this->controls[1] = new MDiv("{$this->id}Box", $this->box, "mPrompt");

            $prompt = new MDiv("{$this->id}Pane", $this->controls);
            if (($this->form instanceof MBaseForm and !$internalForm) || (!$this->page->isPostBack())) {
                $prompt = new MDiv($this->id, $prompt, '', $this->getStyle());
                $prompt->addStyle('display', 'none');
                $prompt->setDojoType('Manager.DialogSimple');
            }
            return $prompt->generate();
        }
        if ($jsLib == 'easyui') {
            $promptType = [
                'information' => 'info',
                'error' => 'error',
                'question' => 'question',
                'confirmation' => 'confirm',
                'warning' => 'warning'
            ];
            $dataJson = MJSON::encode((object)[
                'type' => $promptType[$this->promptType],
                'title' => ucFirst(_M($this->promptType)),
                'msg' => $this->message,
            ]);
            $dataJson = addslashes($dataJson);
            //$control->property->id = 'mprompt';
            $action1 = MAction::parseAction(addslashes($this->action1));
            $action2 = MAction::parseAction(addslashes($this->action2));
            $this->page->addJsCode("var {$this->id} = theme.prompt('{$this->id}','{$dataJson}',\"{$action1}\",\"{$action2}\");");
            //$show = ($control->property->show === false) ? false : true;
            //if ($show) {
            //    $this->page->onLoad("{$control->property->id}.show();");
            //}
            return '';
        }
    }

}
?>


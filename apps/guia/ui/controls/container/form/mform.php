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
 * A class for forms, extending from MBaseForm.
 * This class acts as a Decorator for MBaseForm, optionally adding a outer box, actions and menus to the form.
 */
class MForm extends MBaseForm {
    public $formBox;
    public $header;
    public $footer;
    public $concreteForm;
    public $align = null;
    public $enterSubmit;
    public $enterAction;

    /**
     * Attribute for acting as previously MFormObject
     */
    public $module;
    public $controller;
    public $object;
    protected $actions;
    public $menuAdded;

    public function onBeforeCreate() {
        parent::onBeforeCreate();
        $this->property->outerBox = true;
        $this->property->base = NULL;
        $this->property->generateBase = false;
        $this->property->inline = false;
        $this->property->load = NULL;
    }

    public function onCreate() {
        parent::onCreate();
        $this->formBox = new MBox();
        /* as MFormAction */
        $this->property->inline = Manager::getConf('ui.inlineFormAction') || $this->property->inline;
        /* as MFormBase */
        $this->actions = array();
        $this->menuAdded = false;
        $this->setRender('form');
    }

    /**
     * This is the constructor of the Form class. 
     *
     * @param $title  (string) the form's title string
     * @param $action (string) the URL for the forms <code>ACTION</code>
     *                attribute.
     */
    public function init($title = '', $close = '', $icon = '', $action = '') {
        parent::init();
        $this->setBox($title, $close, $icon, $action);
    }

    public function setBox($title = '', $close = '', $icon = '', $action = '') {
        $this->setTitle($title);
        $this->setClose($close);
        $this->setAction($action);
    }

    public function setCustomBox($id) {
        $this->formBox = new MCustomBox();
        $this->formBox->setId($id);
    }

    /**
     * Obtains the content of this form's title. Observe, that this
     * can be anything other as a simple text string too, such as array of
     * strings and an object implementing the <code>Generate()</code> method.
     *
     * @return (Mixed &) a reference to the title of the form
     */
    public function getTitle() {
        return $this->property->title;
    }

    /**
     * Set the form's title/caption
     *
     * @param (string) $title Form title
     */
    public function setTitle($title) {
        $this->property->title = $title;
    }

    /**
     * Set the form's title/caption
     *
     * @param (string) $title Form title
     */
    public function setCaption($title) {
        $this->property->title = $title;
    }

    /**
     * Sets the form's close action
     *
     * @param (string) $action Form action
     */
    public function setClose($action) {
        $this->property->close = $action;
    }

    /**
     * Sets the form's modal result
     *
     * @param (string) $action Form action
     */
    public function setModal($value) {
        $this->property->modal = $value;
    }

    /**
     * Set the box flag
     *
     * @param (boolean) $value
     */
    public function setOuterBox($value) {
        $this->property->outerBox = $value;
    }

    /**
     * Set the inline flag
     * Se apenas os campos e botões são inseridos no form Base e não o form inteiro 
     * 
     * @param (boolean) $value
     */
    public function setInline($value) {
        $this->property->inline = $value;
    }

    public function getInline() {
        return $this->property->inline;
    }

    /**
     * Obtains the content title of this form's footer. Observe, that this
     * can be anything other as a simple text string too, such as array of
     * strings and an object implementing the <code>Generate()</code> method.
     *
     * @return (Mixed &) a reference to the footer of the form
     */
    public function getFooter() {
        return $this->footer;
    }

    /**
     * Form's footer.
     * Sets the form's footer content.
     *
     * @param $footer (tipo) Footer content
     */
    public function setHeader($header) {
        $this->header = $header;
    }

    /**
     * Form's footer.
     * Sets the form's footer content.
     *
     * @param $footer (tipo) Footer content
     */
    public function setFooter($footer) {
        $this->footer = $footer;
    }

    public function setAlign($value) {
        $this->align = $value;
    }

    public function setWidth($width = NULL) {
        if ($width) {
            $this->formBox->addStyle('width', "{$width}");
        }
    }

    public function setHeight($height = NULL) {
        if ($height) {
            $this->formBox->addStyle('height', "{$height}");
        }
    }

    public function enterSubmit($button) {
        $this->enterSubmit = $button;
    }

    public function enterAction($action) {
        $this->enterAction = $action;
    }

    public function addTool($title = '', $action = '', $icon = '') {
        $this->formBox->addTool($title, $action, $icon);
    }

    /** MFormBase * */
    public function addAction($action, $label = '', $transaction = '', $access = '', $visible = '') {
        if ( ($label != '') && ($visible!==false) ){
            if ($transaction != '') {
                $this->addUserAction($transaction, $access, $label, $action);
            } else {
                $this->actions[$action] = $label;
            }
        }
    }

    public function addUserAction($transaction, $access, $label, $action = '') {
        if (Manager::checkAccess($transaction, $access)) {
            $this->actions[$action] = $label;
        }
    }

    public function setActions($action, $level = 1) {
        $actions = Manager::getAction($action);
        //mdump($action);
        if ($level == 1) {
            $this->addActions($actions[ACTION_ACTIONS]);
        } else {
            foreach ($actions as $group) {
                $this->addActions($group[ACTION_ACTIONS]);
            }
        }
    }

    private function addActions($actions) {
        foreach ($actions as $p => $action) {
            $transaction = $action[ACTION_TRANSACTION];
            if ($transaction) {
                $this->addUserAction($transaction, $action[ACTION_ACCESS], $action[ACTION_CAPTION], $action[ACTION_PATH]);
            } else {
                $this->addAction($action[ACTION_PATH], $action[ACTION_CAPTION]);
            }
        }
    }

    public function addForm($form = NULL) {
        if ($form) {
            $this->addControl($form);
        }
    }

    public function addMenuBar() {
        $menuBar = new MMenuBar('menuBar' . $this->getId());
        foreach ($this->actions as $action => $label) {
            $menuBar->addItem($label, $this->getActionURL($action));
        }
        $this->setHeader($menuBar);
        $this->menuAdded = true;
    }

    public function getActionURL($action = '') {
        if (MAction::isAction($action)) {
            $url = $action;
        } else {
            $url = ">" . ($this->module ? $this->module . "/" : '') . "{$this->getController()->getName()}/{$action}";
        }
        return $url;
    }

    public function getActionLabel($action = '') {
        return $this->actions[($action == '') ? 'main' : $action];
    }

    public function getURL($action = '', $oid = '', $args = array()) {
        return Manager::getURL(($this->module ? $this->module . "/" : '') . "{$this->getController()->getName()}/{$action}/{$oid}", $args);
    }

    /** End MFormBase * */

    /** MFormAction * */
    public function onBeforeLoad() {
        parent::onBeforeLoad();
        $this->setModule($this->getController()->getModule() ? : ''); //$this->controller->getApplication());
        $this->setObject($this->data->object);
    }

    public function setBase($formBase) {
        $this->property->base = $formBase;
        $this->property->generateBase = true;
    }

    public function getBase() {
        return $this->property->base;
    }

    public function getGenerateBase() {
        return $this->property->generateBase;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public function getController() {
        $controller = ($this->controller) ?: parent::getController();
        return $controller ?: Manager::getCurrentController();
    }

    public function setObject($class, $id = '') {
        if (is_string($class)) {
            $this->object = new $class($id);
        } else {
            $this->object = $class;
        }
    }

    /** End MFormAction * */
    public function generateFooter() {
        
    }

    public function generateInner() {
        $isFormBase = (count($this->actions) || $this->addMenu);
        if ($this->property->base) { // this is a FormAction
            if ($this->property->generateBase) {
                $formBase = $this->property->base;
                $path = dirname($this->view->viewFile);
                $base = $this->instance($formBase, $path); //new $formBase();
                $base->load();
                $inline = $this->property->inline && (!$isFormBase);
                if ($inline) {
                    if (count($base->actions) && (!$base->menuAdded)) {
                        $this->actions = $base->actions;
                        $title = $this->getTitle();
                        $key = array_search($title, $this->actions);
                        if ($key !== false) {
                            $label = new MLabel($title);
                            $label->setClass('mBoxPaneTitle');
                            $this->actions[$key] = $label;
                        }
                        $this->addMenuBar();
                    }
                    $this->setTitle($base->getTitle());
                    $this->property->base = $base->getBase();
                    $this->property->inline = $base->getInline();
                    $this->property->generateBase = $base->getGenerateBase();
                    $this->inner = $this;
                    return;
                } else {
                    $this->property->generateBase = false;
                    $base->addForm($this);
                    $this->concreteForm = $base;
                    $this->inner = $this->render();
                    return;
                }
            } else {
                if (count($this->actions) && (!$this->menuAdded)) {
                    $this->addMenuBar();
                }
            }
        } else {
            if (count($this->actions) && (!$this->menuAdded)) {
                $this->addMenuBar();
            }
        }

        if ($this->page->isWindow()) {
            $this->setClose($this->ui->closeWindow());
        }
        if ($this->property->close == 'modal') {
            if ($this->property->modal) {
                $this->page->onLoad(MUI::closeWindow(''));
            }
            $this->property->close = null;
        }
        parent::generateInner();
        $submit = $this->inner['submit'];
        $body = $this->inner['body'];
        $buttons = $this->inner['buttons'];
        $footer = $this->generateFooter();
        if ($this->property->outerBox) {
            $this->formBox->setCaption($this->property->title);
            if ($this->property->close) {
                $this->formBox->setClose($this->property->close);
            }
            $this->formBox->setControls(array($this->header, $body, $this->footer));
            if ($buttons) {
                $this->formBox->addAction($buttons);
            }
            $this->setClass("mForm");
            if (!is_null($this->align)) {
                $this->addStyle('text-align', $this->align);
            }
        } else {
            $this->formBox = new MContainer($this->getId(), $body);
            if ($buttons) {
                $this->formBox->addControl($buttons);
            }
            if (!is_null($this->align)) {
                $this->formBox->addStyle('text-align', $this->align);
            }
        }
        $form = new MConcreteForm($this->tagId);
        $form->addContent($this->formBox);
        $form->setAction($this->action);
        $form->setEnctype($this->enctype);
        $form->setMethod($this->method);

        if ($this->enterSubmit) {
            $button = $this->buttons[$this->enterSubmit];
            $event = MAction::getOnClick($button->getAction(), $this->enterSubmit);
            $form->addEvent('keypress', "if (event.keyCode==dojo.keys.ENTER) { event.preventDefault();{$event};}", false);
        }

        if ($this->enterAction) {
            $event = MAction::getOnClick($this->enterAction, $this->tagId);
            $form->addEvent('keypress', "if (event.keyCode==dojo.keys.ENTER) { event.preventDefault();{$event};}", false);
        }

        $this->page->onSubmit($submit, $this->tagId);
        $this->concreteForm = $form;
        $this->inner = $this->render();
    }

}

?>
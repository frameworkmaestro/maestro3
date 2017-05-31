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

abstract class MControl extends MComponent implements IControl {

    /**
     * Objeto Style (estilos CSS)
     */
    protected $style;

    /**
     * Objeto com attributos renderizáveis
     */
    protected $attributes;

    /**
     * Objeto referente ao label do controle
     */
    protected $fieldLabel;

    /**
     * Código visual interno do controle
     */
    protected $inner;

    /**
     * Código visual completo do controle
     */
    protected $result;

    /**
     * Handlers dos eventos associados ao controle
     */
    protected $event;

    /**
     * The form where this control is inserted (if there is one...).
     */
    protected $form;

    /**
     * Eventos AJAX associado ao controle
     */
    protected $ajax;

    public $context;

    /**
     * Ciclo de vida
     *      - Conjunto de métodos executados durante o ciclo de vida do controle.
     *
     * - construct : instanciação do controle
     *      - create
     *          - onBeforeCreate
     *          - onCreate : inicializa principais propriedades
     *          - onInit
     *              - init : código de inicialização do controle
     *              - onAfterCreate :
     * - load
     *          - onBeforeLoad
     *          - onLoad : controle é carregado em um container (ex. createFields)
     *          - onAfterLoad (ex. setData)
     * - generate
     *          - onBeforeGenerate
     *          - onGenerate : gera o codigo para exibição do controle em $this->result
     *          - onAfterGenerate
     * - postBack
     *          - onBeforePostBack
     *          - onPostBack : executado quando o valor do controle é recebido via POST
     *          - onAfterPostBack
     */
    function __construct() {
        parent::__construct(get_class($this) . uniqid());
        call_user_func_array(array($this, 'create'), func_get_args());
    }

    public function __get($property) {
        $value = parent::__get($property);
        if ($value instanceof MNULL) {
            return $this->style->get($property);
        }
        return $value;
    }

    public function __set($property, $value) {
        $set = parent::__set($property, $value);
        if ($set instanceof MNULL) {
            $this->style->set($property, $value);
        }
    }

    public function instance($className, $path = '') {
        if (class_exists($className, true)) {
            $control = new $className();
        } else {
            $file = $path . '/' . $className . '.xml';
            $controls = $this->getControlsFromXML($file);
            $control = array_shift($controls); // retorna o primeiro controle definido no arquivo xml
        }
        if ($control) {
            if ($this->view) {
                $control->setView($this->view);
            }
        }
        return $control;
    }

    public function getControlsFromXML($file) {
        $xmlControls = new MXMLControls();
        $xmlControls->loadFile($file, $this);
        return $xmlControls->get();
    }

    /**
     * The clone method.
     * It is used to clone controls, avoiding references to same attributes, styles and controls.
     */
    public function __clone() {
        $this->attributes = clone $this->attributes;
        $this->style = clone $this->style;
        if ($this->fieldLabel) {
            $this->fieldLabel = clone $this->fieldLabel;
        }
        parent::__clone();
    }

    /**
     * IControl methods
     */
    public function init($id = '') {
        $this->setId($id);
    }

    function create() {
        call_user_func_array(array($this, 'onBeforeCreate'), func_get_args());
        call_user_func_array(array($this, 'onCreate'), func_get_args());
        call_user_func_array(array($this, 'onInit'), func_get_args());
    }

    function onBeforeCreate() {
        
    }

    function onCreate() {
        $this->data = Manager::getData();
        $this->attributes = new MAttributes();
        $this->style = new MStyle();
        $this->inner = '';
        $this->event = array();
        $this->ajax = array();
        $this->fieldLabel = NULL;
        $this->setEnabled(true);
        $this->setReadonly(false);
        $this->setVisible(true);
    }

    function onInit() {
        if (count(func_get_args())) {
            call_user_func_array(array($this, 'init'), func_get_args());
            call_user_func_array(array($this, 'onAfterCreate'), func_get_args());
        }
    }

    function onAfterCreate() {
        
    }

    function load() {
        call_user_func_array(array($this, 'onBeforeLoad'), func_get_args());
        call_user_func_array(array($this, 'onLoad'), func_get_args());
        call_user_func_array(array($this, 'onAfterLoad'), func_get_args());
    }

    function onBeforeLoad() {
        
    }

    function onLoad() {
        
    }

    function onAfterLoad() {
        
    }

    function postback() {
        call_user_func_array(array($this, 'onBeforePostBack'), func_get_args());
        call_user_func_array(array($this, 'onPostBack'), func_get_args());
        call_user_func_array(array($this, 'onAfterPostBack'), func_get_args());
    }

    function onBeforePostBack() {
        
    }

    function onPostBack() {
        
    }

    function onAfterPostBack() {
        
    }

    /**
     * Compatibility
     * @param <type> $data
     */
    public function eventHandler($data = NULL) {
        $eventTarget = mrequest('__EVENTTARGET') ? : mrequest('event');
        if ($eventTarget) {
            if (method_exists($this, $eventTarget)) {
                $this->$eventTarget($data);
            } else {
                $eventTarget .= '_click';
                if (method_exists($this, $eventTarget)) {
                    $this->$eventTarget($data);
                }
            }
        }
    }

    public function generate() {
        $this->result = '';
        $this->onBeforeGenerate();
        $this->onGenerate();
        $this->onAfterGenerate();
        return $this->result;
    }

    function onBeforeGenerate() {
        
    }

    function onGenerate() {
        if ($this->getEnabled()) {
            $this->generateInner();
            $content = $this->getInner();
            $this->generateEvent();
            $this->result = $this->getPainter()->generateToString($content);
        }
    }

    function onAfterGenerate() {
        
    }

    /*
      Identification - name = id
     */

    public function setName($name) {
        MUtil::setIfNull($this->property->id, $name);
        parent::setName($name);
    }

    public function setId($name) {
        $id = $name ? : 'control_' . substr(uniqid(), -6);
        $this->property->id = $id;
        $this->property->name = $id;
    }

    public function getId() {
        return isset($this->property->id) ? $this->property->id : null;
    }

    /*
      Facade to Style methods
    */

    public function setClass($cssClass, $add = true) {
        $this->style->setClass($cssClass, $add);
    }

    public function insertClass($cssClass) {
        $this->style->insertClass($cssClass);
    }

    public function addStyleSheet($styleFile) {
        $this->style->addStyleSheet($styleFile);
    }

    public function getClass() {
        return $this->style->getClass();
    }

    public function addStyle($name, $value) {
        $this->style->addStyle($name, $value);
    }

    public function cloneStyle(MControl $control) {
        $this->style = clone $control->style;
    }

    public function setStyle($style) {
        $this->style = $style;
    }

    public function getStyle() {
        return $this->style->getStyle();
    }

    public function setWidth($value) {
        $this->style->setWidth($value);
    }

    public function setHeight($value) {
        $this->style->setHeight($value);
    }

    public function setColor($value) {
        $this->style->setColor($value);
    }

    public function setVisibility($value) {
        $this->style->setVisibility($value);
    }

    public function setFont($value) {
        $this->style->setFont($value);
    }

    /*
      Facade to Attribute methods
     */

    public function addAttribute($name, $value = '') {
        $this->attributes->addAttribute($name, $value);
    }

    public function setAttribute($name, $value) {
        $this->attributes->addAttribute($name, $value);
    }

    public function getAttribute($name) {
        return $this->attributes->getAttribute($name);
    }

    public function setAttributes($attr) {
        $this->attributes->setAttributes($attr);
    }

    public function attributes($mergeDuplicates=false) {
        return $this->attributes->getAttributes($mergeDuplicates);
    }

    public function getAttributes($mergeDuplicates=false) {
        return $this->attributes->getAttributes($mergeDuplicates) . ' ' . $this->getStyle();
    }

    /*
      Facade to Attribute methods
     */

    public function setRole($value) {
        $this->addAttribute('role', $value);
    }

    public function setHTMLTitle($title) {
        $this->addAttribute('title', $title);
    }

    public function setDojoType($dojoType) {
        $this->attributes->setDojoType($dojoType);
    }

    public function addDojoProp($dojoProp, $value) {
        $this->attributes->addDojoProp($dojoProp, $value);
    }

    /*
      Label, Hint, Help and Tooltip methods
     */

    public function setShowLabel($value) {
        $this->property->showLabel = $value;
    }

    public function getShowLabel() {
        return $this->property->showLabel;
    }

    public function setLabel($label) {
        $this->getFieldLabel()->setText($label);
    }

    public function getLabel() {
        return $this->getFieldLabel()->getText();
    }

    public function setLabelWidth($value) {
        $this->getFieldLabel()->setWidth($value);
    }

    public function getLabelWidth() {
        return $this->getFieldLabel()->getWidth();
    }

    public function getFieldLabel() {
        if ($this->fieldLabel == NULL) {
            $this->fieldLabel = new MFieldLabel();
            $this->fieldLabel->setId($this->getId());
            $this->setShowLabel(true);
        }
        return $this->fieldLabel;
    }

    public function generateLabel() {
        return $this->getFieldLabel()->generate();
    }

    public function setHint($hint) {
        if ($hint != '') {
            $this->property->hint = $hint;
        }
    }

    public function getHint() {
        return isset($this->property->hint) ? $this->property->hint : null;
    }

    public function generateHint() {
        $hint = new MHint($this->getHint());
        return $hint->generate();
    }

    public function getHelp() {
        return $this->property->help;
    }

    public function setHelp($help) {
        $this->property->help = $help;
        $this->getFieldLabel()->setHelp($help);
    }

    public function setToolTip($tooltip) {
        if ($tooltip != '') {
            $this->property->tooltip = $tooltip;
            $this->page->addDojoRequire("dijit.Tooltip");
        }
    }

    /*
      Properties
     */

    public function setReadOnly($status) {        
        $this->property->readonly = $status;
    }

    public function getReadOnly() {
        return $this->property->readonly;
    }

    /**
     * Enabled status.
     * Acessory method to set the enabled status of the control.
     *
     * @param state (boolean) true or false depending the status
     */
    public function setEnabled($state) {
        $this->property->enabled = $state;
    }

    public function getEnabled() {
        return $this->property->enabled;
    }

    public function setText($text) {
        $this->property->text = $text;
    }

    public function getText() {
        return isset($this->property->text) ? $this->property->text : '';
    }

    public function setVisible($value) {
        $this->property->visible = $value;
    }

    public function getVisible() {
        return $this->property->visible;
    }

    public function setColumnWidth($value) {
        $this->property->columnWidth = $value;
    }

    public function getColumnWidth() {
        return $this->property->columnWidth;
    }

    public function setRowHeight($value) {
        $this->property->rowHeight = $value;
    }

    public function getRowHeight() {
        return isset($this->property->rowHeight) ? $this->property->rowHeight : null;
    }

    public function setOwner(MControl $owner) {
        $this->property->owner = $owner;
    }

    public function getOwner() {
        return $this->property->owner;
    }

    public function setCDATA($value) {
        $this->property->cdata = $value;
    }

    public function setValue($value) {
        $this->property->value = $value;
    }

    public function getValue() {
        return isset($this->property->value) ? $this->property->value : '';
    }

    /**
     * O objetivo dessa função é evitar ataques de XSS. Componentes que podem permitir a renderização de conteúdo
     * HTML devem sobrescrever esse método e fazer o tratamento necessário.
     * @param $value
     * @return string
     */
    public function sanitize($value) {
        return htmlentities($value, ENT_QUOTES);
    }

    /*
      Events
     */

    public function addEvent($event, $handler, $preventDefault = true, $dijit = false) {
        if ($handler{0} == ':') {
            $url = Manager::getCurrentURL() . '?event=' . substr($handler, 1);
            $handler = MUI::doAjaxText($url, $this->id);
        }
        $isDijit = false;//$this->getIsDijit() || $dijit;
        if ($event == 'onClick') {
            $event = 'click';
        } elseif ($event == 'onChange') {
            $event = 'change';
        }
        $this->event[$event][] = array('handler' => $handler, 'prevent' => $preventDefault, 'dijit' => $isDijit);
    }

    public function hasEvent($event) {
        return (count($this->event[$event]) > 0);
    }

    public function clearEvent($event) {
        $this->event[$event] = array();
    }

    public function getEvent() {
        return $this->event;
    }

    public function setEvent($event) {
        $this->event = $event;
    }

    public function generateEvent() {
        $this->generateAjax();
        if (is_array($this->event) && count($this->event)) {
            foreach ($this->event as $event => $handlers) {
                if ($this->hasEvent($event)) {
                    foreach ($handlers as $handler) {
                        $p = $handler['prevent'] ? "true" : "false";
                        $d = $handler['dijit'] ? "true" : "false";
                        $function = $handler['handler'];
                        if ($function{0} == '!') { // handler já é um eventHandler javascript
                            $h = substr($function, 1);
                            $register = "[\"{$this->id}\",\"{$event}\",$h]";
                        } else { // handler será incluido dentro de um eventHandler
                            $h = addslashes($function);
                            $register = "[\"{$this->id}\",\"{$event}\",\"{$h}\", {$p}, {$d}]";
                        }
                        $this->page->registerEvent($register);
                    }
                }
            }
        }
    }

    public function ajaxText($event, $url, $updateElement, $preventDefault = false, $dijit = false) {
        $this->ajax('text', $event, $url, $updateElement, $preventDefault, $dijit);
    }

    public function ajaxEvent($event, $url, $callback=null, $preventDefault = false, $dijit = false) {
        $this->ajax('json', $event, $url, $callback, $preventDefault, $dijit);
    }

    public function ajax($type = 'text', $event='', $url = '', $load = '', $preventDefault = false, $dijit = false) {
        $ajax = new StdClass();
        $ajax->type = $type;
        $ajax->event = $event;
        $ajax->url = $url ? : Manager::getCurrentAction();
        $ajax->load = $load;
        $ajax->preventDefault = $preventDefault;
        $ajax->dijit = $dijit;
        $this->ajax[count($this->ajax)] = $ajax;
    }

    public function getAjax() {
        return count($this->ajax) ? $this->ajax[0] : null;
    }

    public function setAjax($ajax) {
        if ($ajax) {
            $this->ajax[0] = $ajax;
        }
    }

    public function generateAjax() {
        if (count($this->ajax)) {
            foreach ($this->ajax as $ajax) {
                $url = Manager::getURL($ajax->url);
                if ($ajax->type == 'text') {
                    $ajaxCmd = "manager.doAjaxText(\"{$url}\",\"{$ajax->load}\", \"{$this->id}\");";
                } else {
                    $this->page->addDojoRequire('dojo.data.ItemFileReadStore');
                    if ($ajax->event == "onSelectionChange") {
                        $ajax->event = "change";
                        $ajax->load = "function(result){manager.byId(result.data.ajaxReturn.control).reset();manager.byId(result.data.ajaxReturn.control).set(\"store\",new dojo.data.ItemFileReadStore({data:result.data.ajaxReturn}));manager.page.clearBusy();}";
                    }
                    $ajaxCmd = "manager.doAjax(\"{$url}\",{$ajax->load}, \"{$this->id}\");";
                }
                $this->addEvent($ajax->event, $ajaxCmd, $ajax->preventDefault, $ajax->dijit);
            }
        }
    }

    /*
     * Segurança
     */

    public function setAccess($access) {
        $this->property->access = $access;
    }

    public function getAccess() {
        return isset($this->property->access) ? $this->property->access : null;
    }

    public function checkAccess() {
        $result = true;
        $access = $this->getAccess();
        if ($access && Manager::isLogged()) {
            $perms = explode(':', $access);
            $right = Manager::getPerms()->getRight($perms[1]);
            $result = Manager::checkAccess($perms[0], $right);
        }
        return $result;
    }

    /*
      Content and rendering
     */

    public function setContent($inner) {
        $this->inner = $inner;
    }

    public function setInner($inner) {        
        $this->inner = $inner;
    }

    public function getInner() {
        return $this->inner;
    }

    public function generateInner() {
        if ($this->property->tooltip != '') {
            $this->getPage()->onLoad("new dijit.Tooltip({connectId: [\"{$this->getId()}\"], label: \"{$this->property->tooltip}\"});");
        }
    }

    public function render() {
        $method = $this->render;
        return $this->painter->$method($this);
    }

    public function getInnerToString() {
        return $this->painter->generateToString($this->getInner());
    }

    public function __toString() {
        return $this->generate();
    }

}

?>
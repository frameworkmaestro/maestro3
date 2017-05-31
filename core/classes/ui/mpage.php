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
 *  MPage
 *      Represents the page to be rendered. The page contains scripts and follows a layout.
 */
define('PAGE_ISPOSTBACK', '__ISPOSTBACK');

class MPage extends MComponent {

    /**
     * Layout is a HTML template to decorate views. Default is 'base'.
     * @var MLayout
     */
    public $layout;

    /**
     * List of scripts to be rendered.
     * @var MScripts
     */
    public $scripts;

    /**
     * Hold variables throught round-trips.
     * @var MState
     */
    public $state;

    /**
     *
     * @var <type>
     */
    public $action;

    /**
     *
     * @var <type>
     */
    public $actionChanged;

    /**
     *
     * @var <type>
     */
    public $redirectTo;

    /**
     *
     * @var <type>
     */
    public $fileUpload;

    /**
     *
     * @var <type>
     */
    public $window;

    /**
     *
     * @var <type>
     */
    public $prompt;

    /**
     *
     * @var <type>
     */
    public $binary;

    /**
     *
     * @var <type>
     */
    public $download;

    /**
     * Holds to template object that will be rendered.
     * @var MTemplate
     */
    public $template;

    /**
     * Name of template to be rendered. Default is 'index'.
     * @var string
     */
    public $templateName;

    /**
     * Theme name
     * @var type 
     */
    public $theme;

    /**
     * Array with page content.
     * @var array
     */
    public $content;

    /**
     * CSS code to include on page.
     * @var string 
     */
    public $styleSheetCode;
    
    /**
     * Page title.
     * @var string Title to diplay on page header.
     */
    public $title;

    public function __construct() {
        parent::__construct('page' . uniqid());
        $this->scripts = new MScripts($this->name);
        $this->state = new MState($this->name);
        $this->action = Manager::getRequest()->getURL();
        $this->actionChanged = false;
        $this->layout = mrequest('__LAYOUT')? : 'default';
        $this->fileUpload = mrequest('__ISFILEUPLOAD') == 'yes';
        $this->content = new MPageContent();
        $template = mrequest('__TEMPLATE') ? : (Manager::getConf('theme.template')? : 'index');
        $this->setTemplateName($template);
        $this->setTemplate();
        $this->theme = Manager::$conf['theme']['name'];
        $this->title = Manager::getConf('name');
        $this->styleSheetCode = '';
        ob_start();
    }

    public function getLayout() {
        return $this->layout;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * Template methods
     */

    /**
     * Define template and template variables
     */
    public function setTemplate() {
        $path = Manager::getThemePath();
        $this->template = new MTemplate($path);
        $this->template->context('manager', Manager::getInstance());
        $this->template->context('page', $this);
        $this->template->context('charset', Manager::getOptions('charset'));
        $this->template->context('layout', $this->layout);
        $this->template->context('template', $this->template);
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getTemplateName() {
        return $this->templateName;
    }

    public function setTemplateName($name) {
        $this->templateName = $name;
    }

    /**
     * is* methods
     */
    public function isPostBack() {
        return Manager::getRequest()->isPostBack();
    }

    public function isWindow() {
        return ($this->layout == 'window');
    }

    /**
     * Action methods
     */
    function setAction($action) {
        $this->action = $action;
        $this->actionChanged = true;
    }

    public function getAction() {
        return $this->action;
    }

    /*
      CSS Styles
     */

    public function addStyleSheet($fileName) {
        $file = Manager::getFrameworkPath('var/files/' . basename($fileName));
        copy($fileName, $file);
        $url = Manager::getDownloadURL('cache', basename($fileName), true);
        $this->onLoad("dojo.create(\"link\", {href:'{$url}', type:'text/css', rel:'stylesheet'}, document.getElementsByTagName('head')[0]);");
    }

    public function addStyleSheetCode($code) {
        if (Manager::isAjaxCall()) {
            $fileName = md5($code) . '.css';
            $file = Manager::getFrameworkPath('var/files/' . $fileName);
            file_put_contents($file, $code);
            $url = Manager::getDownloadURL('cache', $fileName, true);
            $this->onLoad("dojo.create(\"link\", {href:'{$url}', type:'text/css', rel:'stylesheet'}, document.getElementsByTagName('head')[0]);");
        } else {
            $this->styleSheetCode .= "\n" . $code;
        }
    }

    /*
      Scripts
     */

    public function addScript($url, $module = null) {
        $this->scripts->addScript($url, $module);
    }

    public function addScriptURL($url) {
        $this->scripts->addScriptURL($url);
    }

    public function insertScript($url) {
        $this->scripts->insertScript($url);
    }

    public function addDojoRequire($dojoModule) {
        $dojoModule = str_replace('.', '/', $dojoModule);
        $dojoModule = str_replace('Manager', 'manager', $dojoModule);
        $this->scripts->jsCode->insert("require([\"{$dojoModule}\"]);");
    }

    public function addExtRequire($module) {
        $this->scripts->jsCode->insert("Ext.require(\"{$module}\");");
    }

    public function getScripts() {
        return $this->scripts->scripts;
    }

    public function getCustomScripts() {
        return $this->scripts->customScripts;
    }

    public function getOnLoad() {
        return $this->scripts->onload;
    }

    public function getOnError() {
        return $this->scripts->onerror;
    }

    public function getOnSubmit() {
        return $this->scripts->onsubmit;
    }

    public function getOnUnLoad() {
        return $this->scripts->onunload;
    }

    public function getOnFocus() {
        return $this->scripts->onfocus;
    }

    public function getJsCode() {
        return $this->scripts->jsCode;
    }

    public function onSubmit($jsCode, $formId) {
        $this->scripts->addOnSubmit($jsCode, $formId);
    }

    public function onLoad($jsCode) {
        $this->scripts->onload->add($jsCode);
    }

    public function onUnLoad($jsCode) {
        $this->scripts->onunload->add($jsCode);
    }

    public function onError($jsCode) {
        $this->scripts->onerror->add($jsCode);
    }

    public function onFocus($jsCode) {
        $this->scripts->onfocus->add($jsCode);
    }

    public function addJsCode($jsCode) {
        $this->scripts->jsCode->add($jsCode);
    }

    public function addJsFile($fileName) {
        $jsCode = file_get_contents($fileName);
        $this->scripts->jsCode->add($jsCode);
    }

    public function registerEvent($event) {
        $this->scripts->events->add($event);
    }

    /*
      State
     */

    // it extends the manager->request to include $state
    public function request($vars, $component_name = '', $from = 'ALL') {
        $value = '';
        if (($vars != '')) {
            $value = mrequest($vars, $from);
            if (!isset($value)) {
                if (!$component_name) {
                    $value = $this->state->get($vars);
                } else {
                    $value = $this->state->get($vars, $component_name);
                }
            }
        }
        return $value;
    }

    public function setViewState($var, $value, $component_name = '') {
        $this->state->set($var, $value, $component_name);
    }

    public function getViewState($var, $component_name = '') {
        return $this->state->get($var, $component_name);
    }

    public function loadViewState() {
        $this->state->loadViewState();
    }

    public function saveViewState() {
        $this->state->saveViewState();
    }

    /**
     * Element Value
     */
    // Set a value for a client element, using DOM
    // This method use a javascript code that is execute on response
    public function setElementValue($element, $value) {
        $this->onLoad("manager.getElementById('{$element}').value = '{$value}';");
    }

    public function copyElementValue($element1, $element2) {
        $this->onLoad("manager.getElementById('{$element1}').value = manager.getElementById('{$element2}').value;");
    }

    /*
     * Properties
     */

    public function setTitle($value) {
        $this->property->title = $value;
    }

    public function getTitle() {
        return $this->property->title;
    }

    /*
      Response related methods
     */

    public function redirect($url) {
        $this->redirectTo = $url;
    }

    public function window($url) {
        $this->window = $url;
    }

    public function binary($stream) {
        $this->binary = $stream;
    }

    public function download($fileName) {
        $this->download = $fileName;
    }

    public function prompt($prompt) {
        $this->prompt = $prompt;
    }

    /*
      Token
     */

    public function getTokenId() {
        Manager::getSession()->set('__MAESTROTOKENID', md5(uniqid()));
        $tokenId = Manager::useToken ? Manager::getSession()->get('__MAESTROTOKENID') : '';
        //mdump('getting token id = ' . $tokenId);
        return "manager.page.tokenId = '{$tokenId}';";
    }

    public function sendTokenId() {
        $this->onload($this->getTokenId());
    }

    /**
     * Generate methods
     */
    public function generate($element = 'content') {
        $html = '';
        if ($element == 'content') {
            $html = $this->generateContent() . $this->generateStyleSheetCode() . $this->generateScripts();
        } else {
            $component = new $element;
            $html = $component->generate();
        }
        return $html;
    }

    public function generateStyleSheetCode() {
        //mdump('='. $this->styleSheetCode);
        $code = ($this->styleSheetCode != '') ? "<style type=\"text/css\">" . $this->styleSheetCode . "\n</style>\n" : '';
        return $code;
    }

    public function generateScripts() {
        return $this->scripts->generate($this->getName());
    }

    public function fetch($template = '') {
        $template = $template ? : $this->getTemplateName();
        $html = $template != '' ? $this->template->fetch($template . '.html') : $this->generate();
        return $html;
    }

    public function render($template = '') {
        $html = $this->fetch($template);
        if ($ob = ob_get_clean()) {
            $html = $ob . $html;
        }
        return $html;
    }

    /**
     * Content
     */
    // get the array of controls from content
    public function getContent($key = NULL) {
        return ($key !== NULL ? $this->content->getControl($key) : $this->content->getControls());
    }

    // set the content
    public function setContent($content) {
        if (is_string($content)) {
            $this->content->setInner($content);
        } else {
            $this->clearContent();
            $this->content->setControls($content);
        }
    }

    public function clearContent() {
        $this->content->clearControls();
    }

    public function addContent($content, $key = NULL) {
        if ($key !== NULL) {
            $this->content->insertControl($content, $key);
        } else {
            $this->content->addControl($content);
        }
    }

    public function appendContent($content) {
        $this->addContent($content);
    }

    public function insertContent($content) {
        $this->content->insertControl($content, 0);
    }

    public function generateContent() {
        $this->content->generateInner();
        $html = MBasePainter::generateToString($this->content->getInner());
        return $html;
    }

}

?>
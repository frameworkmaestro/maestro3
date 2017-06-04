<?php

/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
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

class MView
{

    public $application;
    public $module;
    public $controller;
    public $viewFile;
    public $data;

    //public $page;
    //public $result;

    public function __construct($application, $module, $controller, $viewFile)
    {
        $this->application = $application;
        $this->module = $module;
        $this->controller = $controller;
        $this->viewFile = $viewFile;
    }

    public function init()
    {
        //$this->page = $this->manager->getPage();
        //$this->manager->view = $this;
        //$this->result = $this->manager->getResult();
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function setArgs($args)
    {
        if (count($args)) {
            foreach ($args as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    public function getPath()
    {
        return pathinfo($this->viewFile, PATHINFO_DIRNAME);
    }

    public function getControl($className)
    {
        return MControl::instance($className);
    }

    /**
     * Processa o arquivo da view e inclui o conteudo no objeto Page.
     * @param type $controller
     * @param type $parameters
     * @return type
     */
    public function process($controller, $parameters)
    {

        mtrace('view file = ' . $this->viewFile);

        $path = $this->getPath();
        Manager::addAutoloadPath($path);
        $extension = pathinfo($this->viewFile, PATHINFO_EXTENSION);
        $baseName = basename($this->viewFile);
        $content = '';
        $page = Manager::getPage();
        $this->controller = $controller;
        $this->data = $parameters;
//        $mlabelTemporario = new MFieldLabel(); // remover esta linha
        if ($extension == 'php') {
            $viewName = basename($this->viewFile, '.php');
            mtrace($viewName);
            include_once $this->viewFile;
            $view = new $viewName();
            $view->setView($this);
            $view->load();
            if ($page->isPostBack()) {
                $view->eventHandler($this->data);
                $view->postback();
            }
            $page->addContent($view);
        } elseif ($extension == 'xml') {
            $container = new MContainer();
            $container->setView($this);
            $controls = $container->getControlsFromXML($this->viewFile);
            if (is_array($controls)) {
                foreach ($controls as $view) {
                    if (is_object($view)) {
                        //$view->setView($this);
                        $view->load();
                        if ($page->isPostBack()) {
                            $view->postback();
                        }
                        $page->addContent($view);
                    }
                }
            }
        } elseif (($extension == 'js') || ($extension == 'html')) {
            $template = new MTemplate(dirname($this->viewFile));
            $template->context('manager', Manager::getInstance());
            $template->context('page', Manager::getPage());
            $template->context('view', $this);
            $template->context('data', $parameters);
            $template->context('template', $template);
            mtrace('basename = ' . $baseName);
            $content = $template->fetch($baseName);
            $page->setContent($content);
        } elseif ($extension == 'wiki') {
            $wikiPage = file_get_contents($this->viewFile);
            $wiki = new MWiki();
            $content = $wiki->parse('', $wikiPage);
            $page->setContent($content);
        }
//        return $content;
    }

}

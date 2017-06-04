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

/**
 * Brief Class Description.
 * Complete Class Description.
 */
class MController
{

    private $logger;
    private $encryptedFields = array();
    protected $name;
    protected $application;
    protected $module;
    protected $action;
    protected $data;
    protected $params;
    public $renderArgs = array();

    public function __construct()
    {

    }

    public function __call($name, $arguments)
    {
        if (!is_callable($name)) {
            throw new \BadMethodCallException("Method [{$name}] doesn't exists in " . get_class($this) . " Controller.");
        }
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setEncryptedFields(array $fields)
    {
        $this->encryptedFields = $fields;
    }

    public function isPost()
    {
        return \Manager::getRequest()->isPostBack();
    }

    public function init()
    {
        Manager::checkLogin();
    }

    public function dispatch($action)
    {
        mtrace('mcontroller::dispatch = ' . $action);
        $this->decryptData();

        if (!method_exists($this, $action)) {
            mtrace('action does not exists = ' . $action);
            try {
                $this->render($action);
            } catch (Exception $e) {
                throw new ERunTimeException(_M("App: [{$this->application}], Module: [{$this->module}], Controller: [{$this->name}] : action [{$action}] not found!"));
            }
        } else {
            try {
                $this->action = $action;
                if (MPage::isPostBack()) {
                    $actionPost = $action . 'Post';
                    if (method_exists($this, $actionPost)) {
                        $action = $actionPost;
                    }
                }
                mtrace('executing = ' . $action);
                $method = new \ReflectionMethod(get_class($this), $action);
                $params = $method->getParameters();
                $values = array();
                foreach ($params as $param) {
                    $value = $this->data->{$param->getName()};
                    if (!$value && $param->isDefaultValueAvailable()) {
                        $value = $param->getDefaultValue();
                    }
                    $values[] = $value;
                }
                $result = call_user_func_array([$this, $action], $values);

                if (!$this->getResult()) {
                    if (!Manager::isAjaxCall()) {
                        Manager::$ajax = new MAjax(Manager::getOptions('charset'));
                    }
                    $this->setResult(new MRenderJSON(json_encode($result)));
                }
            } catch (Exception $e) {
                mdump('Controller::dispatch exception: ' . $e->getMessage());
                if (\Manager::PROD()) {
                    $this->renderPrompt('error', $e->getMessage());
                } else {
                    $this->renderPrompt('error', "[<b>" . $this->name . '/' . $action . "</b>]" . $e->getMessage());
                }
            }
        }
    }

    /**
     * Executed at the end of Controller execution.
     */
    public function terminate()
    {

    }

    public function forward($action)
    {
        Manager::getFrontController()->setForward($action);
    }

    public function setResult($result)
    {
        Manager::getFrontController()->setResult($result);
    }

    public function getResult()
    {
        return Manager::getFrontController()->getResult();
    }

    public function getContainer()
    {
        return Manager::getFrontController()->getContainer();
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    protected function setProperty($property, $value, $fields)
    {
        foreach ($fields as $field) {
            $this->data->{$field . $property} = $value;
        }
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setData()
    {
        $this->data = Manager::getData();
    }

    public function setDataObject($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    private function getContent($controller, $view, $parameters = NULL)
    {
        if ($this->module != '') {
            $path = Manager::getConf("srcPath.{$this->module}") . '/views/' . $controller . '/' . $view;
        } else {
            $path = Manager::getOptions('srcPath') . '/views/' . $controller . '/' . $view;
        }

        if (is_dir(Manager::getAppPath($path, $this->module))) {
            $path .= '/' . $view;
        }
        if (file_exists($file = Manager::getAppPath($path . '.php', $this->module, $this->application))) { // try php view
            mtrace('MController::getContent from ' . $file);
            $this->renderView($controller, $file, $parameters);
        } elseif (file_exists($file = Manager::getAppPath($path . '.xml', $this->module, $this->application))) { // php view not found, try xml view
            mtrace('MController::getContent from ' . $file);
            $this->renderView($controller, $file, $parameters);
        } elseif (file_exists($file = Manager::getAppPath($path . '.js', $this->module, $this->application))) { // xml view not found, try js view
            mtrace('MController::getContent from ' . $file);
            $this->renderView($controller, $file, $parameters);
        } elseif (file_exists($file = Manager::getAppPath($path . '.html', $this->module, $this->application))) { // js view not found, try html view
            mtrace('MController::getContent from ' . $file);
            $this->renderView($controller, $file, $parameters);
        } elseif (file_exists($file = Manager::getAppPath($path . '.wiki', $this->module, $this->application))) { // html view not found, try wiki view
            mtrace('MController::getContent from ' . $file);
            $this->renderView($controller, $file, $parameters);
        }
    }

    private function getParameters($parameters = NULL)
    {
        if (!(is_object($parameters) || is_array($parameters))) {
            $parameters = array('result' => $parameters);
        }
        foreach ($parameters as $name => $value) {
            $this->data->$name = $value;
        }
    }

    public function getService($service, $module = '')
    {
        $service = Manager::getService($this->application, ($module == '' ? $this->module : $module), $service);
        $service->setData();
        return $service;
    }

    public function renderAppView($app, $module, $controller, $viewFile, $parameters)
    {
        $view = Manager::getView($app, $module, $controller, $viewFile);
        $view->setArgs($parameters);
        $view->process($this, $parameters);
    }

    public function renderView($controller, $viewFile, $parameters = array())
    {
        $this->renderAppView($this->application, $this->module, $controller, $viewFile, $parameters);
    }

    public function renderPrompt($prompt)
    {
        if (is_string($prompt)) {
            $args = func_get_args();
            $oPrompt = MPrompt::$prompt($args[1], $args[2], $args[3]);
        } else {
            $oPrompt = $prompt;
        }
        if (Manager::isAjaxCall()) {
            $this->setResult(new MRenderPrompt($oPrompt));
        } else {
            Manager::getPage()->onLoad("manager.doPrompt('{$oPrompt->getId()}')");
            $this->setResult(new MRenderPage($oPrompt));
        }
    }

    public function renderJSON($json = '')
    {
        if (!Manager::isAjaxCall()) {
            Manager::$ajax = new MAjax();
            Manager::$ajax->initialize(Manager::getOptions('charset'));
        }
        $ajax = Manager::getAjax();
        $ajax->setData($this->data);
        $this->setResult(new MRenderJSON($json));
    }

    public function renderStream($stream)
    {
        $this->setResult(new MRenderBinary($stream, true, 'raw'));
    }

    public function renderBinary($stream, $fileName = '')
    {
        $this->setResult(new MRenderBinary($stream, true, $fileName));
    }

    public function renderDownload($file, $fileName = '')
    {
        $this->setResult(new MRenderBinary(null, false, $fileName, $file));
    }

    public function renderTemplate($templateName, $parameters = array())
    {
        $controller = strtolower($this->name);
        $path = Manager::getBasePath('/views/' . $controller . '/', $this->module);
        $file = $templateName . '.html';
        if (file_exists($path . '/' . $file)) {
            $template = new MTemplate($path);
            $template->load($file);
            $this->getParameters($parameters);
            $this->setResult(new MRenderTemplate($template, $this->data));
        } else {
            throw new ENotFoundException('Template ' . $templatename . ' was not found!');
        }
    }

    public function redirect($url)
    {
        $this->setResult(new MRedirect(NULL, $url));
    }

    public function notfound($msg)
    {
        $this->setResult(new MNotFound($msg));
    }

    public function renderPartial($viewName = '', $parameters = array())
    {
        if (($view = $viewName) == '') {
            $view = $this->action;
        }
        $this->getParameters($parameters);
        $controller = strtolower($this->name);
        $this->getContent($controller, $view, $this->data);
    }

    public function renderContent($viewName = '', $parameters = array())
    {
        $controller = strtolower($this->name);
        $view = $viewName;
        if ($view == '') {
            $view = $this->action;
        } else if (strpos($view, '/') !== false) {
            $controller = substr($view, 0, strrpos($view, "/"));
            $view = substr($view, strrpos($view, "/"));
        }
        $this->getParameters($parameters);
        $this->getContent($controller, $view, $this->data);
    }

    public function renderFile(MFile $file)
    {
        Manager::getPage()->window($file->getURL());
        $this->setResult(new MBrowserFile($file));
    }

    public function renderWindow($viewName = '', $parameters = array())
    {
        $this->renderContent($viewName, $parameters);
        $this->setResult(new MBrowserWindow());
    }

    public function render($viewName = '', $parameters = array())
    {
        $this->encryptData();

        $this->renderContent($viewName, $parameters);
        if (Manager::isAjaxCall()) {
            $this->setResult(new MRenderJSON());
        } else {
            $this->setResult(new MRenderPage());
        }
    }

    public function prepareFlush()
    {
        Manager::getFrontController()->response->prepareFlush();
    }

    public function flush($output)
    {
        Manager::getFrontController()->response->sendFlush($output);
    }

    public function renderFlush($viewName = '', $parameters = array())
    {
        Manager::getPage()->clearContent();
        $this->renderContent($viewName, $parameters);
        $output = Manager::getPage()->generate();
        $this->flush($output);
    }

    protected function log($message, $operation = 'default')
    {
        if ($this->logger === null) {
            $this->logger = Manager::getModelMAD('log');
        }

        $idUser = \Manager::getLogin() ? \Manager::getLogin()->getIdUser() : 0;
        $message .= ' - IP: ' . MUtil::getClientIP();
        $this->logger->log($operation, get_class($this), 0, $message, $idUser);
    }

    /**
     * Vasculha o $this->data para encontrar campos que precisam ser criptografados.
     */
    private function encryptData()
    {
        $this->cryptIterator(function ($plain, $token) {
            return \MSSL::simmetricEncrypt($plain, $token);
        });
    }

    /**
     * Vasculha o $this->data para encontrar campos que precisam ser descriptografados.
     */
    private function decryptData()
    {
        if (!\Manager::getRequest()->getIsPostRequest()) {
            return;
        }

        $this->cryptIterator(function ($encrypted, $token) {
            return \MSSL::simmetricDecrypt($encrypted, $token);
        });
    }

    /**
     * Função que itera o $this->encryptedFields e encontra os campos que devem ser criptografados ou decriptografados.
     * @param \Closure $function
     * @throws \ESecurityException
     */
    private function cryptIterator(\Closure $function)
    {
        $token = \Manager::getSessionToken();

        foreach ($this->encryptedFields as $field) {
            if (isset($this->data->{$field})) {
                $result = $function($this->data->{$field}, $token);

                if ($result === false) {
                    throw new \ESecurityException("[cryptError]{$this->getName()}Controller::{$field}");
                }

                $this->data->{$field} = $result;
            }
        }
    }

}

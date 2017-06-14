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
 * A URL tem o seguinte formato:
 * http://host.domain[:port]/(path/)(index.php)(app/)[(module/)](controller/)(action/)(id/)?querystring.
 * Arquivos são acessados diretamente, já que o Maestro é instalado em uma pasta acessível pelo servidor web
 */
class MContext
{

    /**
     * Método HTTP
     * @var string
     */
    private $method;

    /**
     * HTTP ContentType
     * @var string
     */
    private $contentType;

    /**
     * HTTP accepted results
     * @var string
     */
    private $resultFormat;

    /**
     * Se é uma chamada Ajax
     * @var string
     */
    private $isAjax;

    /**
     * Current application
     * @var string
     */
    private $isCore;

    /**
     * Is file Upload? (Maestro1)
     * @var string
     */
    private $isFileUpload;

    /**
     * Current application
     * @var string
     */
    private $app;

    /**
     * Current module path
     * @var string
     */
    private $module;

    /**
     * Current controller from path
     * @var string
     */
    private $controller;

    /**
     * Current component from path
     * @var string
     */
    private $component;

    /**
     * Current service from path
     * @var string
     */
    private $service;

    /**
     * Current api from path
     * @var string
     */
    private $api;

    /**
     * Current action from path
     * @var string
     */
    private $action;

    /**
     * Variable "id", if it exists
     * @var string
     */
    private $id;

    /**
     * Array with actions, if there is two or more
     * @var array
     */
    private $actionTokens;

    /**
     * Current action from $actionTokens
     * @var string
     */
    private $currentToken;

    /**
     * Variables passed on path and querystring
     * @var <type>
     */
    private $vars;

    /**
     * MRequest object
     * @var MRequest
     */
    private $request;

    /**
     *
     * @param type $request
     */
    private $actionPath;

    /**
     * Url
     * @var string
     */
    private $url;

    public function __construct($request)
    {
        $this->isCore = false;
        if (is_string($request)) {
            $path = $request;
            $this->url = $path;
        } else {
            $this->request = $request;
            if ($this->request->querystring != '') {
                parse_str($this->request->querystring, $this->vars);
            }
            $path = $this->request->getPathInfo();
            $this->url = $this->request->path;
            $this->method = $this->request->getMethod();
            $this->contentType = $this->request->getContentType();
            $this->resultFormat = strtoupper($this->request->getFormat());
            $this->isAjax = Manager::isAjaxCall();
        }
        $this->isFileUpload = (mrequest('__ISFILEUPLOAD') == 'yes');
        $pathParts = explode('/', $path);
        $part = null;
        $component = '';
        $app = array_shift($pathParts);
        if ($app != '') {
            if ($app == 'core') {
                $this->isCore = true;
                $this->app = $app = array_shift($pathParts);
            } else {
                $this->app = $app;
                // load app conf
                $configFile = Manager::getAbsolutePath("apps/{$this->app}/conf/conf.php");
                Manager::loadConf($configFile);
            }
            //
            $part = array_shift($pathParts);
            // check for module
            $namespace = $this->getNamespace($this->app, $part);
            if ($part && Manager::existsNS($namespace)) {
                $this->module = $part;
                $part = array_shift($pathParts);
            } else {
                $this->module = '';
            }
            // check for controller/component/service
            $ctlr = $part;
            $controller = $service = $api = $system = '';
            // first try via autoload
            $vendorAutoload = Manager::getAppPath("vendor/autoload_manager.php", "", $this->app);
            if (file_exists($vendorAutoload)) {
                Manager::loadAutoload($vendorAutoload);
                $ns = $this->app . '\\' . ($this->module ? $this->module . '\\' : '');
                $try = $ns . 'controllers\\' . $part . 'controller';
                if (class_exists($try)) {
                    $controller = $part;
                    $part = array_shift($pathParts);
                } else {
                    $try = $ns . 'services\\' . $part . 'service';
                    if (class_exists($try)) {
                        $service = $part;
                        $part = array_shift($pathParts);
                    } else {
                        $try = $ns . 'components\\' . $part;
                        if (class_exists($try)) {
                            $component = $part;
                            $part = array_shift($pathParts);
                        }
                    }
                }
            }
            // then, try via namespaces
            while ($part && (($controller == '') && ($component == '') && ($service == ''))) {
                $namespace = $this->getNamespace($this->app, $this->module, '', 'controllers');
                $ns = $namespace . $part . 'Controller.php';
                if (Manager::existsNS($ns)) {
                    $controller = $part;
                    $part = array_shift($pathParts);
                } else {
                    $namespace = $this->getNamespace($this->app, $this->module, '', 'services');
                    $ns = $namespace . $part . 'Service.php';
                    if (Manager::existsNS($ns)) {
                        $service = $part;
                        $part = array_shift($pathParts);
                    } else {
                        $namespace = $this->getNamespace($this->app, $this->module, '', 'components');
                        $ns = $namespace . $part . '.php';
                        if (Manager::existsNS($ns)) {
                            $component = $part;
                            $part = array_shift($pathParts);
                        } else {
                            $namespace = $this->getNamespace($this->app, $this->module, '', $part);
                            $dir = Manager::getNamespacePath($namespace);
                            //mdump($dir);
                            $nextPart = array_shift($pathParts);
                            if (is_dir($dir)) {
                                $api = $part;
                                $service = $nextPart;
                                $apiPart = $service;
                                $dir .= DIRECTORY_SEPARATOR . $service;
                                //mdump('>>>>'.$dir);
                                $nextPart = array_shift($pathParts);
                                if (is_dir($dir)) {
                                    $system = $service;
                                    $service = $nextPart;
                                    $dir .= DIRECTORY_SEPARATOR . $service;
                                    //mdump('>>>>'.$dir);
                                    $nextPart = array_shift($pathParts);
                                    if (is_dir($dir)) {
                                        $system = $apiPart . "\\" . $service;
                                        $service = $nextPart;
                                    }
                                } else {
                                    array_unshift($pathParts, $nextPart);
                                }
                            } else {
                                $part = $nextPart;//array_shift($pathParts);
                            }
                        }
                    }
                }
            }
        } else {
            $this->app = Manager::getOptions('startup');
            $controller = 'main';
        }
        if ($controller) {
            $this->controller = $controller;
        } elseif ($api) {
            $this->api = $api;
            $this->service = $service;
            $this->system = $system;
        } elseif ($service) {
            $this->service = $service;
        } elseif ($component) {
            $this->component = $component;
        } else {
            throw new ENotFoundException(_M("App: [%s], Module: [%s], Controller: [%s] : Not found!", array($this->app, $this->module, $ctlr)));
        }
        $this->action = ($part ?: ($component == '' ? 'main' : ''));
        $this->actionTokens[0] = $this->controller;
        $this->actionTokens[1] = $this->action;

        $this->currentToken = 1 + ($this->module ? 1 : 0);
        if ($n = count($pathParts)) {
            for ($i = 0; $i < $n; $i++) {
                $this->actionTokens[$i + 2] = $this->vars[$pathParts[$i]] = $pathParts[$i];
            }
        }
        $this->id = $this->vars['item'] ?: $this->actionTokens[2];
        if ($this->id !== '') {
            $_REQUEST['id'] = $this->id;
        }
        Manager::getInstance()->application = $this->app;

        mtrace('Context [[');
        mtrace('path: ' . $path);
        mtrace('method: ' . $this->method);
        mtrace('app: ' . $this->app);
        mtrace('module: ' . $this->module);
        mtrace('handler: ' . $this->getType() . '::' . $this->getTypeName());
        mtrace('action: ' . $this->action);
        mtrace('id: ' . $this->id);
        mtrace(']]');
    }

    public function isCore()
    {
        return $this->isCore;
    }

    public function isAjax()
    {
        return $this->isAjax;
    }

    public function isFileUpload()
    {
        return $this->isFileUpload;
    }

    public function isPost()
    {
        return ($this->method == 'POST');
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getResultFormat()
    {
        return $this->resultFormat;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function getApp()
    {
        return $this->app;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getService()
    {
        return $this->service;
    }

    public function getSystem()
    {
        return $this->system;
    }

    public function getComponent()
    {
        return $this->component;
    }

    public function getAPI()
    {
        return $this->api;
    }

    public function getType()
    {
        return ($this->controller ? 'controller' : ($this->api ? 'api' : ($this->service ? 'service' : ($this->component ? 'component' : ''))));
    }

    public function getTypeName()
    {
        return ($this->controller ?: ($this->api ? $this->service . " [{$this->system}]" : ($this->service ?: ($this->component ? : '?'))));
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getControllerAction()
    {
        return $this->controller . '.' . $this->action;
    }

    public function getNamespace($app, $module = '', $class = '', $type = 'controllers')
    {
        $ns = $this->isCore ? 'core::' : '';
        $ns .= 'apps::' . $app . '::';
        $ns .= (Manager::getOptions('srcPath') ? substr(Manager::getOptions('srcPath'), 1) . '::' : '');
        $ns .= ($module ? 'modules::' . $module . '::' : '');
        $ns .= (Manager::getConf("srcPath.{$module}") ? substr(Manager::getConf("srcPath.{$module}"), 1) . '::' : '');
        $ns .= $type . '::' . $class;
        return $ns;
    }

    public function get($name)
    {
        return $this->vars[$name];
    }

    public function getVar($name)
    {
        return $this->vars[$name];
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function setStartup($value)
    {
        $this->startup = $value;
    }

    public function shiftAction()
    {
        $action = $this->currentToken < count($this->actionTokens) ? $this->actionTokens[$this->currentToken++] : NULL;
        return $action;
    }

    public function pushAction($action)
    {
        $this->actionPath = $action;
        $this->actionTokens = explode(':', $action);
        $this->currentToken = 0;
        if ($this->actionTokens[0] == 'main') {
            ++$this->currentToken;
        }
    }

    public function buildURL($action = '', $parameters = array())
    {
        //mtrace('buildURL = ' . $action);
        //mtrace($parameters);
        $app = Manager::getApp();
        $module = Manager::getModule();

        if ($action{0} == '@') {
            $url = Manager::getAppURL($app);
            $action = substr($action, 1);
        } elseif ($action{0} == '>') {
            $url = Manager::getAppURL($app);
            $action = substr($action, 1);
        } elseif ($action{0} == '#') {
            $url = Manager::getStaticURL();
            $action = substr($action, 1);
        } else {
            $url = Manager::getAppURL($app);
        }
//mtrace('url = '. $url);
        $path = '';
        //mtrace("============== buildURL ============");
        //mtrace('*action = ' . $action);
        if ($p = strpos($action, '/')) {
            $index = substr($action, 0, $p);
            $action = substr($action, $p + 1);
        } else {
            $index = ($module != '') ? $module : $app;
        }
        //    mtrace('index = ' . $index);
        //    mtrace('action = ' . $action);
        //$action = str_replace('.','/', $action);
        $path = '/' . $action;

        // mtrace('*index = ' . $index);
        //    mtrace('app = ' . $app);
        if ($index != $app) {
            $path = '/' . $index . $path;
        }
        //mtrace('*path = ' . $path);

        if (count($parameters)) {
            $query = http_build_query($parameters);
            $path .= ((strpos($path, '?') === false) ? '?' : '') . $query;
        }

        $url .= $path;
        //mtrace('url = ' . $url);
        //mtrace("============ end getURL ============");
        return $url;
    }

}

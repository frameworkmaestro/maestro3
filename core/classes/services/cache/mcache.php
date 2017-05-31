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

class MCache extends MService
{
    public $type;
    public $cache;

    public function __construct($type = 'php')
    {
        parent::__construct();
        $this->type = $type;
        $class = "MCache" . $type;
        $this->cache = new $class();
    }

    public function add($name, $value, $ttl = 0)
    {
        $this->cache->set($name, $value, $ttl);
    }

    public function set($name, $value, $ttl = 0)
    {
        $this->cache->set($name, $value, $ttl);
    }

    public function get($name)
    {
        return $this->cache->get($name);
    }

    public function delete($name)
    {
        $this->cache->delete($name);
    }

    public function clear()
    {
        $this->cache->clear();
    }
}


class MCachePHP extends MService
{
    public $session;

    public function __construct()
    {
        parent::__construct();
        $this->session = $this->manager->getSession();
    }

    public function add($name, $value, $ttl = 0)
    {
        $this->session->set($name, $value);
    }

    public function set($name, $value, $ttl = 0)
    {
        $this->session->set($name, $value);
    }

    public function get($name)
    {
        return $this->session->get($name);
    }

}

class MCacheJava extends MService
{
    public $store;

    public function __construct()
    {
        parent::__construct();
        $this->store = $this->manager->javaServletContext;
    }

    public function add($name, $value)
    {
        $this->store->setAttribute($name, $value);
    }

    public function set($name, $value)
    {
        $this->store->setAttribute($name, $value);
    }

    public function get($name)
    {
        return java_values($this->store->getAttribute($name));
    }

}

class MCacheAPC extends MService
{
    public $defaulTTL;

    public function __construct()
    {
        parent::__construct();
        if(!function_exists('apc_cache_info') || !($cache=@apc_cache_info($cache_mode))) {
        	echo "No cache info available.  APC does not appear to be running.";
            exit;
        }
        $this->defaultTTL = $this->getConf('cache.apc.default.ttl');
    }

   public function add($name, $value, $ttl = 0)
    {
        $value = serialize($value);
        apc_add($name, $value, $ttl? $ttl :$this->defaultTTL);
    }

    public function set($name, $value, $ttl = 0)
    {
        $value = serialize($value);
        apc_store($name, $value, $ttl? $ttl :$this->defaultTTL);
    }

    public function get($name)
    {
        $value = apc_fetch($name);
        return unserialize($value);
    }

    public function delete($name)
    {
        apc_delete($name);
    }

    public function clear()
    {
        apc_clear_cache();
    }

}

class MCacheMemCache extends MService
{
    public $defaulTTL;
    public $memcache;
    public $sessionid;

    public function __construct()
    {
        parent::__construct();
        $this->memcache = new MemCache;
        if (! $this->memcache->connect($this->getConf('cache.memcache.host'),$this->getConf('cache.memcache.port')))
        {
            die('Could not connect to MemCache!');
        }
        $this->defaultTTL = $this->getConf('cache.memcache.default.ttl');
        $this->sessionid = $this->manager->getSession()->getId();
    }

    public function add($name, $value, $ttl = 0)
    {
        $key = md5($this->sessionid . $name);
        $this->memcache->add($key, $value, '', $ttl? $ttl :$this->defaultTTL);
    }

    public function set($name, $value, $ttl = 0)
    {
        $key = md5($this->sessionid . $name);
        $result = $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $ttl? $ttl :$this->defaultTTL);
    }

    public function get($name)
    {
        $key = md5($this->sessionid . $name);
        return $this->memcache->get($key);
    }

    public function delete($name)
    {
        $key = md5($this->sessionid . $name);
        $this->memcache->delete($key);
    }

    public function clear()
    {
        $this->memcache->flush();
        $time = time()+1; //one second future
        while(time() < $time) {
          //sleep
        } 
    }
}

?>
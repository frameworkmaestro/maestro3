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
 * Classe genérica que define a interface dos serviços de cache.
 */
abstract class MCacheService
{

    /**
     * Factory Method para criação da instância correta do serviço de cache.
     *
     * @return MCacheService
     * @throws Exception
     */
    public static function getCacheService()
    {
        $serviceClass = 'M' . \Manager::getConf('cache.type');

        if (!class_exists($serviceClass)) {
            mdump("A definição do serviço de cache [$serviceClass] não foi encontrada. Carregando a implementação padrão.", 'error');
            return new \MNullCache();
        }

        if (method_exists($serviceClass, 'getInstance')) {
            return $serviceClass::getInstance();
        } else {
            return new $serviceClass;
        }
    }

    public abstract function add($name, $value, $ttl = -1);

    public abstract function set($name, $value, $ttl = -1);

    public abstract function get($name);

    public abstract function increment($name, $by = 1);

    public abstract function decrement($name, $by = 1);

    public abstract function delete($name);

    public abstract function deleteMultiple(array $keys);

    public abstract function clear();

    public abstract function getKeys($pattern = '*');

    public abstract function getAllKeys();

    public abstract function serviceIsAvailable();
}
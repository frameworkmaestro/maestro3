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
 * A <tt>ClassLoader</tt> is an autoloader for class files that can be
 * installed on the SPL autoload stack. It is a class loader that either loads only classes
 * of a specific namespace or all namespaces and it is suitable for working together
 * with other autoloaders in the SPL autoload stack.
 *
 * If no include path is configured through the constructor or {@link setIncludePath}, a ClassLoader
 * relies on the PHP <code>include_path</code>.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
class ClassLoader
{

    private $fileExtension = '.php';
    private $namespace;
    private $includePath;
    private $namespaceSeparator = '\\';

    /**
     * Creates a new <tt>ClassLoader</tt> that loads classes of the
     * specified namespace from the specified include path.
     *
     * If no include path is given, the ClassLoader relies on the PHP include_path.
     * If neither a namespace nor an include path is given, the ClassLoader will
     * be responsible for loading all classes, thereby relying on the PHP include_path.
     *
     * @param string $ns The namespace of the classes to load.
     * @param string $includePath The base include path to use.
     */
    public function __construct($ns = null, $includePath = null)
    {
        $this->namespace = $ns;
        $this->includePath = $includePath;
    }

    /**
     * Sets the namespace separator used by classes in the namespace of this ClassLoader.
     *
     * @param string $sep The separator to use.
     */
    public function setNamespaceSeparator($sep)
    {
        $this->namespaceSeparator = $sep;
    }

    /**
     * Gets the namespace separator used by classes in the namespace of this ClassLoader.
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Sets the base include path for all class files in the namespace of this ClassLoader.
     *
     * @param string $includePath
     */
    public function setIncludePath($includePath)
    {
        $this->includePath = $includePath;
    }

    /**
     * Gets the base include path for all class files in the namespace of this ClassLoader.
     *
     * @return string
     */
    public function getIncludePath()
    {
        return $this->includePath;
    }

    /**
     * Sets the file extension of class files in the namespace of this ClassLoader.
     *
     * @param string $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * Gets the file extension of class files in the namespace of this ClassLoader.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Registers this ClassLoader on the SPL autoload stack.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Removes this ClassLoader from the SPL autoload stack.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $classname The name of the class to load.
     * @return boolean TRUE if the class has been successfully loaded, FALSE otherwise.
     */
    public function loadClass($className)
    {
        if ($this->namespace !== null && strpos($className, $this->namespace . $this->namespaceSeparator) !== 0) {
            return false;
        }
        $file = ($this->includePath !== null ? $this->includePath . DIRECTORY_SEPARATOR : '')
            . str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $className)
            . $this->fileExtension;
        //$toLower = (\strpos($file,'Doctrine') === false) && (\strpos($file,'apps') === false) && (\strpos($file,'modules') === false);
        if (!file_exists($file)) {
            $file = strtolower($file);
        }
        if (!file_exists($file)) {
            //mtracestack();
            mtrace('ClassLoader: file doesnt exists - ' . $file);
            return false;
        }
        require_once $file;
        return true;
    }

    /**
     * Asks this ClassLoader whether it can potentially load the class (file) with
     * the given name.
     *
     * @param string $className The fully-qualified name of the class.
     * @return boolean TRUE if this ClassLoader can load the class, FALSE otherwise.
     */
    public function canLoadClass($className)
    {
        if ($this->namespace !== null && strpos($className, $this->namespace . $this->namespaceSeparator) !== 0) {
            return false;
        }

        $file = str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $className) . $this->fileExtension;

        if ($this->includePath !== null) {
            return file_exists($this->includePath . DIRECTORY_SEPARATOR . $file);
        }

        return self::fileExistsInIncludePath($file);
    }

    /**
     * Checks whether a class with a given name exists. A class "exists" if it is either
     * already defined in the current request or if there is an autoloader on the SPL
     * autoload stack that is a) responsible for the class in question and b) is able to
     * load a class file in which the class definition resides.
     *
     * If the class is not already defined, each autoloader in the SPL autoload stack
     * is asked whether it is able to tell if the class exists. If the autoloader is
     * a <tt>ClassLoader</tt>, {@link canLoadClass} is used, otherwise the autoload
     * function of the autoloader is invoked and expected to return a value that
     * evaluates to TRUE if the class (file) exists. As soon as one autoloader reports
     * that the class exists, TRUE is returned.
     *
     * Note that, depending on what kinds of autoloaders are installed on the SPL
     * autoload stack, the class (file) might already be loaded as a result of checking
     * for its existence. This is not the case with a <tt>ClassLoader</tt>, who separates
     * these responsibilities.
     *
     * @param string $className The fully-qualified name of the class.
     * @return boolean TRUE if the class exists as per the definition given above, FALSE otherwise.
     */
    public static function classExists($className)
    {
        if (class_exists($className, false)) {
            return true;
        }

        foreach (spl_autoload_functions() as $loader) {
            if (is_array($loader)) { // array(???, ???)
                if (is_object($loader[0])) {
                    if ($loader[0] instanceof ClassLoader) { // array($obj, 'methodName')
                        if ($loader[0]->canLoadClass($className)) {
                            return true;
                        }
                    } else if ($loader[0]->{$loader[1]}($className)) {
                        return true;
                    }
                } else if ($loader[0]::$loader[1]($className)) { // array('ClassName', 'methodName')
                    return true;
                }
            } else if ($loader instanceof \Closure) { // function($className) {..}
                if ($loader($className)) {
                    return true;
                }
            } else if (is_string($loader) && $loader($className)) { // "MyClass::loadClass"
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the <tt>ClassLoader</tt> from the SPL autoload stack that is responsible
     * for (and is able to load) the class with the given name.
     *
     * @param string $className The name of the class.
     * @return The <tt>ClassLoader</tt> for the class or NULL if no such <tt>ClassLoader</tt> exists.
     */
    public static function getClassLoader($className)
    {
        foreach (spl_autoload_functions() as $loader) {
            if (is_array($loader) && $loader[0] instanceof ClassLoader && $loader[0]->canLoadClass($className)
            ) {
                return $loader[0];
            }
        }

        return null;
    }

    /**
     * @param string $file The file relative path.
     * @return boolean Whether file exists in include_path.
     */
    public static function fileExistsInIncludePath($file)
    {
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $dir) {
            if (file_exists($dir . DIRECTORY_SEPARATOR . $file)) {
                return true;
            }
        }
        return false;
    }

}

<?php
namespace FMUP;

/**
 * Retrieve FMUP Version
 * @package FMUP
 */
class Version
{
    private static $instance;
    private $structure;

    /**
     * @return $this
     */
    final public static function getInstance()
    {
        if (!self::$instance) {
            $class = get_called_class();
            self::$instance = new $class;
        }
        return self::$instance;
    }

    /**
     * private construct - singleton
     */
    private function __construct()
    {
    }

    /**
     * private clone - singleton
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    /**
     * Get version name
     * @return string
     */
    public function get()
    {
        foreach ($this->getStructure()->packages as $package) {
            if ($package->name == 'fmup/fmup') {
                return $package->version;
            }
        }
        return "v0.0.0";
    }

    /**
     * Get composer json path
     * @return string
     * @codeCoverageIgnore
     */
    protected function getComposerPath()
    {
        return implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..', '..', '..', 'composer.lock'));
    }

    /**
     * Get composer file structure
     * @return Object
     * @throws Exception
     */
    protected function getStructure()
    {
        if (!$this->structure) {
            if (!is_file($this->getComposerPath())) {
                throw new Exception('composer.lock does not exist');
            }
            $this->structure = json_decode(file_get_contents($this->getComposerPath()));
            if (!$this->structure) {
                throw new Exception('composer.lock invalid structure');
            }
        }
        return $this->structure;
    }
}

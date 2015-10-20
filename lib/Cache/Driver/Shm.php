<?php
namespace FMUP\Cache\Driver;

use FMUP\Cache\CacheInterface;
use FMUP\Cache\Exception;

class Shm implements CacheInterface
{
    const SETTING_NAME = 'SETTING_NAME';
    const SETTING_SIZE = 'SETTING_SIZE';
    private $shmInstance = null;
    private $isAvailable = null;

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * constructor of File
     * @param array $settings
     */
    public function __construct($settings = array())
    {
        $this->setSettings($settings);
    }

    /**
     * Can define settings of the component
     * @param array $settings
     * @return $this
     */
    public function setSettings($settings = array())
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param string $setting
     * @param mixed $value
     * @return $this
     */
    public function setSetting($setting, $value)
    {
        $this->settings[$setting] = $value;
        return $this;
    }

    /**
     * Get a specific value of setting
     * @param string $setting
     * @return mixed
     */
    public function getSetting($setting)
    {
        return isset($this->settings[$setting]) ? $this->settings[$setting] : null;
    }

    /**
     * Internal method to secure a SHM name
     * @param string $name
     * @return int
     */
    private function secureName($name = null)
    {
        if (is_numeric($name)) {
            return (int)$name;
        }
        if (is_null($name)) {
            return 1;
        }
        return $this->stringToUniqueId($name);
    }

    /**
     * Convert string to a unique id
     * @param string $string
     * @return int
     */
    private function stringToUniqueId($string)
    {
        if (is_numeric($string)) {
            return (int)$string;
        }
        $length = strlen($string);
        $return = 0;
        for ($i = 0; $i < $length; $i++) {
            $return += ord($string{$i});
        }
        return (int)$length . '1' . $return;
    }

    /**
     * Get SHM resource
     * @return resource
     * @throws Exception
     */
    private function getShm()
    {
        if (!$this->isAvailable()) {
            throw new Exception('SHM is not available');
        }
        if (!$this->shmInstance) {
            $memorySize = $this->getSetting(self::SETTING_SIZE);
            $shmName = $this->secureName($this->getSetting(self::SETTING_NAME));
            $this->shmInstance = is_numeric($memorySize)
                ? shm_attach($shmName, (int)$memorySize)
                : shm_attach($shmName);
        }
        return $this->shmInstance;
    }

    /**
     * Retrieve stored value
     * @param string $key
     * @return mixed|null
     * @throws Exception
     */
    public function get($key)
    {
        if (!$this->isAvailable()) {
            throw new Exception('SHM is not available');
        }
        return ($this->has($key)) ? shm_get_var($this->getShm(), $key) : null;
    }

    /**
     * Check whether key exists in SHM
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function has($key)
    {
        if (!$this->isAvailable()) {
            throw new Exception('SHM is not available');
        }
        return shm_has_var($this->getShm(), $key);
    }

    /**
     * Remove a stored key if exists
     * @param string $key
     * @return $this
     * @throws Exception
     */
    public function remove($key)
    {
        if (!$this->isAvailable()) {
            throw new Exception('SHM is not available');
        }

        if ($this->has($key)) {
            if (!shm_remove_var($this->getShm(), $key)) {
                throw new Exception('Unable to delete key from cache Shm');
            }
        }
        return $this;
    }

    /**
     * Define a key in SHM
     * @param string $key
     * @param mixed $value
     * @throws Exception
     * @return $this
     */
    public function set($key, $value)
    {
        if (!$this->isAvailable()) {
            throw new Exception('SHM is not available');
        }

        if (!shm_put_var($this->getShm(), $key, $value)) {
            throw new Exception('Unable to define key into cache Shm');
        }
        return $this;
    }

    /**
     * Check whether apc is available
     * @return bool
     */
    public function isAvailable()
    {
        if (is_null($this->isAvailable)) {
            $this->isAvailable = function_exists('shm_attach');
        }
        return $this->isAvailable;
    }
}
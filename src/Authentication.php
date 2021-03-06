<?php

namespace FMUP;

/**
 * Description of Authentication
 *
 * @author sweffling
 */
class Authentication
{

    private static $instance = null;
    private $driver;

    /**
     * private constructor - design pattern Singleton
     */
    private function __construct()
    {
    }

    /**
     * private clone - design pattern Singleton
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    /**
     * @return $this
     */
    final public static function getInstance()
    {
        if (self::$instance === null) {
            $class = get_called_class();
            self::$instance = new $class;
        }
        return self::$instance;
    }

    /**
     * Get the driver to stock user in
     * @return Authentication\DriverInterface
     */
    public function getDriver()
    {
        if ($this->driver === null) {
            $this->driver = new Authentication\Driver\Session();
        }
        return $this->driver;
    }

    /**
     * Change driver used for authentication
     * @param Authentication\DriverInterface $driver
     * @return $this
     */
    public function setDriver(Authentication\DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Set a user in session
     * @param Authentication\UserInterface $user
     * @return $this
     */
    public function set(Authentication\UserInterface $user)
    {
        $this->getDriver()->set($user);
        return $this;
    }

    /**
     * Get user
     * @return Authentication\UserInterface|null $user
     */
    public function get()
    {
        return $this->getDriver()->get();
    }

    /**
     * Unset user from driver
     * @return $this
     */
    public function clear()
    {
        $this->getDriver()->clear();
        return $this;
    }

    /**
     * Login a user in session
     * @param Authentication\UserInterface $user
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public function login(Authentication\UserInterface $user, $login, $password)
    {
        if ($return = (bool)$user->auth($login, $password)) {
            $this->set($user);
        }
        return $return;
    }
}

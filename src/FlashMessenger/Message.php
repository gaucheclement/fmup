<?php
namespace FMUP\FlashMessenger;

/**
 * Description of Flash
 *
 * @author sweffling
 */
class Message
{

    private $message;
    private $type = self::TYPE_DEFAULT;

    const TYPE_DEFAULT = 'default';
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

    /**
     * Construct a new flash message
     * @param string $message
     * @param string $type
     */
    public function __construct($message, $type = self::TYPE_DEFAULT)
    {
        $this->setType($type)->setMessage($message);
    }

    /**
     * Set message property
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = (string)$message;
        return $this;
    }

    /**
     * Set type property
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $this->checkType($type);
        return $this;
    }

    /**
     * Get message property
     * @return string $message
     */
    public function getMessage()
    {
        return (string)$this->message;
    }

    /**
     * Get message property
     * @return string|null $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Checks if the type given is valid
     * @param string $type
     * @return string
     */
    private function checkType($type)
    {
        if ($type !== self::TYPE_DANGER &&
            $type !== self::TYPE_DEFAULT &&
            $type !== self::TYPE_INFO &&
            $type !== self::TYPE_SUCCESS &&
            $type !== self::TYPE_WARNING
        ) {
            return ($this->getType() !== null) ? $this->getType() : self::TYPE_DEFAULT;
        } else {
            return $type;
        }
    }
}

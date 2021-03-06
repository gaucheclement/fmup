<?php
namespace FMUP\ErrorHandler\Plugin;

class Log extends Abstraction
{
    public function canHandle()
    {
        return true;
    }

    public function handle()
    {
        $this->errorLog($this->getException()->getMessage());
        return $this;
    }

    /**
     * @param $message
     * @return bool
     * @codeCoverageIgnore
     */
    protected function errorLog($message)
    {
        return error_log((string)$message);
    }
}

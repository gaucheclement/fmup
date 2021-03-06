<?php
namespace FMUP\Logger\Channel;

use FMUP\Logger\Channel;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Class Error
 * @package FMUP\Logger\Channel
 */
class Error extends Channel
{
    const NAME = 'Error';

    private $projectVersion;

    public function configure()
    {
        $handler = new NativeMailerHandler(
            explode(',', $this->getConfig()->get('mail_support')),
            '[Erreur] ' . $this->getProjectVersion()->name(),
            $this->getConfig()->get('mail_robot'),
            Logger::CRITICAL
        );
        $handler->setFormatter(new HtmlFormatter());

        $this->getLogger()
            ->pushProcessor(new IntrospectionProcessor())
            ->pushProcessor(new WebProcessor())
            ->pushHandler($handler);
        return $this;
    }

    /**
     * @return \FMUP\ProjectVersion
     */
    public function getProjectVersion()
    {
        if (!$this->projectVersion) {
            $this->projectVersion = \FMUP\ProjectVersion::getInstance();
        }
        return $this->projectVersion;
    }

    /**
     * @param \FMUP\ProjectVersion $projectVersion
     * @return $this
     */
    public function setProjectVersion(\FMUP\ProjectVersion $projectVersion)
    {
        $this->projectVersion = $projectVersion;
        return $this;
    }
}

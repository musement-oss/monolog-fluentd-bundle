<?php

/*
 * This file is part of "musement/monolog-fluentd-bundle".
 *
 * (c) Musement S.p.A. <oss@musement.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Musement\MonologFluentdBundle\Monolog\Handler;

use Fluent\Logger\Entity;
use Fluent\Logger\FluentLogger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Musement\MonologFluentdBundle\Monolog\Exception\MusementMonologFluentdHandlerException;
use Psr\Log\InvalidArgumentException;

class FluentdHandler extends AbstractProcessingHandler
{
    const DEFAULT_TAG_FORMAT = '{{channel}}.{{level_name}}';

    /**
     * Maps Monolog log levels to PSR-3 (syslog) log values.
     *
     * @see https://tools.ietf.org/html/rfc5424
     */
    protected static $psr3Levels = [
        Logger::DEBUG => LOG_DEBUG,
        Logger::INFO => LOG_INFO,
        Logger::NOTICE => LOG_NOTICE,
        Logger::WARNING => LOG_WARNING,
        Logger::ERROR => LOG_ERR,
        Logger::CRITICAL => LOG_CRIT,
        Logger::ALERT => LOG_ALERT,
        Logger::EMERGENCY => LOG_EMERG,
    ];

    /** @var FluentLogger */
    protected $logger;

    /** @var string */
    protected $tagFormat = self::DEFAULT_TAG_FORMAT;

    /** @var bool */
    protected $exceptions = true;

    /**
     * FluentdHandler constructor.
     *
     * @param FluentLogger $logger An instance of FluentdLogger
     * @param int          $level  The minimum logging level at which this handler will be triggered
     * @param bool         $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(
        FluentLogger $logger,
        $level = Logger::DEBUG,
        $bubble = true
    ) {
        $this->logger = $logger;

        parent::__construct($level, $bubble);
    }

    /**
     * Get the internal FluentLogger instance.
     *
     * @return FluentLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param string $tagFormat
     */
    public function setTagFormat($tagFormat)
    {
        $this->tagFormat = $tagFormat;
    }

    /**
     * @param bool $exceptions
     */
    public function setExceptions($exceptions)
    {
        $this->exceptions = (bool) $exceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->logger->close();
    }

    /**
     * Converts Monolog levels to PSR-3 (Syslog) numeric values.
     *
     * @param string|int Level number (monolog)
     * @param mixed $level
     *
     * @throws InvalidArgumentException
     *
     * @return int Psr-3 level number
     */
    public static function toPsr3Level($level)
    {
        if (isset(static::$psr3Levels[$level])) {
            return static::$psr3Levels[$level];
        }

        throw new InvalidArgumentException(sprintf(
            'Level "%s" is not defined, use one among "%s".',
            $level,
            implode('", "', array_keys(static::$psr3Levels))
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function write(array $record)
    {
        unset($record['formatted']);

        $record['level'] = static::toPsr3Level($record['level']);

        try {
            $this->logger->post2(new Entity(
                $this->buildTag($record),
                $record,
                $record['datetime']->getTimestamp()
            ));
        } catch (\Exception $e) {
            if ($this->exceptions) {
                throw new MusementMonologFluentdHandlerException(
                    sprintf('An error occurred on fluentd side: "%s".', $e->getMessage()),
                    0,
                    $e
                );
            }
        }
    }

    /**
     * @param array $record
     *
     * @throws \LogicException
     *
     * @return string
     */
    protected function buildTag(array $record)
    {
        $tag = $this->tagFormat;
        if (!preg_match_all('/\{\{(.*?)\}\}/', $tag, $matches)) {
            return $tag;
        }

        /** @var array[] $matches */
        foreach ($matches[1] as $match) {
            if (isset($record[$match])) {
                $tag = str_replace("{{{$match}}}", $record[$match], $tag);
                continue;
            }

            throw new \LogicException(sprintf('No such field "%s" in the record', $record[$match]));
        }

        return $tag;
    }
}

<?php
/**
 * PSR16 Cache Adapter
 *
 * Copyright (c) 2017 http://pudelek.org.pl
 *
 * @license MIT License (MIT)
 *
 * For the full copyright and license information, please view source file
 * that is bundled with this package in the file LICENSE
 *
 * @author Marcin Pudełek <marcin@pudelek.org.pl>
 */


namespace mrcnpdlk\Psr16Cache;


use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Class Adapter
 *
 * @package mrcnpdlk\Psr16Cache
 */
class Adapter
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $oCache;
    /**
     * @var \Psr\Log\LoggerInterface|\Psr\Log\NullLogger
     */
    private $oLog;
    /**
     * @var array
     */
    private $tHashSalt = [];

    /**
     * Adapter constructor.
     *
     * @param \Psr\SimpleCache\CacheInterface|null $oCacheInstance
     * @param \Psr\Log\LoggerInterface|null        $oLogInterface
     */
    public function __construct(CacheInterface $oCacheInstance = null, LoggerInterface $oLogInterface = null)
    {
        $this->setLoggerInstance($oLogInterface);
        $this->setCacheInstance($oCacheInstance);

    }

    /**
     * Generating hash based on array of some keys
     *
     * @param array $tHashKeys
     *
     * @return string
     */
    public function genHash(array $tHashKeys)
    {
        return md5(json_encode(array_merge($tHashKeys, $this->tHashSalt)));
    }

    /**
     * Getting Cache instance
     *
     * @return \Psr\SimpleCache\CacheInterface
     */
    public function getCache()
    {
        return $this->oCache;
    }

    /**
     * Setting Cache instance
     *
     * @param \Psr\SimpleCache\CacheInterface|null $oCacheInstance
     *
     * @return $this
     */
    public function setCacheInstance(CacheInterface $oCacheInstance = null)
    {
        $this->oCache = $oCacheInstance;

        return $this;
    }

    /**
     * Setting hash salt parameters.
     *
     * @param array $tHashSalt
     *
     * @return $this
     */
    public function setHashSalt(array $tHashSalt)
    {
        $this->tHashSalt = $tHashSalt;

        return $this;
    }

    /**
     * Setting Logger instance
     *
     * @param \Psr\Log\LoggerInterface|null $LogInterface
     *
     * @return $this
     */
    public function setLoggerInstance(LoggerInterface $LogInterface = null)
    {
        $this->oLog = $LogInterface ?? new \Psr\Log\NullLogger();

        return $this;
    }

    /**
     * Using Cache for closure.
     * If $tHash key is null no cache is using.
     *
     * @param \Closure               $closure   Anonymous function getting data if cache is invalid or no cache
     * @param array|null             $tHashKeys Array of keys for cache hash
     * @param null|int|\DateInterval $ttl       Optional. The TTL value of this item. If no value is sent and
     *                                          the driver supports TTL then the library may set a default value
     *                                          for it or let the driver take care of that.
     *
     * @return mixed
     */
    public function useCache(\Closure $closure, array $tHashKeys = null, $ttl = null)
    {
        if (!is_null($tHashKeys)) {
            $hashKey = $this->genHash($tHashKeys);
            if ($this->getCache()) {
                if ($this->getCache()->has($hashKey)) {
                    $answer = $this->getCache()->get($hashKey,null);
                    $this->oLog->debug(sprintf('CACHE [%s]: getting from cache', $hashKey));
                } else {
                    $answer = $closure();
                    $this->getCache()->set($hashKey, $answer, $ttl);
                    $this->oLog->debug(sprintf('CACHE [%s]: old, reset', $hashKey));
                }
            } else {
                $this->oLog->debug(sprintf('CACHE [%s]: not implemented', $hashKey));
                $answer = $closure();
            }
        } else {
            $answer = $closure();
        }

        return $answer;
    }
}

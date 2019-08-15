<?php

namespace Integration\ThirdPart;

use DateTime;
use DateTimeInterface;
use Integration\ThirdPart\Exceptions\CustomException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class DecoratorDataProvider implements DataProviderInterface
{

    /** @var \Psr\Cache\CacheItemPoolInterface */
    protected $cache;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Integration\DataProviderInterface */
    protected $dataProvider;

    /** @var \DateTimeInterface | null */
    protected $expiresAt;

    /** @var string */
    private $cachePrefix = 'prefix_';

    /**
     * @param \Integration\ThirdPart\DataProviderInterface $dataProvider
     * @param \Psr\Cache\CacheItemPoolInterface            $cache
     * @param \Psr\Log\LoggerInterface                     $logger
     *
     * @throws \Exception
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->dataProvider = $dataProvider;
        $this->cache        = $cache;
        $this->logger       = $logger;

        $this->setExpiresAt((new DateTime())->modify('+1 day'));
    }

    /**
     * Get and cache response from third-part service
     *
     * @inheritDoc
     *
     * @throws CustomException
     */
    public function getResponse(ParamsBuilder $paramsBuilder): array
    {
        try {
            $cacheItem = $this->cache->getItem($this->getCacheKey($paramsBuilder->toArray()));

            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $response = $this->dataProvider->get($paramsBuilder);

            $cacheItem
                ->set($response)
                ->expiresAt($this->expiresAt);

            return $response;
        } catch (CustomException $e) {
            //add logic for custom exception

            $this->logger->critical(sprintf('Informative text [%s]%s', $e->getCode(), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * Get key from cache
     *
     * @author Vitaliy Panait <panait.v@yandex.ru>
     *
     * @param  array  $input
     *
     * @return string
     */
    public function getCacheKey(array $input): string
    {
        return $this->cachePrefix . hash('md5', json_encode($input));
    }

    /**
     * @author Vitaliy Panait <panait.v@yandex.ru>
     *
     * @param  \DateTimeInterface | null  $expiresAt
     */
    public function setExpiresAt(?DateTimeInterface $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}

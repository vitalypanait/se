<?php

namespace Integration\ThirdPart;

class DataProvider implements DataProviderInterface
{

    /** @var string */
    private $host;

    /** @var string */
    private $user;

    /** @var string */
    private $password;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->host     = $config->getHost();
        $this->user     = $config->getUser();
        $this->password = $config->getPassword();
    }

    /**
     * @inheritDoc
     */
    public function getResponse(ParamsBuilder $paramsBuilder): array
    {
        // returns a response from external service
    }
}

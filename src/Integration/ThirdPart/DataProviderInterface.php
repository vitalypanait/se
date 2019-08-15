<?php

namespace Integration\ThirdPart;

interface DataProviderInterface
{

    /**
     * Get response from service
     *
     * @author Vitaliy Panait <panait.v@yandex.ru>
     *
     * @param  ParamsBuilder  $paramsBuilder
     *
     * @return array
     */
    public function getResponse(ParamsBuilder $paramsBuilder): array;
}

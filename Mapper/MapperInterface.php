<?php

namespace Bilyiv\RequestDataBundle\Mapper;

use Bilyiv\RequestDataBundle\Exception\NotSupportedFormatException;
use Bilyiv\RequestDataBundle\RequestDataInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Vladyslav Bilyi <beliyvladislav@gmail.com>
 */
interface MapperInterface
{
    /**
     * Map request to certain object.
     *
     * @param Request              $request
     * @param RequestDataInterface $requestDataObject
     *
     * @throws NotSupportedFormatException
     */
    public function map(Request $request, RequestDataInterface $requestDataObject): void;
}

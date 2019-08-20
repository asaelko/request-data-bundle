<?php

namespace Bilyiv\RequestDataBundle\Event;

use Bilyiv\RequestDataBundle\RequestDataInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Vladyslav Bilyi <beliyvladislav@gmail.com>
 */
class FinishEvent extends Event
{
    /**
     * @var RequestDataInterface
     */
    protected $requestData;

    public function __construct(RequestDataInterface $requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * @return RequestDataInterface
     */
    public function getRequestData(): RequestDataInterface
    {
        return $this->requestData;
    }
}

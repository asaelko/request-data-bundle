<?php

namespace Bilyiv\RequestDataBundle\EventListener;

use Bilyiv\RequestDataBundle\Event\FinishEvent;
use Bilyiv\RequestDataBundle\Events;
use Bilyiv\RequestDataBundle\Exception\NotSupportedFormatException;
use Bilyiv\RequestDataBundle\Mapper\MapperInterface;
use Bilyiv\RequestDataBundle\RequestDataInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Vladyslav Bilyi <beliyvladislav@gmail.com>
 */
class ControllerListener
{
    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(MapperInterface $mapper, EventDispatcherInterface $dispatcher)
    {
        $this->mapper = $mapper;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws NotSupportedFormatException
     * @throws \ReflectionException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!\is_array($controller)) {
            return;
        }

        $controllerClass = new \ReflectionClass($controller[0]);
        if ($controllerClass->isAbstract()) {
            return;
        }

        $parameters = $controllerClass->getMethod($controller[1])->getParameters();
        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();

            if (null !== $class && in_array(RequestDataInterface::class, $class->getInterfaceNames())) {
                $request = $event->getRequest();

                $object = $class->newInstance();

                $this->mapper->map($request, $object);

                $request->attributes->set($parameter->getName(), $object);

                $this->dispatcher->dispatch(Events::FINISH, new FinishEvent($object));

                break;
            }
        }
    }
}

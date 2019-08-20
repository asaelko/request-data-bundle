<?php

namespace Bilyiv\RequestDataBundle\Mapper;

use Bilyiv\RequestDataBundle\Exception\NotSupportedFormatException;
use Bilyiv\RequestDataBundle\Extractor\ExtractorInterface;
use Bilyiv\RequestDataBundle\Formats;
use Bilyiv\RequestDataBundle\FormatSupportableInterface;
use Bilyiv\RequestDataBundle\RequestDataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Vladyslav Bilyi <beliyvladislav@gmail.com>
 */
class Mapper implements MapperInterface
{
    /**
     * @var ExtractorInterface
     */
    private $extractor;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(
        ExtractorInterface $extractor,
        SerializerInterface $serializer,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->extractor = $extractor;
        $this->serializer = $serializer;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Request $request, RequestDataInterface $requestDataObject): void
    {
        $format = $this->extractor->extractFormat($request);
        $formatSupportable = $requestDataObject instanceof FormatSupportableInterface;
        if (!$format || ($formatSupportable && !\in_array($format, $requestDataObject::getSupportedFormats()))) {
            throw new NotSupportedFormatException();
        }

        $data = $this->extractor->extractData($request, $format);
        if (!$data) {
            return;
        }

        if (Formats::FORM === $format && \is_array($data)) {
            $this->mapForm($data, $requestDataObject);
            return;
        }

        $this->serializer->deserialize($data, \get_class($requestDataObject), $format, ['object_to_populate' => $requestDataObject]);
    }

    /**
     * @param array                $data
     * @param RequestDataInterface $requestDataObject
     */
    protected function mapForm(array $data, RequestDataInterface $requestDataObject): void
    {
        foreach ($data as $propertyPath => $propertyValue) {
            if ($this->propertyAccessor->isWritable($requestDataObject, $propertyPath)) {
                $this->propertyAccessor->setValue($requestDataObject, $propertyPath, $propertyValue);
            }
        }
    }
}

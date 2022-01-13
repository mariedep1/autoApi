<?php

namespace App\Serializer;
use App\Entity\Photo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class PhotoNormalizer implements ContextAwareNormalizerInterface
{
    private $requestStack;
    private $normalizer;

    public function __construct(RequestStack $requestStack, ObjectNormalizer $normalizer)
    {
        $this->requestStack = $requestStack;
        $this->normalizer = $normalizer;
    }

    private const ALREADY_CALLED = 'PHOTO_NORMALIZER_ALREADY_CALLED';

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data Instanceof Photo;
    }

    public function normalize($photo, string $format = null, array $context = [])
    {

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($photo, $format, $context);

        $request = $this->requestStack->getCurrentRequest();

        if(!empty($data['path'])){
            $data['path'] = $request->getUriForPath($data['path']);
        }

        return $data;
    }
}
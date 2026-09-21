<?php
declare(strict_types=1);

namespace App\Serializer;

use App\ValueObject\RoomCapacity;
use Symfony\Component\Serializer\Annotation\AsNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsNormalizer]
final class RoomCapacityNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): int
    {
        return $object->toInt();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof RoomCapacity;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [RoomCapacity::class => true];
    }
}
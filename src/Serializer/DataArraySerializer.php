<?php
namespace CherryneChou\Admin\Serializer;

use League\Fractal\Serializer\ArraySerializer;

/**
 * Class DataArraySerializer
 * @package App\Serializer
 */
class DataArraySerializer extends ArraySerializer
{
    /**
     * @param string|null $resourceKey
     * @param array $data
     * @return array[]
     */
    public function collection(?string $resourceKey, array $data) : array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    /**
     * @param string|null $resourceKey
     * @param array $data
     * @return array|array[]
     */
    public function item(?string $resourceKey, array $data): array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    /**
     * @return array|null
     */
    public function null(): ?array
    {
        return [];
    }
}

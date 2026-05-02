<?php

namespace App\Services;

use App\Models\CafeModel;
use DateTimeImmutable;

class CafeService
{
    public function __construct(
        private readonly CafeModel $cafes = new CafeModel(),
    ) {
    }

    public function getCurrentCafe(): ?array
    {
        $cafeId = session('cafe_id');

        if (! is_numeric($cafeId)) {
            return null;
        }

        return $this->cafes->find((int) $cafeId);
    }

    public function getCurrentCafeId(): ?int
    {
        $cafeId = session('cafe_id');

        return is_numeric($cafeId) ? (int) $cafeId : null;
    }

    public function getPublicCafeByUsername(string $username): ?array
    {
        return $this->cafes->findByUsername($username);
    }

    public function getPublicCafeByCode(string $code): ?array
    {
        return $this->cafes->findByCode($code);
    }

    public function touchMenuUpdatedAt(int $cafeId): void
    {
        $cafe = $this->cafes->find($cafeId);

        if ($cafe === null) {
            return;
        }

        $this->cafes->skipValidation(true)->update($cafeId, [
            'menu_updated_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ]);
    }
}

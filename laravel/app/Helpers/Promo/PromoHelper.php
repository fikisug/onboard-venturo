<?php

namespace App\Helpers\Promo;

use App\Helpers\Venturo;
use App\Models\PromoModel;
use Throwable;

class PromoHelper extends Venturo
{

    const PROMO_PHOTO_DIRECTORY = 'foto-promo';
    private $promo;

    public function __construct()
    {
        $this->promo = new PromoModel();
    }

    private function uploadAndGetPayload(array $payload)
    {
        if (!empty($payload['photo'])) {
            $fileName = $this->generateFileName($payload['photo'], 'PROMO_' . date('Ymdhis'));
            $photo = $payload['photo']->storeAs(self::PROMO_PHOTO_DIRECTORY, $fileName, 'public');
            $payload['photo'] = $photo;
        } else {
            unset($payload['photo']);
        }

        return $payload;
    }


    public function create(array $payload): array
    {
        // dd($payload['details']);
        try {
            $payload = $this->uploadAndGetPayload($payload);

            $this->beginTransaction();

            $promo = $this->promo->store($payload);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $promo
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    public function delete(string $promoId)
    {
        try {
            $this->beginTransaction();

            $this->promo->drop($promoId);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $promoId
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $promo = $this->promo->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $promo
        ];
    }


    public function getById(string $id): array
    {
        $promo = $this->promo->getById($id);
        if (empty($promo)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $promo
        ];
    }


    public function update(array $payload): array
    {
        try {
            $payload = $this->uploadAndGetPayload($payload);
            
            $this->beginTransaction();
            
            $this->promo->edit($payload, $payload['id']);
            
            $promo = $this->getById($payload['id']);
            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $promo['data']
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}

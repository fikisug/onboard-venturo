<?php

namespace App\Helpers\Promo;

use App\Helpers\Venturo;
use App\Models\DiscountModel;
use Throwable;

class DiscountHelper extends Venturo
{
  private $discount;

  public function __construct()
  {
      $this->discount = new DiscountModel();
  }

  public function create(array $payload): array
  {
      try {
          $discounts = $this->discount->store($payload);

          return [
              'status' => true,
              'data' => $discounts
          ];
      } catch (Throwable $th) {
          return [
              'status' => false,
              'error' => $th->getMessage()
          ];
      }
  }

  public function delete(string $id): bool
  {
      try {
          $this->discount->drop($id);

          return true;
      } catch (Throwable $th) {
          return false;
      }
  }

  public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
  {
      $discounts = $this->discount->getAll($filter, $itemPerPage, $sort);
      return [
          'status' => true,
          'data' => $discounts
      ];
  }

  public function getById(string $id): array
  {
      $discounts = $this->discount->getById($id);
      if (empty($discounts)) {
          return [
              'status' => false,
              'data' => null
          ];
      }

      return [
          'status' => true,
          'data' => $discounts
      ];
  }

  public function update(array $payload, string $id): array
  {
      try {
          $this->discount->edit($payload, $id);

          $discounts = $this->getById($id);

          return [
              'status' => true,
              'data' => $discounts['data']
          ];
      } catch (Throwable $th) {
          return [
              'status' => false,
              'error' => $th->getMessage()
          ];
      }
  }
}
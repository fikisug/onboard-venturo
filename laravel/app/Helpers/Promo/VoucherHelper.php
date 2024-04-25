<?php

namespace App\Helpers\Promo;

use App\Helpers\Venturo;
use App\Models\VoucherModel;
use Throwable;

class VoucherHelper extends Venturo
{
  private $voucher;

  public function __construct()
  {
      $this->voucher = new VoucherModel();
  }

  public function create(array $payload): array
  {
      try {
          $voucher = $this->voucher->store($payload);

          return [
              'status' => true,
              'data' => $voucher
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
          $this->voucher->drop($id);

          return true;
      } catch (Throwable $th) {
          return false;
      }
  }

  public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
  {
      $categories = $this->voucher->getAll($filter, $itemPerPage, $sort);

      return [
          'status' => true,
          'data' => $categories
      ];
  }

  public function getById(string $id): array
  {
      $voucher = $this->voucher->getById($id);
      if (empty($voucher)) {
          return [
              'status' => false,
              'data' => null
          ];
      }

      return [
          'status' => true,
          'data' => $voucher
      ];
  }

  public function update(array $payload, string $id): array
  {
      try {
          $this->voucher->edit($payload, $id);

          $voucher = $this->getById($id);

          return [
              'status' => true,
              'data' => $voucher['data']
          ];
      } catch (Throwable $th) {
          return [
              'status' => false,
              'error' => $th->getMessage()
          ];
      }
  }
}
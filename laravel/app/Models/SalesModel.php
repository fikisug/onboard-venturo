<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalesModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    public $timestamps = true;
    protected $fillable = [
        'no_struk',
        'm_customer_id',
        'm_voucher_id',
        'voucher_nominal',
        'm_discount_id',
        'date',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    protected $table = 't_sales';

    public function customer()
    {
        return $this->hasOne(CustomerModel::class, 'id', 'm_customer_id');
    }

    public function details()
    {
        return $this->hasMany(SalesDetailModel::class, 't_sales_id', 'id');
    }

    public function voucher()
    {
        return $this->hasOne(VoucherModel::class, 'id', 'm_voucher_id');
    }

    public function discount()
    {
        return $this->hasOne(DiscountModel::class, 'id', 'm_discount_id');
    }

    public function getSalesByCustomerPerDate($customerId, $date)
    {
        $sales = $this->query()->with([
            'details',
        ]);

        $sales->where('m_customer_id', $customerId);
        $sales->whereDate('date', $date);

        return $sales->orderBy('no_struk')->get();
    }

    public function getSalesByCustomer($startDate, $endDate, $customer = '')
    {
        $sales = $this->query()->with([
            'customer',
            'details',
        ]);

        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        return $sales->orderByDesc('date')->get();
    }

    public function getSalesByCategory($startDate, $endDate, $category = '')
    {
        $sales = $this->query()->with([
            'details.product' => function ($query) use ($category) {
                if (!empty($category)) {
                    $query->where('m_product_category_id', $category);
                }
            },
            'details',
            'details.product.category'
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        return $sales->orderByDesc('date')->get();
    }

    public function getSalesPromo(int $itemPerPage = 0, string $sort = '', $startDate, $endDate, $customer = [], $promo = [])
    {
        $sales = $this->query()->with(['voucher', 'customer', 'voucher.promo']);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereBetween('date', [$startDate . ' 00:00:01', $endDate . ' 23:59:59']);
        }

        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }

        if (!empty($promo)) {
            $sales->where(function ($query) use ($promo) {
                $query->whereHas('voucher', function ($query) use ($promo) {
                    $query->where('m_promo_id', $promo);
                })->orWhereHas('discount', function ($query) use ($promo) {
                    $query->where('m_promo_id', $promo);
                });
            });
        }

        $sales->where(function ($query) {
            $query->whereNotNull('m_voucher_id')
                ->orWhereNotNull('m_discount_id');
        });

        $sort = $sort ?: 'id DESC';
        $sales->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $sales->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }


    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        // if (!empty($filter['name'])) {
        //     $user->where('name', 'LIKE', '%' . $filter['name'] . '%');
        // }

        // if (!empty($filter['m_product_category_id'])) {
        //     $user->where('m_product_category_id', '=', $filter['m_product_category_id']);
        // }

        // if ($filter['is_available'] != '') {
        //     $user->where('is_available', '=', $filter['is_available']);
        // }

        $sort = $sort ?: 'id DESC';
        $user->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $user->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }
}

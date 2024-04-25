<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalesDetailModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    public $timestamps = true;
    protected $fillable = [
        't_sales_id',
        'm_product_id',
        'm_product_detail_id',
        'total_item',
        'price',
        'discount_nominal'
    ];

    protected $casts = [
        'id' => 'string',
    ];

    protected $table = 't_sales_detail';

    public function sale()
    {
        return $this->hasOne(SalesModel::class, 'id', 't_sales_id');
    }

    public function product()
    {
        return $this->hasOne(ProductModel::class, 'id', 'm_product_id');
    }

    public function product_detail()
    {
        return $this->hasOne(ProductDetailModel::class, 'id', 'm_product_detail_id');
    }

    public function getTotalSaleByPeriod(string $startDate, string $endDate): int
    {
        $total = $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sale'))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
            })
            ->first()
            ->toArray();

        return $total['total_sale'] ?? 0;
    }

    public function getTotalPerMonth($month, $year)
    {
        $total = $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sale'))
            ->whereHas('sale', function ($query) use ($month, $year) {
                $query->whereMonth('date', '=', $month)
                    ->whereYear('date', '=', $year);
            })
            ->first()
            ->toArray();

        return $total['total_sale'] ?? 0;
    }

    public function getTotalPerDates($dates)
    {
        $salesData = $this->query()
            ->select(DB::raw('DATE(t_sales.date) as saleDate'), DB::raw('sum((total_item * price) - discount_nominal) as totalSale'))
            ->join('t_sales', 't_sales.id', '=', 't_sales_detail.t_sales_id') 
            ->whereRaw('t_sales.date >= "' . $dates['startDate'] . ' 00:00:01" and t_sales.date <= "' . $dates['endDate'] . ' 23:59:59"')
            ->groupBy('saleDate')
            ->orderBy('saleDate', 'ASC')
            ->get();

        return $salesData ?? [];
    }

    /**
     * Get list year of transaction in t_sales
     *
     * @return array
     */
    public function getListYear()
    {
        $sales   = new SalesModel();
        $years   = $sales->query()
            ->select(DB::raw('Distinct(year(date)) as year'))
            ->get()
            ->toArray();

        return array_map(function ($year) {
            return $year['year'];
        }, $years);
    }

    /**
     * Get All sales
     *
     * @return void
     * @param mixed $year
     */
    public function getTotalPerYears($year)
    {
        $total = $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sales'))
            ->whereHas('sale', function ($query) use ($year) {
                $query->where(DB::raw('year(date)'), '=', $year);
            })
            ->first()
            ->toArray();

        return $total['total_sales'] ?? 0;
    }

    /**
     * Get total sale in periode
     *
     * @param string $startDate
     * @param string $endDate
     * @return void
     */
    public function getTotalSaleByPeriode(string $startDate, string $endDate): int
    {
        $total = $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sales'))
            ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                $query->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
            })
            ->first()
            ->toArray();
        return $total['total_sales'] ?? 0;
    }


    public function getSalesTransaction(int $itemPerPage = 0, string $sort = '', $startDate, $endDate, $customer = [], $menu = [])
    {
        $salesDetail = $this->query()->with(['sale', 'product', 'product_detail']);

        if (!empty($startDate) && !empty($endDate)) {
            $salesDetail->whereHas('sale', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate . ' 00:00:01', $endDate . ' 23:59:59']);
            });
        }

        if (!empty($customer)) {
            $salesDetail->whereHas('sale', function ($query) use ($customer) {
                $query->whereIn('m_customer_id', $customer);
            });
        }

        if (!empty($menu)) {
            $salesDetail->where(function ($query) use ($menu) {
                $query->whereIn('m_product_id', $menu);
            });
        }

        $sort = $sort ?: 'id DESC';
        $salesDetail->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $salesDetail->paginate($itemPerPage)->appends('sort', $sort);
        // return $salesDetail->orderByDesc('id')->get();
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function dropByProductId(string $productId)
    {
        return $this->where('m_product_id', $productId)->delete();
    }


    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        if (!empty($filter['type'])) {
            $user->where('type', 'LIKE', '%' . $filter['type'] . '%');
        }

        if (!empty($filter['m_product_id'])) {
            $user->where('m_product_id', 'LIKE', '%' . $filter['m_product_id'] . '%');
        }

        $sort = $sort ?: 'm_product_category.index ASC';
        $user->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $user->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }
}

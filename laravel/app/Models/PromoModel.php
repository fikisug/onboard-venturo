<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    public $timestamps = true;
    protected $fillable = [
        'name',
        'status',
        'expired_in_day',
        'nominal_percentage',
        'nominal_rupiah',
        'term_conditions',
        'photo'
    ];

    protected $casts = [
        'id' => 'string',
    ];
    
    protected $table = 'm_promo';

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
        $promo = $this->query();

        if (!empty($filter['name'])) {
            $promo->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }
        
        if (!empty($filter['status'])) {
            $promo->where('status', '=', $filter['status']);
        }        

        $sort = $sort ?: 'id DESC';
        $promo->orderByRaw($sort);

        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $promo->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }
}

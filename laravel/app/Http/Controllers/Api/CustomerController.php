<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\CustomerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerCreateRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Http\Resources\Customer\CustomerCollection;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $customer;

    public function __construct()
    {
        $this->customer = new CustomerHelper();
    }

    /**
     * Delete data user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */
    public function destroy($id)
    {
        $user = $this->customer->delete($id);

        if (!$user) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($user, "User berhasil dihapus");
    }

    /**
     * Mengambil data user dilengkapi dengan pagination
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function index(Request $request)
    {
        $filter = [
            'id' => $request->id ?? '',
            'name' => $request->name ?? '',
            'is_verified' => $request->is_verified ?? '',
        ];
        $users = $this->customer->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new CustomerCollection($users['data']));
    }

    /**
     * Menampilkan user secara spesifik dari tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */
    public function show($id)
    {
        $customer = $this->customer->getById($id);

        if (!($customer['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }
        return response()->success(new CustomerResource($customer['data']));
    }

    /**
     * Membuat data user baru & disimpan ke tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function store(CustomerCreateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
         
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'date_of_birth', 'photo', 'phone_number', 'is_verified']);
        $customer = $this->customer->create($payload);

        if (!$customer['status']) {
            return response()->failed($customer['error']);
        }

        return response()->success(new CustomerResource($customer['data']), "User berhasil ditambahkan");
    }

    /**
     * Mengubah data user di tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function update(CustomerUpdateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/UpdateRequest
         */

        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'date_of_birth', 'id', 'photo', 'phone_number', 'is_verified']);
        $customer = $this->customer->update($payload, $payload['id'] ?? '0');

        if (!$customer['status']) {
            return response()->failed($customer['error']);
        }

        return response()->success(new CustomerResource($customer['data']), "User berhasil diubah");
    }
}

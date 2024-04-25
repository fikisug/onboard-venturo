<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\RolesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleCreateRequest;
use App\Http\Requests\Role\RoleUpdateRequest;
use App\Http\Resources\Role\RoleCollection as RoleRoleCollection;
use App\Http\Resources\Role\RoleResource as RoleRoleResource;
use Hamcrest\Type\IsString;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isJson;
use function PHPUnit\Framework\isNull;

class RolesController extends Controller
{
    private $roles;

    public function __construct()
    {
        $this->roles = new RolesHelper();
    }

    /**
     * Delete data user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */
    public function destroy($id)
    {
        $role = $this->roles->delete($id);

        if (!$role) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($role, "User berhasil dihapus");
    }

    /**
     * Mengambil data user dilengkapi dengan pagination
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'access' => $request->email ?? '',
        ];
        $role = $this->roles->getAll($filter, 5, $request->sort ?? '');
        
        return response()->success(new RoleRoleCollection($role['data']));
    }

    /**
     * Menampilkan user secara spesifik dari tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */
    public function show($id)
    {
        $role = $this->roles->getById($id);

        if (!($role['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }
        return response()->success(new RoleRoleResource($role['data']));
    }

    /**
     * Membuat data user baru & disimpan ke tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function store(RoleCreateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        // dd($request->name);
        if(!$request->has(['name', 'access'])){
            return response()->failed('data tidak ada');
        }

        $payload = $request->only(['name', 'access']);
        $payload['access'] = json_encode($payload['access']);
        
        if (isset($request->validator) && $request->validator->fails() && !is_string($payload['access'])) {
            return response()->failed($request->validator->errors());
        }
        
        $role = $this->roles->create($payload);

        if (!$role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleRoleResource($role['data']), "User berhasil ditambahkan");
    }

    /**
     * Mengubah data user di tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function update(RoleUpdateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/UpdateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'access', 'id']);
        $role = $this->roles->update($payload, $payload['id'] ?? '0');

        if (!$role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleRoleResource($role['data']), "User berhasil diubah");
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Services\Department\DepartmentCrudService;
use App\Http\Services\Department\DepartmentQueryService;
use App\Http\Services\Department\DepartmentValidationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    private DepartmentCrudService $crudService;
    private DepartmentQueryService $queryService;
    private DepartmentValidationService $validationService;

    public function __construct(
        DepartmentCrudService $crudService,
        DepartmentQueryService $queryService,
        DepartmentValidationService $validationService
    )
    {
        $this->crudService = $crudService;
        $this->queryService = $queryService;
        $this->validationService = $validationService;
    }

    /**
     * GET /api/departments
     * Menampilkan semua departemen
     */
    public function index(Request $request)
    {
        try {
            $departments = $this->queryService->listDepartments($request->search);

            return response()->json([
                'message' => 'Daftar departemen berhasil diambil',
                'data' => $departments,
                'total' => $departments->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil daftar departemen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/departments
     * Menambah departemen baru
     */
    public function store(StoreDepartmentRequest $request)
    {
        try {
            $validated = $request->validated();

            // Buat departemen baru
            $department = $this->crudService->createDepartment($validated);

            return response()->json([
                'message' => 'Departemen berhasil ditambahkan',
                'data' => $department
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambah departemen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/departments/{id}
     * Menampilkan detail departemen
     */
    public function show($id)
    {
        try {
            $department = $this->queryService->getDepartmentById($id);

            if (!$department) {
                return response()->json([
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'message' => 'Detail departemen berhasil diambil',
                'data' => $department
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil detail departemen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT/PATCH /api/departments/{id}
     * Mengedit departemen
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        try {
            $department = $this->queryService->getDepartmentById($id);

            if (!$department) {
                return response()->json([
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }

            $validated = $request->validated();

            // Update departemen
            $department = $this->crudService->updateDepartment($department, $validated);

            return response()->json([
                'message' => 'Departemen berhasil diperbarui',
                'data' => $department
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui departemen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/departments/{id}
     * Menghapus departemen
     */
    public function destroy($id)
    {
        try {
            $department = $this->queryService->getDepartmentById($id);

            if (!$department) {
                return response()->json([
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }

            // Cek apakah ada user yang menggunakan departemen ini
            $canDelete = $this->validationService->canDelete($department);
            if (!$canDelete['valid']) {
                return response()->json([
                    'message' => $canDelete['message'],
                    'data' => [
                        'user_count' => $canDelete['user_count']
                    ]
                ], 422);
            }

            $this->crudService->deleteDepartment($department);

            return response()->json([
                'message' => 'Departemen berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus departemen',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

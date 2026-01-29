<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    /**
     * GET /api/departments
     * Menampilkan semua departemen
     */
    public function index(Request $request)
    {
        try {
            $departments = Department::when($request->search, function ($q, $search) {
                return $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

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
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:departments,name'
            ], [
                'name.required' => 'Nama departemen wajib diisi',
                'name.unique' => 'Nama departemen sudah ada',
                'name.max' => 'Nama departemen maksimal 255 karakter'
            ]);

            // Buat departemen baru
            $department = Department::create($validated);

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
            $department = Department::find($id);

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
    public function update(Request $request, $id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return response()->json([
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }

            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:departments,name,' . $id
            ], [
                'name.required' => 'Nama departemen wajib diisi',
                'name.unique' => 'Nama departemen sudah ada',
                'name.max' => 'Nama departemen maksimal 255 karakter'
            ]);

            // Update departemen
            $department->update($validated);

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
            $department = Department::find($id);

            if (!$department) {
                return response()->json([
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }

            // Cek apakah ada user yang menggunakan departemen ini
            if ($department->users()->exists()) {
                return response()->json([
                    'message' => 'Tidak dapat menghapus departemen yang memiliki user',
                    'data' => [
                        'user_count' => $department->users()->count()
                    ]
                ], 422);
            }

            // Hapus departemen
            $department->delete();

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

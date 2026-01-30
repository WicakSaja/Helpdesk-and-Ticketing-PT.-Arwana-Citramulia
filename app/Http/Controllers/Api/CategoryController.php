<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Mengambil semua kategori tiket
     */
    public function index(Request $request)
    {
        try {
            $categories = Category::when($request->search, function ($q, $search) {
                return $q->where('name', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'message' => 'Daftar kategori berhasil diambil',
                'data' => $categories,
                'total' => $categories->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil daftar kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/categories
     * Menambah kategori baru
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string|max:1000'
            ], [
                'name.required' => 'Nama kategori wajib diisi',
                'name.unique' => 'Nama kategori sudah ada',
                'name.max' => 'Nama kategori maksimal 255 karakter',
                'description.max' => 'Deskripsi maksimal 1000 karakter'
            ]);

            // Buat kategori baru
            $category = Category::create($validated);

            return response()->json([
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambah kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/categories/{id}
     * Mengambil detail kategori berdasarkan ID
     */
    public function show($id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'message' => 'Detail kategori berhasil diambil',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil detail kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT/PATCH /api/categories/{id}
     * Mengubah/update kategori
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string|max:1000'
            ], [
                'name.required' => 'Nama kategori wajib diisi',
                'name.unique' => 'Nama kategori sudah ada',
                'name.max' => 'Nama kategori maksimal 255 karakter',
                'description.max' => 'Deskripsi maksimal 1000 karakter'
            ]);

            // Update kategori
            $category->update($validated);

            return response()->json([
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/categories/{id}
     * Menghapus kategori
     */
    public function destroy($id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            // Cek apakah ada tiket yang menggunakan kategori ini
            if ($category->tickets()->exists()) {
                return response()->json([
                    'message' => 'Tidak dapat menghapus kategori yang memiliki tiket',
                    'data' => [
                        'ticket_count' => $category->tickets()->count()
                    ]
                ], 422);
            }

            // Hapus kategori
            $category->delete();

            return response()->json([
                'message' => 'Kategori berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

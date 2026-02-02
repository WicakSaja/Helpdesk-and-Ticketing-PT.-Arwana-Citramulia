<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Services\Category\CategoryCrudService;
use App\Http\Services\Category\CategoryQueryService;
use App\Http\Services\Category\CategoryValidationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    private CategoryCrudService $crudService;
    private CategoryQueryService $queryService;
    private CategoryValidationService $validationService;

    public function __construct(
        CategoryCrudService $crudService,
        CategoryQueryService $queryService,
        CategoryValidationService $validationService
    )
    {
        $this->crudService = $crudService;
        $this->queryService = $queryService;
        $this->validationService = $validationService;
    }

    /**
     * GET /api/categories
     * Mengambil semua kategori tiket
     */
    public function index(Request $request)
    {
        try {
            $categories = $this->queryService->listCategories($request->search);

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
    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            // Buat kategori baru
            $category = $this->crudService->createCategory($validated);

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
            $category = $this->queryService->getCategoryById($id);

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
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = $this->queryService->getCategoryById($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            $validated = $request->validated();

            // Update kategori
            $category = $this->crudService->updateCategory($category, $validated);

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
            $category = $this->queryService->getCategoryById($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            // Cek apakah ada tiket yang menggunakan kategori ini
            $canDelete = $this->validationService->canDelete($category);
            if (!$canDelete['valid']) {
                return response()->json([
                    'message' => $canDelete['message'],
                    'data' => [
                        'ticket_count' => $canDelete['ticket_count']
                    ]
                ], 422);
            }

            $this->crudService->deleteCategory($category);

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

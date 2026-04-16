<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class StudentController extends Controller
{
    protected StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * FUNGSI TAMPILKAN DATA
     * Menampilkan semua data mahasiswa
     */
    public function index(): JsonResponse
    {
        try {
            $students = $this->studentService->getAllStudents();

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diambil',
                'data'    => $students,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mahasiswa',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FUNGSI TAMBAH DATA
     * Menyimpan data mahasiswa baru
     * Dilengkapi: request validasi, error handling, service refactor
     */
    public function store(Request $request): JsonResponse
    {
        // Request Validasi
        try {
            $validated = $request->validate([
                'nama'    => 'required|string|max:100',
                'nim'     => 'required|string|max:20|unique:students,nim',
                'jurusan' => 'required|string|max:100',
                'email'   => 'required|email|unique:students,email',
                'no_hp'   => 'nullable|string|max:15',
            ], [
                'nama.required'    => 'Nama mahasiswa wajib diisi.',
                'nim.required'     => 'NIM wajib diisi.',
                'nim.unique'       => 'NIM sudah terdaftar.',
                'jurusan.required' => 'Jurusan wajib diisi.',
                'email.required'   => 'Email wajib diisi.',
                'email.email'      => 'Format email tidak valid.',
                'email.unique'     => 'Email sudah terdaftar.',
            ]);

        } catch (ValidationException $e) {
            // Error Handling - Validasi gagal
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        }

        // Service Refactor - proses disimpan via StudentService
        try {
            $student = $this->studentService->createStudent($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil ditambahkan',
                'data'    => $student,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data mahasiswa',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FUNGSI UBAH DATA
     * Mengupdate data mahasiswa berdasarkan ID
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $student = Student::findOrFail($id);

            $validated = $request->validate([
                'nama'    => 'sometimes|required|string|max:100',
                'nim'     => 'sometimes|required|string|max:20|unique:students,nim,' . $id,
                'jurusan' => 'sometimes|required|string|max:100',
                'email'   => 'sometimes|required|email|unique:students,email,' . $id,
                'no_hp'   => 'nullable|string|max:15',
            ]);

            $updated = $this->studentService->updateStudent($student, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diperbarui',
                'data'    => $updated,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data mahasiswa',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FUNGSI HAPUS DATA
     * Menghapus data mahasiswa berdasarkan ID
     */
    public function destroy($id): JsonResponse
    {
        try {
            $student = Student::findOrFail($id);
            $this->studentService->deleteStudent($student);

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil dihapus',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data mahasiswa',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}

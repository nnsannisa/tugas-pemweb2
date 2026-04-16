<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentService
{
    /**
     * Ambil semua data mahasiswa
     */
    public function getAllStudents()
    {
        return Student::latest()->get();
    }

    /**
     * Simpan data mahasiswa baru
     */
    public function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            return Student::create($data);
        });
    }

    /**
     * Update data mahasiswa
     */
    public function updateStudent(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $student->update($data);
            return $student->fresh();
        });
    }

    /**
     * Hapus data mahasiswa
     */
    public function deleteStudent(Student $student): bool
    {
        return DB::transaction(function () use ($student) {
            return $student->delete();
        });
    }
}

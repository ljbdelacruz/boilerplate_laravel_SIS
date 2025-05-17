<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                //Rule::unique('users', 'email') 
                //test
            ],
            'contact_number' => 'required|string|max:20', 
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ];
    }

    public function model(array $row)
    {
        try {
            return DB::transaction(function () use ($row) {
                // Find existing user by email
                $user = User::where('email', $row['email'])->first();
                if ($user) {
                    return null;
                } else {
                    $user = User::create([
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => Hash::make($row['contact_number']), 
                        'role' => 'teacher'
                    ]);

                    return Teacher::create([
                        'user_id' => $user->id,
                        'specialization' => $row['specialization'],
                        'bio' => $row['bio'] ?? null,
                        'contact_number' => $row['contact_number']
                    ]);
                }
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("Error processing row: " . json_encode($row) . "\nError: " . $e->getMessage());
        }
    }
}
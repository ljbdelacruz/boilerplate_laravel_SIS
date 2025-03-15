<?php

namespace App\Http\Controllers;

use App\Services\SchoolYearService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(title="School Year API", version="1.0.0")
 * @OA\PathItem(path="/api")
 */
class SchoolYearController extends Controller
{
    protected $schoolYearService;

    public function __construct(SchoolYearService $schoolYearService)
    {
        $this->schoolYearService = $schoolYearService;
    }

    /**
     * @OA\Post(
     *     path="/api/add-school-year",
     *     summary="Add a new school year",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"school_year", "grade_level", "section_name"},
     *             @OA\Property(property="school_year", type="string", example="2025-2026"),
     *             @OA\Property(property="grade_level", type="string", example="Grade 1"),
     *             @OA\Property(property="section_name", type="string", example="Section A")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="School year, grade level, and section added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="School year, grade level, and section added successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create school year",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to create school year"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'school_year' => 'required|string|unique:school_years,school_year',
                'grade_level' => 'required|string',
                'section_name' => 'required|string',
            ]);

            $result = $this->schoolYearService->createSchoolYear($validated);

            return response()->json([
                'message' => 'School year, grade level, and section added successfully.',
                'data' => $result
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create school year',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/school-years",
     *     summary="Get all school years",
     *     @OA\Response(
     *         response=200,
     *         description="School years retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="School years retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve school years",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to retrieve school years"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $schoolYears = $this->schoolYearService->getAllSchoolYears();
            
            if ($schoolYears->isEmpty()) {
                return response()->json([
                    'message' => 'No school years found',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'message' => 'School years retrieved successfully',
                'data' => $schoolYears
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve school years',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
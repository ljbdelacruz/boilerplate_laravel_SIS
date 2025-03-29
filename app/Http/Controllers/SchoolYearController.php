<?php

namespace App\Http\Controllers;

use App\Services\SchoolYearService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="School Year API",
 *     description="API endpoints for managing school years"
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local Development Server"
 * )
 * @OA\Tag(
 *     name="School Years",
 *     description="API Endpoints for School Year operations"
 * )
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
     *     operationId="storeSchoolYear",
     *     tags={"School Years"},
     *     summary="Add a new school year",
     *     description="Creates a new school year with grade level and section",
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
     *         description="School year created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="School year, grade level, and section added successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="school_year", type="string", example="2025-2026"),
     *                 @OA\Property(property="grade_level", type="string", example="Grade 1"),
     *                 @OA\Property(property="section_name", type="string", example="Section A"),
     *                 @OA\Property(property="created_at", type="string", format="datetime"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Server error")
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
     *     operationId="getSchoolYears",
     *     tags={"School Years"},
     *     summary="Get all school years",
     *     description="Returns a list of all school years",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="School years retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="school_year", type="string", example="2025-2026"),
     *                     @OA\Property(property="grade_level", type="string", example="Grade 1"),
     *                     @OA\Property(property="section_name", type="string", example="Section A"),
     *                     @OA\Property(property="created_at", type="string", format="datetime"),
     *                     @OA\Property(property="updated_at", type="string", format="datetime")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error")
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
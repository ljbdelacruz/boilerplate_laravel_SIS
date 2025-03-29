use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentsController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Create user first
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student'
            ]);

            // Then create student with user_id
            $student = Student::create([
                'student_id' => $request->student_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'guardian_name' => $request->guardian_name,
                'guardian_contact' => $request->guardian_contact,
                'school_year_id' => $request->school_year_id,
                'user_id' => $user->id
            ]);

            DB::commit();
            return redirect()->route('students.index')->with('success', 'Student created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create student. ' . $e->getMessage()])->withInput();
        }
    }
}
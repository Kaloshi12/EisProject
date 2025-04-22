<?php

namespace App\Http\Controllers;

use App\Customs\Services\EmailVerificationService;
use App\Models\ClassGroup;
use App\Models\Degree;
use App\Models\Departments;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(private EmailVerificationService $service)
    {
    }

    /**
     * Store a new user
     *
     * @operationId Store User
     *
     * @tags User
     */
    public function store(Request $request)
    {
        $roleId = $request->role_id;

        $rules = [
            'name' => 'required|string|max:30',
            'surname' => 'required|string|max:30',
            'email' => 'required|email|unique:users,email|regex:/^.+@epoka\.edu\.al$/',
            'secondary_email' => 'required|email|unique:users,secondary_email',
            'birthdate' => 'required|date|date_format:Y-m-d',
            'nationality' => 'required|string|max:20',
            'gender' => 'required|string',
            'blood_group' => 'required|string|max:2',
            'civil_status' => 'required|in:single,married',
        ];

        if ($roleId === config('constants.STUDENT_ROLE_ID')) {
            $rules['year_started'] = 'required|digits:4|integer';
            $rules['aptis_level'] = 'required|string|max:2';
            $rules['bourse_percentage'] = 'required|integer|min:0|max:100';
            $rules['supervised_name'] = 'required|string';
            $rules['supervised_surname'] = 'required|string';
            $rules['degree'] = 'required|string';
            $rules['department'] = 'required|string';
        } else if ($roleId === config('constants.LECTURER_ROLE_ID')) {
            $rules['department'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $inputs = $validator->validated();

        if ($roleId === config('constants.STUDENT_ROLE_ID')) {
            $supervisor = User::whereRaw('LOWER(name) = ?', [strtolower($inputs['supervised_name'])])
                ->whereRaw('LOWER(surname) = ?', [strtolower($inputs['supervised_surname'])])
                ->first();

            if (!$supervisor) {
                return response()->json(['error' => 'Supervisor not found'], 404);
            }
        }

        $institution_number = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $verification_code = \Illuminate\Support\Str::random(6);

        $userData = [
            'institution_number' => $institution_number,
            'id_cart_number' => chr(rand(65, 90)) . mt_rand(10000000, 99999999) . chr(rand(65, 90)),
            'name' => ucfirst(strtolower($inputs['name'])),
            'surname' => ucfirst(strtolower($inputs['surname'])),
            'email' => $inputs['email'],
            'secondary_email' => $inputs['secondary_email'],
            'birthdate' => $inputs['birthdate'],
            'nationality' => ucfirst(strtolower($inputs['nationality'])),
            'gender' => $inputs['gender'],
            'blood_group' => $inputs['blood_group'],
            'civil_status' => $inputs['civil_status'],
            'verification_code' => $verification_code,
            'password' => bcrypt('12345678'),
            'role_id' => $roleId,
        ];

        if ($roleId === config('constants.STUDENT_ROLE_ID')) {
            $degree = Degree::whereRaw('LOWER(name) = ?', [strtolower($inputs['degree'])])->first();
            if (!$degree) {
                return response()->json(['error' => 'Degree not found'], 404);
            }
            $userData['degree_id'] = $degree->id;

            $department = Departments::whereRaw('LOWER(name) = ?', [strtolower($inputs['department'])])->first();
            if (!$department) {
                return response()->json(['error' => 'Department not found'], 404);
            }
            $userData['department_id'] = $department->id;
            $userData['year_started'] = $inputs['year_started'];
            $userData['aptis_level'] = $inputs['aptis_level'];
            $userData['initial_bourse_percentage'] = $inputs['bourse_percentage'];
            $userData['supervised_id'] = $supervisor->id;
        } elseif ($roleId === config('constants.LECTURER_ROLE_ID')) {
            $department = Departments::whereRaw('LOWER(name) = ?', [strtolower($inputs['department'])])->first();
            if (!$department) {
                return response()->json(['error' => 'Department not found'], 404);
            }
            $userData['department_id'] = $department->id;
        }

        $user = User::create($userData);

        if ($user) {
            // Send verification link
            $this->service->sendVerificationLink($user);

            // Generate Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Successfully created',
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Failed to create user'], 500);
    }

    /**
     *  @operationId Delete User
     *
     * @tags User
     */
    public function deleteUser(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            /**
             * The user's identification details.
             *
             * @var array{name: string, surname: string, id_cart_number: string}
             *
             * @example {"name": "John", "surname": "Doe", "id_cart_number": "123456789"}
             */
            'name' => 'required|string',
            'surname' => 'required|string',
            'id_cart_number' => 'required|string',

        ]);

        $inputs = $validator->validated();
        $userToDelete = User::where('name', $inputs['name'])
            ->where('surname', $inputs['surname'])
            ->where('id_cart_number', $inputs['id_cart_number'])
            ->first();

        $user = Auth::user();

        if ($userToDelete) {
            if ($user->institution_number === $userToDelete->institution_number) {
                return response()->json([
                    'message' => 'Cannot delete yourself',
                ], 400);
            } else {
                try {
                    $userToDelete->delete();

                    return response()->json([
                        'message' => 'Successfully deleted',
                        'userDeleted' => $userToDelete,
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Failed to delete user',
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }
        } else {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }
}
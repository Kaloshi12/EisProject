<?php

namespace App\Http\Controllers;

use App\Customs\Services\EmailVerificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function __construct(private EmailVerificationService $service){}

/**
     * Store a newly created resource in storage.
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
            'civil_status' => 'required|in:single,maried',
        ];
    
        if ($roleId === config('constants.Student_ROLE_ID')) {
            $rules['year_started'] = 'required|digits:4|integer|between:2010,' . date('Y');
            $rules['aptis_level'] = 'required|string|max:2';
            $rules['bourse_percentage'] = 'required|integer|min:0|max:100';
            $rules['supervised_name'] = 'required|string';
            $rules['supervised_surname'] = 'required|string';
            $rules['degree_id'] = 'required|exists:degrees,id';
            $rules['group_id'] = 'required|exists:class_groups,id';
            $rules['department_id'] = 'required|exists:departments,id';

        }else if($roleId === config('constants.LECTURER_ROLE_ID')){
            $rules['department_id'] = 'required|exists:departments,id';
        }else{

        }
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $inputs = $validator->validated();
    
        if ($roleId === config('constants.Student_ROLE_ID')) {
            $supervisor = User::where('name', $inputs['supervised_name'])
                              ->where('surname', $inputs['supervised_surname'])
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
    
        if ($roleId === config('constants.Student_ROLE_ID')) {
            $userData['year_started'] = $inputs['year_started'];
            $userData['aptis_level'] = $inputs['aptis_level'];
            $userData['bourse_percentage'] = $inputs['bourse_percentage'];
            $userData['supervised_id'] = $supervisor->id;   
            $userData['degree_id'] = $inputs['degree_id'];
            $userData['group_id'] = $inputs['group_id'];    
            $userData['department_id'] = $inputs['department_id'];
        }
        if($roleId === config('constants.LECTURER_ROLE_ID')){
            $userData['department_id'] = $inputs['department_id'];
        }
    
        $user = User::create($userData);
    
        if ($user) {
            $this->service->sendVerificationLink($user);
           $token = auth()->login($user);      
            return response()->json([
                'message' => 'Successfully created',
                'user' => $user,
                'token' => $token
            ]);
        }
    
        return response()->json(['message' => "Failed to create user"], 500);
    }
    
            public function delete_user(Request $request){
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(),[
                    'name'=> 'required',
                    'surname'=>'required',
                    'id_cart_number'=>'required'
                ]);
                
                $inputs = $validator->validated();
                $userToDelete = User::where('name', $inputs['name'])
                                    ->where('surname', $inputs['surname'])
                                    ->where('id_cart_number', $inputs['id_cart_number'])
                                    ->first(); 
                
                $user = Auth::user();
                
                if($userToDelete){
                    if($user->institution_number === $userToDelete->institution_number){
                        return response()->json([
                            'message' => 'Cannot delete yourself'
                        ], 400);
                    }else{
                        try {
                            $userDeleted = $userToDelete->delete();
                            return response()->json([
                                'message' => 'Successfully deleted',
                                'userDeleted' => $userToDelete
                            ]);
                        } catch (\Exception $e) {
                            return response()->json([
                                'message' => 'Failed to delete user',
                                'error' => $e->getMessage()
                            ], 500);
                        }
                    }
                }else{
                    return response()->json([
                        'message' => 'User not found'
                    ], 404);
                }
            }
          
        }
        
       
    

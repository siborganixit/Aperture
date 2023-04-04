<?php

namespace App\Http\Controllers;
use App\Models\Code;
use App\Models\TestSubjects;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
class CodeController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code' => 'validation_error',
                'message' => $validator->errors(),
            ], 400);
        }

        $code = new Code;
        $code->code = $request->input('code');
        $code->expiry = date('Y-m-d H:i:s', strtotime($request->created_at.' +60 minutes'));

        $code->save();

        return response()->json([
            'message' => 'Code created successfully',
            'data' => $code,
        ], 200);
    }

    public function store_testsubjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
            'fullname' => 'required',
            'activation_code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code' => 'validation_error',
                'message' => $validator->errors(),
            ], 400);
        }

        $activation_code = $request->input('activation_code');
        $code = Code::where('code', $activation_code)->where('expiry', '>', Carbon::now())->first();
        if (!$code) {
            return response()->json([
                'error_code' => 'activation_error',
                'message' => 'Invalid or expired activation code',
            ], 400);
        }

        $user = new TestSubjects;
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->fullname = $request->input('fullname');
        $user->activation_code = $request->input('activation_code');

        $user->save();


        return response()->json([
            'message' => 'TestSubjects created successfully',
            'data' => $user,
        ], 200);
    }

    public function index() {
        $data=TestSubjects::all();

        return response()->json([
            'message' => 'TestSubjects Data',
            'data' => $data,
         ], 200);
    }
}

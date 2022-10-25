<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create()
    {
        //
    }
    */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!empty($request->all())) {
            
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            /**
             * codificacao em jwt
             */
            $timeNow = time();
            $expirationTime = $timeNow + 60*60;

            $jwtHeader = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];
            $jwtPayload = [
                'exp' => $expirationTime,
                'iss' => 'petcarebackend',
                'id' => Auth::user()->id
            ];

            $jwtHeader = json_encode($jwtHeader);
            $jwtHeader = base64_encode($jwtHeader);

            $jwtPayload = json_encode($jwtPayload);
            $jwtPayload = base64_encode($jwtPayload);

            $jwtSignature = hash_hmac('sha256',"$jwtHeader.$jwtPayload", getenv('JWT_KEY'),true);
            $jwtSignature = base64_encode($jwtSignature);

            return response()->json([
                "token" => "$jwtHeader.$jwtPayload.$jwtSignature",
                "message" => "user record created"
            ], 201);
        } else {
            return response()->json([
                "message" => "internal servidor error"
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request, $id)
    {
        $tokenParts = explode(".", $request->token);  
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        $tokenValid = $this->validacaoJwt($request);
            
        if($tokenValid == 1) {

            /**
            * verifica id do token
             */
            if($jwtPayload->id == $id) {
                if (User::where('id', $id)->exists()) {
                    $user = User::find($id);

                    return response()->json([
                        "user" => $user,
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "user not found"
                    ], 404);
                }
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 404);
            }
        } else if($tokenValid == 2) {
            return response()->json([
                "message" => "token has expired",
            ], 403);
        } else if($tokenValid == 3) {
            return response()->json([
                "message" => "invalid token",
            ], 403);
        } else if($tokenValid == 4){
            return response()->json([
                "message" => "invalid token structure"
            ], 403);
        } else if($tokenValid == 5){
            return response()->json([
                "message" => "token does not exist"
            ], 403);
        }
    }

    public function getAllUsers()
    {
        $users = User::get()->toJson(JSON_PRETTY_PRINT);
        return response($users, 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $tokenParts = explode(".", $request->token);  
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        $tokenValid = $this->validacaoJwt($request);
            
        if($tokenValid == 1) {
                                
            if($jwtPayload->id == $id) {

                $user = User::find($id);
                $user->name = is_null($request->name) ? $User->name : $request->name;
                $user->password = bcrypt(is_null($request->password) ? $User->password : $request->password);
                $user->update();
                        
                return response()->json([
                    "token" => "$request->token",
                    "message" => "records updated successfully"
                ], 200);
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 403);
            }
        } else if($tokenValid == 2) {
            return response()->json([
                "message" => "token has expired",
            ], 403);
        } else if($tokenValid == 3) {
            return response()->json([
                "message" => "invalid token",
            ], 403);
        } else if($tokenValid == 4){
            return response()->json([
                "message" => "invalid token structure"
            ], 403);
        } else if($tokenValid == 5){
            return response()->json([
                "message" => "token does not exist"
            ], 403);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        $tokenParts = explode(".", $request->token);  
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        $tokenValid = $this->validacaoJwt($request);
            
        if($tokenValid == 1) {
                        
            if($jwtPayload->id == $id) {
                $user = User::find($id);
                $user->delete();
                        
                return response()->json([
                    "token" => $request->token,
                    "message" => "records deleted"
                ], 202);
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 403);
            }
        } else if($tokenValid == 2) {
            return response()->json([
                "message" => "token has expired",
            ], 403);
        } else if($tokenValid == 3) {
            return response()->json([
                "message" => "invalid token",
            ], 403);
        } else if($tokenValid == 4){
            return response()->json([
                "message" => "invalid token structure"
            ], 403);
        } else if($tokenValid == 5){
            return response()->json([
                "message" => "token does not exist"
            ], 403);
        }
    }
    

    public function login(Request $request) {
        
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {

            $timeNow = time();
            $expirationTime = $timeNow + 60;

            $jwtHeader = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];
            $jwtPayload = [
                'exp' => $expirationTime,
                'iss' => 'petcarebackend',
                'id' => Auth::user()->id
            ];

            $jwtHeader = json_encode($jwtHeader);
            $jwtHeader = base64_encode($jwtHeader);

            $jwtPayload = json_encode($jwtPayload);
            $jwtPayload = base64_encode($jwtPayload);

            $jwtSignature = hash_hmac('sha256',"$jwtHeader.$jwtPayload", getenv('JWT_KEY'),true);
            $jwtSignature = base64_encode($jwtSignature);

            return response()->json([
                "token" => "$jwtHeader.$jwtPayload.$jwtSignature",
                "message" => "successfully logged in"
            ], 200);
        } else {
            return response()->json([
                "message" => "login attempt failed"
            ], 404);
        }
    }
    
    /**
     * funcao para validar o token JWT
     */
    public function validacaoJwt(Request $request)
    {
        if(isset($request->token)) {

            $tokenParts = explode(".", $request->token);
            
            $tokenHeader = base64_decode($tokenParts[0]);
            $jwtHeader = json_decode($tokenHeader);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);
            
            /**
             * verifica estrutura do token jwt
             */
            if (sizeof($tokenParts)==3) {
                
                /**
                 * verifica o tempo de expiracao do token
                 */
                if(!empty($jwtPayload->exp) && is_null($jwtPayload->id) == FALSE) {
                    
                    $expiration = Carbon::createFromTimestamp(json_decode($tokenPayload)->exp);
                    $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);

                    if($tokenExpired==false) {
                                
                        $jwtSignatureValid = hash_hmac('sha256',"$tokenParts[0].$tokenParts[1]", getenv('JWT_KEY'),true);
                        $jwtSignatureValid = base64_encode($jwtSignatureValid);
                        
                        $tokenSignature = $tokenParts[2];

                        /**
                         * verifica signature do token
                         */
                        if($tokenSignature == $jwtSignatureValid) {
                           return 1;
                        } else {
                            return 3;
                        }
                    } else {
                        return 2;
                    }
                } else {
                    return 3;
                }
            } else {
                return 4;
            }
        } else {
            return 5;
        }
    }

}

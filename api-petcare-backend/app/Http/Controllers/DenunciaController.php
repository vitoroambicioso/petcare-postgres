<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Denuncia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class DenunciaController extends Controller
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
    public function create(Request $request)
    {
        if(isset($request->token)) {
            if(!empty($request->all())) {

                $tokenParts = explode(".", $request->token);  
                $tokenHeader = base64_decode($tokenParts[0]);
                $tokenPayload = base64_decode($tokenParts[1]);
                $jwtHeader = json_decode($tokenHeader);
                $jwtPayload = json_decode($tokenPayload);

                $denuncia = new Denuncia;
                $denuncia->idUsuario = $jwtPayload->id;
                $denuncia->tipo = $request->tipo;
                $denuncia->cor = $request->cor;
                $denuncia->localizacao = $request->localizacao;
                $denuncia->rua = $request->rua;
                $denuncia->bairro = $request->bairro;
                $denuncia->pontoDeReferencia = $request->pontoDeReferencia;
                $denuncia->picture = $request->picture;
                $denuncia->save();

                return response()->json([
                    $denuncia,
                    "message" => "denuncia record created"
                ], 201);
            } else {
                return response()->json([
                    "message" => "internal servidor error"
                ], 500);
            }
        } else {
            return response()->json([
                "message" => "token doesn't exist"
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDenuncia(Request $request)
    {
            $tokenParts = explode(".", $request->token);
            
            $tokenHeader = base64_decode($tokenParts[0]);
            $jwtHeader = json_decode($tokenHeader);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);

            $tokenValid = $this->validacaoJwt($request);

            if($tokenValid == true) {
            
            if (Denuncia::where('idUsuario', $jwtPayload->id)->exists()) {
                    
                $denuncia = Denuncia::where('idUsuario', $jwtPayload->id)->get();
                return response()->json([
                    $denuncia,
                ], 200);
            } else {
                return response()->json([
                "message" => "denuncia not found"
                ], 404);
            }
        } else {
            return response()->json([
            "message" => "access denied"
            ], 403);
        }
    }

    public function getAllDenuncias()
    {
        $denuncias = Denuncia::get()->toJson(JSON_PRETTY_PRINT);
        return response($denuncias, 200);
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
            
        if($tokenValid == true) {

            if (Denuncia::where('id', $id)->exists()) {

                $denuncia = Denuncia::find($id);

                if($jwtPayload->id == $denuncia->idUsuario) {

                    $denuncia->tipo = is_null($request->tipo) ? $denuncia->tipo : $request->tipo;
                    $denuncia->cor = is_null($request->cor) ? $denuncia->cor : $request->cor;
                    $denuncia->localizacao = is_null($request->localizacao) ? $denuncia->localizacao : $request->localizacao;
                    $denuncia->rua = is_null($request->rua) ? $denuncia->rua : $request->rua;
                    $denuncia->bairro = is_null($request->bairro) ? $denuncia->bairro : $request->bairro;
                    $denuncia->pontoDeReferencia = is_null($request->pontoDeReferencia) ? $denuncia->pontoDeReferencia : $request->pontoDeReferencia;
                    $denuncia->picture = is_null($request->picture) ? $denuncia->picture : $request->picture;
                    $denuncia->save();
    
                    return response()->json([
                        $denuncia,
                        "message" => "records updated successfully"
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "user does not have permission to modify this denuncia"
                    ], 403);
                }
            }
            else {
                return response()->json([
                    "message" => "denuncia not found"
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "access denied"
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

        if($tokenValid == true) {

            if(Denuncia::where('id', $id)->exists()) {

                $denuncia = Denuncia::find($id);
                
                if($jwtPayload->id == $denuncia->idUsuario) {
                    $denuncia->delete();
        
                    return response()->json([
                    $denuncia,
                    "message" => "denuncia records deleted"
                    ], 202);
                } else {
                    return response()->json([
                        "message" => "user does not have permission to modify this denuncia"
                    ], 403);
                }
            } else {
                return response()->json([
                "message" => "denuncia not found"
                ], 404);
            }
        } else {
            return response()->json([
            "message" => "access denied"
            ], 403);
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
                        return true;
                    }
                    return false;
                }
                return false;
            }
            return false;
        }
        return false;
    }
}

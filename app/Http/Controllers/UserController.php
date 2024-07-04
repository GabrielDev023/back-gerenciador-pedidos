<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'cnpj_cpf' => 'required|string|unique:users',
        ]);

        //Faça um tratamento para deixar o CNPJ apenas com números
        $cnpj_cpf = preg_replace('/[^0-9]/', '', $request->cnpj_cpf);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if ($this->isCNPJ($cnpj_cpf)) {
            $cnpj = $cnpj_cpf;
            $response = Http::get("https://publica.cnpj.ws/cnpj/$cnpj");

            if ($response->successful()) {
                $data = $response->json();
                

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'cnpj_cpf' => $cnpj_cpf,
                    'razao_social' => $data['razao_social'],
                    'mei' => $data['simples']['mei'] === 'Sim',
                    'endereco' => $data['estabelecimento']['logradouro'],
                    'bairro' => $data['estabelecimento']['bairro'],
                    'complemento' => $data['estabelecimento']['complemento'],
                    'cidade' => $data['estabelecimento']['cidade']['nome'],
                    'uf' => $data['estabelecimento']['estado']['sigla'],
                    'cep' => $data['estabelecimento']['cep'],
                    'telefone' => $data['estabelecimento']['ddd1'] . $data['estabelecimento']['telefone1'],
                ]);

                return new UserResource($user);
            } elseif ($response->status() === 404) {
                return response()->json(['error' => 'CNPJ não encontrado'], 404);
            } else {
                return response()->json(['error' => 'Erro ao consultar CNPJ'], 500);
            }
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'cnpj_cpf' => $cnpj_cpf,
                'razao_social' => $request->razao_social,
                'mei' => $request->mei,
                'endereco' => $request->endereco,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
                'cep' => $request->cep,
                'telefone' => $request->telefone,
            ]);

            return new UserResource($user);
        }
    }

    private function isCNPJ($value)
    {
        return strlen($value) == 14 && ctype_digit($value);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'password' => 'string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
         $data = $request->except('cnpj_cpf');
    
        $user->update($data);
    
        return new UserResource($user);
    }
    

    public function show($id)
    {
        return new UserResource(User::findOrFail($id));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Usuario excluído com sucesso.',
        ], 200);    }
}

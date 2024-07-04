<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'cnpj_cpf' => $this->cnpj_cpf,
            'razao_social' => $this->razao_social,
            'mei' => $this->mei,
            'endereco' => $this->endereco,
            'bairro' => $this->bairro,
            'complemento' => $this->complemento,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'cep' => $this->cep,
            'telefone' => $this->telefone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

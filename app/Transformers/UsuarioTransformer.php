<?php

namespace App\Transformers;

use App\Entities\User;
use App\Repositories\UsuarioRepository;
use League\Fractal\TransformerAbstract;

class UsuarioTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Usuario $user)
    {
	    $data_criacao = new \DateTime($user->data_cadastro);

        return [
            'id'   => $user->id,
            'login'=> $user->login,
            'nome'=> $user->nome,
            'dt_cadastro' => $data_criacao->format('d/m/Y'),
            'perfil_id' => $user->perfil_id,
            'perfil' => $user->perfil,
            'status' => $user->status
        ];
    }
}

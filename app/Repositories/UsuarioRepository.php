<?php
namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface UsuarioRepository extends RepositoryInterface
{
    public function getPermissoesById($id);
    public function getPermissionsGroupByApplications($id);
}
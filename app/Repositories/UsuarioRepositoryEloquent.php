<?php
namespace App\Repositories;

use App\Entities\Aplicacao;
use App\Entities\User;
use App\Presenters\UsuarioPresenter;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class UsuarioRepositoryEloquent extends BaseRepository implements UsuarioRepository
{
    use TraitLog;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Usuario::class;
    }


    /**
     * @return string
     */
    public function presenter()
    {
        return UsuarioPresenter::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $id
     * @return array
     */
    public function getPermissoesById($id)
    {
        $this->skipPresenter(true);
        $user = $this->find($id);
        $this->skipPresenter(false);

        $permissoesFormatadas =  [];
        $aplicacaoRepository = app(AplicacaoRepository::class);
        $permissoesUser = $this->formatPermissoes($user->permissoes);

        foreach ($permissoesUser as $permissao) {
            $aplicacao = $aplicacaoRepository->find($permissao['aplicacao_id']);
            $permissoesFormatadas[] = $aplicacao->nome . "_" . $permissao['acao'];
        }

        return array_unique($permissoesFormatadas);
    }

    /**
     * @param $permissoes
     * @return array
     */
    private function formatPermissoes($permissoes)
    {
        $permissoesFormatadas = [];

        foreach ($permissoes as $permissao) {
            $permissoesFormatadas[] = [
                'aplicacao_id' => $permissao->aplicacao_id,
                'acao' => $permissao->acao
            ];
        }

        return $permissoesFormatadas;
    }

    /**
     * @param $id
     * @return array
     */
    public function getPermissionsGroupByApplications($id)
    {
        $this->skipPresenter(true);
        $usuario = $this->with(['nivel'])->find($id);
        $nivel = $usuario->nivel;
        $permissoes = $nivel->permissoes;
        $this->skipPresenter(false);

        return $this->formatGroupByApplication($permissoes);
    }

    /**
     * @param $permissoes
     * @return array
     */
    public function formatGroupByApplication($permissoes)
    {
        $permissoesFormatadas = [];

        $permissoes = $permissoes->map(function ($permissao) {
            return $permissao->toArray();
        });

        $permissoes = $permissoes->groupBy('aplicacao_id');

        foreach ($permissoes as $chave => $permissao) {
            $aplicacao = Aplicacao::find($chave);
            $permissoesFormatadas[] = [
                'id' => $aplicacao->id,
                'nome' => $aplicacao->nome,
                'model' => $aplicacao->model,
                'permissoes' => $permissoes[$chave]
            ];
        }

        return $permissoesFormatadas;
    }
}
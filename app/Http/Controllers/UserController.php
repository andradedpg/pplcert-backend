<?php
namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Requests\UsuarioCreateRequest;
use App\Http\Requests\UsuarioUpdateRequest;
use App\Repositories\UsuarioRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * UsuarioController constructor.
     * @param UsuarioRepository $repository
     */
    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retornando o usuário autenticado
     *
     * @return mixed
     */
    public function getUser()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'user' => $this->repository->find($user->id)
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->repository->all();
        return response()->json($users);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  UsuarioCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UsuarioCreateRequest $request)
    {
        try {
            $attributes = $request->all();
            $attributes['remember_token'] = str_random(10);

            if (isset($attributes['senha']) && $attributes['senha']) {
                $attributes['senha'] = bcrypt($attributes['senha']);
            }

            $user   = Usuario::create($attributes);
            $perfil = $user->perfil;

            foreach ($perfil->menus as $menu) {
                $user->menu()->attach($menu->id);
            }

            return response()->json($this->repository->parserResult($user));
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = $this->repository->find($id);
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Usuário não encontrado'], 404);
        } catch (\Throwable $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UsuarioUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(UsuarioUpdateRequest $request, $id)
    {
        try {
            $password = $request->get('senha');
            $this->repository->skipPresenter(true);
            $userOld = $this->repository->find($id);
            $user = $this->repository->update(array_except($request->all(), 'password'), $id);
            $this->repository->skipPresenter(false);

            if ($userOld->nivel->id !== $user->nivel->id) {
                $user->permissoes()->detach();

                foreach ($user->nivel->permissoes as $permissao) {
                    if (!in_array($user->nivel->nome, ['Administrador', 'Secretário', 'Tesoureiro'])) {
                        if ($permissao->acao == "dashboard") {
                            $user->permissoes()->attach($permissao->id);
                        }
                    } else {
                        $user->permissoes()->attach($permissao->id);
                    }
                }
            }

            if ($password) {
                $user->password = bcrypt($password);
                $user->save();
            }

            return response()->json($this->repository->parserResult($user));
        } catch (\Throwable $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->repository->skipPresenter(true);
            $user = $this->repository->find($id);
            $this->repository->skipPresenter(false);
            $user->menu()->detach();
            $user->tokens()->delete();
            $user->permissoes()->detach();

            $deleted = $this->repository->delete($id);

            if (!$deleted) {
                throw new \Exception("Usuário não pode ser removido");
            }

            return response()->json([], 204);
        } catch (\Throwable $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissoes($id)
    {
        try {
            return response()->json($this->repository->getPermissoesById($id));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissionsGroupByApplications($id)
    {
        try {
            $aplicacoes = $this->repository->getPermissionsGroupByApplications($id);
            $permissoes = $this->repository->getPermissoesById($id);

            return response()->json([
                'aplicacoes' => $aplicacoes,
                'permissoes' => array_values($permissoes)
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getLine()], 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachPermissions(Request $request, $id)
    {
        try {
            $this->repository->skipPresenter(true);
            $usuario = $this->repository->find($id);
            $this->repository->skipPresenter(false);

            $idPermissao = $request->get('idPermissao');
            $acao = $request->get('acao');
            $objPermissao = $this->permissaoRepository->find($idPermissao);

            if ($acao == 'add') {
                if (!$usuario->menu()->find($objPermissao->menu_id)) {
                    $usuario->menu()->attach($objPermissao->menu_id);
                }
                $usuario->permissoes()->attach($objPermissao->id);
            }

            if ($acao == 'delete') {
                if ($objPermissao->menu_id) {
                    $usuario->menu()->detach($objPermissao->menu_id);
                }

                $usuario->permissoes()->detach($objPermissao->id);
            }

            $permissoes = $this->repository->getPermissoesById($usuario->id);

            return response()->json($permissoes);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function check(Request $request)
    {
        try {
            $resultList = [];
            $checked = false;

            $token = $request->get('token');

            $resultQuery = \DB::table('usuarios')
                ->where('token', $token)
                ->select('users.id', 'users.status')
                ->first();

            if ($resultQuery && $resultQuery->status != 'A') {
                $checked = true;
                $user = Usuario::find($resultQuery->id);
                $user->status = 'A';
                $pessoa = $user->pessoa;
                $user->save();

                \Mail::send('emails.dados-acesso', ['user' => $user], function ($message) use ($pessoa) {
                    $message->from(env('MAIL_FROM_ADDRESS'))
                        ->to($pessoa->email)
                        ->subject('Sisatos - Dados de acesso');
                });
            }

            $resultList = [
                'checked' => $checked
            ];

            return view('auth.ativacao-cadastro', compact('resultList'));
        } catch (\Throwable $e) { dd($e);
            return view('auth.ativacao-cadastro', ['checked' => false]);
        }
    }

    public function logout()
    { 
        if (Auth::check()) {
            Auth::user()->OauthAcessToken()->delete();
            return response()->json(['logout' => 'success'], 200);
        }
    }
}

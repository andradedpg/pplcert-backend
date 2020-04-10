<?php
namespace App\Repositories;

use Carbon\Carbon;
use App\Entities\Log;

trait TraitLog
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        $skipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::create($attributes);

        if ($model) {
            $this->logSave($model, $attributes);
        }

        $this->skipPresenter($skipPresenter);

        return $this->parserResult($model);
    }


    public function update(array $attributes, $id)
    {
        $skipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $modelOld = $this->find($id);
        $model = parent::update($attributes, $id);

        if ($model) {
            $this->logUpdate($model, $modelOld);
        }

        $this->skipPresenter($skipPresenter);

        return $this->parserResult($model);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $skipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = $this->find($id);
        $this->skipPresenter($skipPresenter);

        $deleted =  parent::delete($id);

        if ($deleted) {
            $this->logDelete($model);
        }

        return $deleted;
    }

    /**
     * @param $model
     * @param $data
     */
    private function logSave($model, $data)
    {
        $dateNow = new Carbon();
        $dateStr = $dateNow->format('Y-m-d H:i:s');

        $log = [
            'nome_log' => $model->getTable(),
            'model' => $model->logname,
            'usuario_id' => \Auth::user()->id,
            'registro_id' => $model->id,
            'tipo' => 'I',
            'descricao' => json_encode($data),
            'data_cadastro' => $dateStr
        ];

        $this->storeLog($log);
    }

    /**
     * @param $model
     * @param $modelOld
     */
    private function logUpdate($model, $modelOld)
    {
        $to = $from = $r = null;
        $data = $model->toArray();
        $dataOld = $modelOld->toArray();

        foreach ($data as $field => $value) {
            $valueFormatted = @(string) $this->formatDescription($value, $field);
            $valueOldFormatted = @(string) $this->formatDescription($dataOld[$field], $field);

            if ($valueFormatted != $valueOldFormatted && isset($data[$field])) {
                $to[$field]   = $valueOldFormatted;
                $from[$field] = $valueFormatted;
            }
        }

        if (!is_null($to) && !is_null($from)) {
            $dateNow = new Carbon();
            $dateStr = $dateNow->format('Y-m-d H:i:s');

            $log = [
                'nome_log' => $model->getTable(),
                'model' => $model->logname,
                'usuario_id' => \Auth::user()->id,
                'registro_id' => $model->id,
                'tipo' => 'U',
                'descricao' => '{"de":'.json_encode($to).', "para":'.json_encode($from).'}',
                'data_cadastro' => $dateStr,
                //'created_at' => $dateStr,
                //'updated_at' => $dateStr
            ];

            $this->storeLog($log);
        }
    }

    /**
     * @param $model
     */
    public function logDelete($model)
    {
        $dateNow = new Carbon();
        $dateStr = $dateNow->format('Y-m-d H:i:s');

        $log = [
            'nome_log' => $model->getTable(),
            'model' => $model->logname,
            'usuario_id' => \Auth::user()->id,
            'registro_id' => $model->id,
            'tipo' => 'D',
            'descricao' => '{"registro excluido"}',
            'data_cadastro' => $dateStr
        ];

        $this->storeLog($log);
    }

    /**
     * @param array $log
     */
    private function storeLog(array $log)
    {
        $log['ip'] = $_SERVER['REMOTE_ADDR'];
        Log::create($log);
    }

    /**
     * @param $valor
     * @param $tipo
     * @return mixed
     */
    private function formatDescription($valor, $tipo)
    {
        $camposEspecificos = [
            'cpf',
            'cpfTitular',
            'cnpj',
            'telefone',
            'celular',
            'documento',
            'cep'
        ];

        /* Se o tipo do valor não for especificado, ele cai em expressões que tenta formata-lo*/
        if (!in_array($tipo, $camposEspecificos)) {
            /* Se o valor for uma data*/
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$valor)) {
                $data_formatada = new \DateTime($valor);
                $valor = $data_formatada->format('d/m/Y');
            }
            /* Se o valor for data com hora */
            if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $valor)) {
                $data_formatada = new \DateTime($valor);

                # Ele trasnforma o campo para data tbm. Pois esse campos no campos,
                # apesar de serem date_time, só consideram a data mesmo
                $valor = $data_formatada->format('d/m/Y');
            }
        } else {
            if ($tipo == 'cpf' || $tipo == 'cpfTitular' || $tipo == 'cnpj') {
                $valor = str_replace(array('.','-','/'), '', $valor);
            } elseif ($tipo == 'documento') {
                $valor = str_replace(array('_'), '', $valor);
            } elseif ($tipo == 'telefone' || $tipo == 'celular') {
                $valor = str_replace(array('(',')','_'), '', $valor);
            } if ($tipo == 'cep' ) {
                $valor = str_replace(array('.','-'), '', $valor);
            }
        }

        return $valor;
    }
}
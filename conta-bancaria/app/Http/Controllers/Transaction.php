<?php

namespace App\Http\Controllers;

use App\Models\ApiTransaction;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Models\User;

class Transaction extends Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set_data_json()
    {
        $arr = [
            'conta' => [
                'codigoCliente' => 1, 'Ativa' => true, 'LimiteDisponivel' => 100
            ],
            'transacao' => [
                ['tipo' => 'saque', 'motivo' => 'teste', 'valor' => 30, 'data' => "2021-07-17T17: 00: 00.000Z"],
                ['tipo' => 'deposito', 'motivo' => 'teste', 'valor' => 10, 'data' => "2021-07-17T18: 00:00.000Z"],
                ['tipo' => 'saque', 'motivo' => 'teste', 'valor' => 90, 'data' => "2021-07-17T19: 00:00.000Z"],
            ]
        ];

        return json_encode($arr);
    }

    public function get_data_json()
    {
        $arr = [
            ['conta' => ['codigoCliente' => 1, 'Ativa' => true, 'LimiteDisponivel' => 100], 'violacao' => []],
            ['conta' => ['codigoCliente' => 1, 'Ativa' => true, 'LimiteDisponivel' => 70], 'violacao' => []],
            ['conta' => ['codigoCliente' => 1, 'Ativa' => true, 'LimiteDisponivel' => 80], 'violacao' => []],
            ['conta' => ['codigoCliente' => 1, 'Ativa' => true, 'LimiteDisponivel' => 80], 'violacao' => ['limite-insuficiente']],
        ];

        return json_encode($arr);
    }

    public function validateTransition(Array $arra = array())
    {
        $status = 'fail';
        $codigoCliente = $arra['conta']['codigoCliente'];

        if(!isset($_SESSION['Cliente']) && !isset($_SESSION['Cliente'][$codigoCliente])) {
            return array('success' => false, 'violacao' => ['cliente-nao-encontrado']);
        }


        $limite = $_SESSION['Cliente'][$codigoCliente]['LimiteDisponivel'] ?: 0;

        $transacao = array();

        $saldo = false;
        foreach ($arra['transacao'] as $key => $val) {
            $saldo = $this->setSaldo($val, $limite);

            $newTransaction = [
                'CodigoCliente' => $codigoCliente, 'LimiteDisponivel' => $saldo,
                'tipo' => $val['tipo'], 'motivo' => $val['motivo'], 'valor' => $val['valor'], 'data' => $val['data']
            ];

            if($saldo === false) {
                $this->setTransition($newTransaction, $status);
                $response = ['conta' => [$_SESSION['Cliente'][$codigoCliente]], 'violacao' => ['limite-insuficiente']];
                return $response;
            }

            $limite = $saldo;

            $status = 'success';
            $this->setTransition($newTransaction, $status);
        }

        $_SESSION['Cliente'][$codigoCliente]['LimiteDisponivel'] = $limite;

        return array('success' => true, $arra);
    }

    public function setSaldo($transacao, $limite)
    {
        $saldo = false;
        if($transacao['tipo'] == 'saque') {
            $saldo = $limite - $transacao['valor'];
            return ($saldo <= -1) ? false : $saldo;
        }
        $saldo = $limite + $transacao['valor'];
        return $saldo;
    }

    /**
     * @param $dataTransaction => ['CodigoCliente', 'LimiteDisponivel', 'tipo', 'motivo', 'valor', 'data']
     * @param string $status
     * @return array
     */
    public function setTransition($dataTransaction, $status = 'fail')
    {
        $ok = false;
        if($status != 'fail') {
            $user = User::find($dataTransaction['CodigoCliente']);
            $user->LimiteDisponivel = $dataTransaction['LimiteDisponivel'];
            $ok = $user->save();
        }

        if(!$ok) {
            $status = 'fail';
        }

        $newDate = $dataTransaction['data'];
        $newDate = str_replace(' ', '', $newDate);
        $newDate = date("Y-m-d H:i:s", strtotime($newDate));

        $apiTransaction = new ApiTransaction();
        $apiTransaction->user_id = $dataTransaction['CodigoCliente'];
        $apiTransaction->tipo = $dataTransaction['tipo'];
        $apiTransaction->motivo = $dataTransaction['motivo'];
        $apiTransaction->valor = $dataTransaction['valor'];
        $apiTransaction->data = $newDate;
        $apiTransaction->status = $status;
        $apiTransaction->save();

        $success = false;
        return ['success' => $success];
    }

    /**
     * - Saída:
     * O estado atual da conta criada mais qualquer violação da lógica de negócio.
     */
    public function getTransition()
    {
        $success = false;
        return ['success' => $success];
    }
}

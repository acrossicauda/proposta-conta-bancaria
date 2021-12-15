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
            $newDate = date("Y-m-d H:i:\%");

            $transaction = ApiTransaction::where('user_id', $codigoCliente)
                ->where('valor', $val['valor'])
                ->where('tipo', $val['tipo'])
                ->where('created_at', 'like', $newDate)->get();
            if($transaction->count() > 0) {
                $response = array('conta' => array($_SESSION['Cliente'][$codigoCliente]),
                    'violacao' => array('transação-duplicada'));
                return $response;
            }
            $saldo = $this->setSaldo($val, $limite);

            $newTransaction = array(
                'CodigoCliente' => $codigoCliente, 'LimiteDisponivel' => $saldo,
                'tipo' => $val['tipo'], 'motivo' => $val['motivo'], 'valor' => $val['valor'], 'data' => $val['data']
            );

            if($saldo === false) {
                $this->setTransition($newTransaction, $status);
                $response = array('conta' => array($_SESSION['Cliente'][$codigoCliente]), 'violacao' => array('limite-insuficiente'));
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
        return array('success' => $success);
    }

    /**
     * - Saída:
     * O estado atual da conta criada mais qualquer violação da lógica de negócio.
     */
    public function getTransition()
    {
        $success = false;
        return array('success' => $success);
    }
}

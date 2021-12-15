<?php

namespace App\Http\Controllers;

use App\Models\ApiTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class ApiTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $post = $request->all();

        $transaction = new Transaction();

        if(isset($_GET['teste'])) {
            $post = $transaction->set_data_json();
        }

        $codigoCliente = $post['conta']['codigoCliente'];

        $user = User::find($codigoCliente);

        if(!$user->Ativa) {
            return response()->json([
                "conta" => [
                    'Cliente' => ['codigoCliente' => $codigoCliente, 'Ativa' => $user->Ativa, 'LimiteDisponivel' => $user->LimiteDisponivel],
                    'violacao' => ['conta-nao-ativa']
                ]
            ], 200);
        }

        $_SESSION['Cliente'][$codigoCliente]['codigoCliente'] = $codigoCliente;
        $_SESSION['Cliente'][$codigoCliente]['Ativa'] = $user->Ativa;
        $_SESSION['Cliente'][$codigoCliente]['LimiteDisponivel'] = $user->LimiteDisponivel;


        $post = $transaction->validateTransition($post);
        if(!isset($post['success']) || !$post['success']) {
            return response()->json([
                "conta" => [
                    'Cliente' => ['codigoCliente' => $codigoCliente, 'Ativa' => $user->Ativa, 'LimiteDisponivel' => $user->LimiteDisponivel],
                    'violacao' => [$post['violacao']]
                ]
            ], 200);
        }

        // pegando valor atualizado diretamente do banco
        $user = User::find($codigoCliente);

        return response()->json([
            "conta" => [
                'Cliente' => ['codigoCliente' => $codigoCliente, 'Ativa' => $user->Ativa, 'LimiteDisponivel' => $user->LimiteDisponivel],
                'violacao' => []
            ]
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ApiTransaction  $apiTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(ApiTransaction $apiTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ApiTransaction  $apiTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(ApiTransaction $apiTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ApiTransaction  $apiTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ApiTransaction $apiTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ApiTransaction  $apiTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApiTransaction $apiTransaction)
    {
        //
    }
}

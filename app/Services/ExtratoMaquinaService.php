<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class ExtratoMaquinaService
{

    public static function criar($dados){
        $url = env('APP_URL_API') . "/extratoMaquina";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_maquinas = $response->json();

        return $extrato_maquinas;
    }

    public static function coletar(string $id = Null)
    {
        if(is_null($id)){
            $url = env('APP_URL_API') . "/extratoMaquina";
        }else{
            $url = env('APP_URL_API') . "/extratoMaquina/$id";
        }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        $extrato_maquinas = $response->json();

        return $extrato_maquinas;
    }

    public static function coletarComPaginacao(array $filtros = [], int $page = 1)
{
    // Define a URL base da API de extrato de máquinas
    $url = env('APP_URL_API') . "/extratoMaquina";

    // Adiciona os parâmetros de paginação e filtros na requisição
    $params = array_merge($filtros, ['page' => $page]);

    // Coleta o token para autenticação
    $token = AuthService::getToken();

    // Faz a requisição HTTP com o token e parâmetros
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->get($url, $params);

    // Verifica se a resposta foi bem-sucedida
    if ($response->successful()) {
        // Retorna os dados da resposta da API
        return $response->json();
    } else {
        // Tratamento de erro caso a requisição falhe
        return [
            'data' => [],
            'current_page' => 1,
            'last_page' => 1,
        ];
    }
}

public static function coletarRelatorioTotalTransacoes($dados, $id_cliente = null){
    $url = env('APP_URL_API') . "/relatorioTotalTransacoes";

    if(isset($id_cliente)){
        $dados['id_cliente'] = [$id_cliente];
    }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_maquinas = $response->json();

        return $extrato_maquinas;
}

public static function coletarRelatorioTotalTransacoesTotal($dados, $id_cliente = null){
    $url = env('APP_URL_API') . "/relatorioTotalTransacoesTotal";

    if(isset($id_cliente)){
        $dados['id_cliente'] = [$id_cliente];
    }

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_maquinas = $response->json();

        return $extrato_maquinas;
}

public static function coletarRelatorioTotalTransacoesTaxa($dados, $id_cliente = null){
    $url = env('APP_URL_API') . "/relatorioTotalTransacoesTaxa";


    if(isset($id_cliente)){
        $dados['id_cliente'] = [$id_cliente];
    }
        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_maquinas = $response->json();

        return $extrato_maquinas;
}

public static function coletarExtratoDasMaquinasDeUmCliente($dados){
    $url = env('APP_URL_API') . "/transacaoMaquinaCliente";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_maquinas = $response->json();

        return $extrato_maquinas;
}

public static function coletarTotalTransacaoDasMaquinasDeUmCliente($dados){
    $url = env('APP_URL_API') . "/totalTransacaoMaquinaCliente";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_maquinas = $response->json();

        return $extrato_maquinas;
}


    public static function coletarComFiltro($filtros, $tipo)
    {
        $url = env('APP_URL_API') . "/extratoMaquina";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($url);

        if ($response->successful()) {
            // Obtenha os clientes da resposta JSON
            $extrato_maquinas = $response->json();
            
            // Filtrar os clientes com base nos filtros fornecidos
            foreach ($filtros as $chave => $valor) {
                // Verifique se a chave existe e se o valor não está vazio
                if (isset($chave) && $valor !== null) {
                    // Filtrar os clientes com base no valor do filtro
                    $extrato_maquinas = array_filter($extrato_maquinas, function ($extrato_maquina) use ($chave, $valor) {
                        return $extrato_maquina[$chave] === $valor;
                    });
                }
            }

            // Retorna os clientes filtrados
            return $extrato_maquinas;
        } else {
            // Em caso de falha na chamada à API, retorne um array vazio ou uma mensagem de erro
            return [];
        }
    }

    public function atualizar($dados, $id){
        $url = env('APP_URL_API') . "/extratoMaquina/$id";

        $token = AuthService::getToken();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($url, $dados);

        $extrato_maquina = $response->json();

        return $extrato_maquina;
    }

public static function coletarSaldoTotal($id = null) {
    if(!is_null($id)){
        $url = env('APP_URL_API') . "/extrato/saldo/$id";
    }else{
        $url = env('APP_URL_API') . "/extrato/saldo/";
    }
    $token = AuthService::getToken();
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->get($url);

    $extrato_maquinas = $response->json();

    return $extrato_maquinas; 
}
public static function coletarDevolucoes($id = null) {
    if(!is_null($id)){
        $url = env('APP_URL_API') . "/extrato/devolucao/$id";
    }else{
        $url = env('APP_URL_API') . "/extrato/devolucao/";
    }
    $token = AuthService::getToken();
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->get($url);


    $extrato_maquinas = $response->json();

    return $extrato_maquinas; 
}

}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Mock (desenvolvimento)
    |--------------------------------------------------------------------------
    |
    | Quando habilitado, todas as chamadas à API externa são interceptadas
    | e respondidas com dados fictícios locais (MockStore).
    |
    | Uso: defina API_MOCK_ENABLED=true no .env
    |
    */
    'enabled' => env('API_MOCK_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Persistência do estado dos mocks
    |--------------------------------------------------------------------------
    |
    | Quando true, operações de create/update/delete são salvas em
    | storage/app/mocks/state.json e sobrevivem entre requisições.
    | Use `php artisan mock:reset` para restaurar os dados iniciais.
    |
    */
    'persist' => env('API_MOCK_PERSIST', true),

    /*
    | Token fictício retornado pelo mock de autenticação.
    */
    'token' => env('API_MOCK_TOKEN', 'mock-api-token-dev'),

];

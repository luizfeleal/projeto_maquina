<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagbankWebhookController extends Controller
{
    public function processamentoWebhook(Request $request)
    {
        Log::info('req webhook pagbank ------------------');
        Log::info('Method: ' . $request->method());
        Log::info('Headers: ', $request->headers->all());
        Log::info('Query: ', $request->query());
        Log::info('Body: ', $request->all());
        Log::info('Raw content: ' . $request->getContent());

        return response()->json(['status' => 'ok']);
    }
}

<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Services\LogsService;


class EmailService
{


    public static function enviarEmailGenerico($destino, $assunto, $template, $dados)
    {

        return Mail::send($template, ['dados' => $dados], function ($message) use ($destino, $assunto, $template, $dados){
            $message->to($destino);
            $message->subject($assunto);
        });
    }

}

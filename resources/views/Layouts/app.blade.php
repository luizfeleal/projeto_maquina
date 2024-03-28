<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

   <!--<link rel="icon" href="{{ asset('site/img/favicon_hc.png') }}" sizes="32x32">-->

    <link rel="stylesheet" href="{{ asset('site/style.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">


</head>
<body>

@php
$currentRoute = Request::route()->getName();
@endphp


    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
      <div class="container-fluid">
        <!--<a class="navbar-brand" href="#" style="cursor: default;"><img src="{{ asset('site/img/logo_hc.png') }}" width="250" height="63" alt="Hospital Angelina Caron"></a>-->
        <h1>LOGO</h1>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
          <span class="navbar-toggler-icon"></span>
        </button>

      </div>
    </nav>
    <div  class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
            <div class="flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px; height: 100vh;display: flex;flex-direction: column;justify-content: space-around; position:fixed;min-height: 800px;">
                <div>

                        <a href="/" class="d-flex align-items-center pb-3 mb-3 link-body-emphasis text-decoration-none" style=" padding-left: 12px;">
                        <div class="d-flex" style="flex-direction: column;">
                            <span class="fs-5 fw-semibold" id="username">{{session()->get('usuario_nome')}}</span>
                            <span class="fs-10" id="user_function">{{session()->get('grupo_nome')}}</span>
                        </div>
                        </a>


                    <ul class="list-unstyled ps-0">
                        <li class="mb-1">
                            <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#procedimentos-collapse" aria-expanded="false">
                                <a href="/" class="text-decoration-none">
                                    <i class="fa-solid fa-table icon-sidebar" style=" font-size: 25px;padding-right:5px;"></i>Local e Máquinas
                                </a>
                            </button>
                            <div class="collapse {{ $currentRoute === 'procedimentos-criar' || $currentRoute === 'procedimentos-incluir' || $currentRoute === 'procedimentos-detalhar' || $currentRoute === 'procedimentos-editar'  ? 'show' : '' }}" id="procedimentos-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="/" class="d-inline-flex text-decoration-none rounded" style="{{ $currentRoute === 'procedimentos-criar' ? 'color:grey' : ' ' }}">Criar nova máquina</a></li>
                                <li><a href="/" class=" d-inline-flex text-decoration-none rounded" style="{{  $currentRoute === 'procedimentos-incluir' || $currentRoute === 'procedimentos-detalhar' || $currentRoute === 'procedimentos-editar' ? 'color:grey': ' ' }}">Incluir usuários</a></li>
                                <li><a href="/" class=" d-inline-flex text-decoration-none rounded" style="{{  $currentRoute === 'procedimentos-incluir' || $currentRoute === 'procedimentos-detalhar' || $currentRoute === 'procedimentos-editar' ? 'color:grey': ' ' }}">Criar local</a></li>
                            </ul>
                            </div>
                        </li>
                        <li class="mb-1">
                            <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0">
                                <a href="/" class="text-decoration-none" style="{{ $currentRoute === 'planos-exibir' || $currentRoute === 'planos-detalhar' || $currentRoute === 'planos-criar' || $currentRoute === 'planos-editar' ? 'color:grey' : ' ' }}">
                                    <i class="fa-solid fa-square-poll-vertical icon-sidebar" style=" font-size: 25px;padding-right:5px;"></i>Clientes
                                </a>
                            </button>

                        </li>
                        <li class="mb-1">
                            <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0">
                                <a href="/" class="text-decoration-none" style="{{ $currentRoute === 'relatorio-criar' || $currentRoute ===  'relatorio-gerar' || $currentRoute === 'relatorio-exibir'  ?  'color:grey' : ' ' }}">
                                    <i class="fa-solid fa-chart-pie icon-sidebar" style=" font-size: 22px;padding-right:5px;"></i>Relatórios
                                </a>
                            </button>
                        </li>

                        <li class="mb-1">
                            <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#termo-collapse" aria-expanded="false">
                                <a href="/" class="text-decoration-none">
                                    <i class="fa-solid fa-gear icon-sidebar" style=" font-size: 25px;padding-right:5px;"></i>Minhas máquinas
                                </a>
                            </button>
                            <div class="collapse {{ $currentRoute === 'termo-adesao-detalhar' || $currentRoute === 'termo-adesao-criar' || $currentRoute === 'termo-adesao-editar' || $currentRoute === 'extrato-termo-editar' || $currentRoute === 'termo-adesao-exibir' || $currentRoute === 'extrato-termo-criar' || $currentRoute === 'extrato-termo-exibir' || $currentRoute === 'extrato-termo-gerar' ? 'show' : '' }}" id="termo-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="/" class=" d-inline-flex text-decoration-none rounded" style="{{ $currentRoute === 'termo-adesao-exibir' || $currentRoute === 'termo-adesao-criar' || $currentRoute === 'termo-adesao-detalhar' || $currentRoute === 'termo-adesao-editar' ? 'color:grey' : ' ' }}">Transações</a></li>
                                <li><a href="/" class=" d-inline-flex text-decoration-none rounded">Acumulado</a></li>
                            </ul>
                            </div>
                        </li>
                        <li class="mb-1">
                            <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#prestadores-collapse" aria-expanded="false">
                                <a href="/" class="text-decoration-none">
                                    <i class="fa-solid fa-people-group icon-sidebar" style=" font-size: 25px;padding-right:5px;"></i>Gerar QR
                                </a>
                            </button>
                        </li>

                        <li class="mb-1">
                            <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#guias-collapse" aria-expanded="false">
                                <a href="/"  class="text-decoration-none">
                                    <i class="fa-solid fa-bell icon-sidebar" style=" font-size: 25px;padding-right:5px;"></i>Novo usuário
                                </a>
                            </button>


                        </li>

                        <li class=" my-5"></li>

                    </ul>

                        <ul class="list-unstyled ps-0">
                            <li class="mb-1">
                                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" >
                                        <a href="/" class="text-decoration-none">
                                            <i class="fa-solid fa-right-from-bracket icon-sidebar" style=" font-size: 25px;padding-right:5px;"></i>Logout
                                        </a>
                                    </button>
                                </li>
                        </ul>
                </div>
            </div>
    </div>
    <div class="d-flex d-flex-container" style="min-height: 100vh;">

        @yield('content')


    </div>



<script src="{{ asset('site/jquery.js') }}"></script>
<script src="{{ asset('site/functions.js') }}"></script>
<script src="{{ asset('site/bootstrap.js') }}"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
    integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>




@yield('scriptTable')



<script>

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))



    function toggleOffcanvas() {
        const offcanvasElement = document.getElementById("offcanvasScrolling");
        if (window.innerWidth < 768) {
            // Fecha o offcanvas em telas menores (modo mobile)
            offcanvasElement.classList.remove("show");
        } else {
            // Mantém o offcanvas aberto em telas maiores
            offcanvasElement.classList.add("show");
        }
    }

    // Adiciona um ouvinte de evento para redimensionamento da janela
    window.addEventListener("resize", toggleOffcanvas);

    // Chama a função inicialmente para configurar o estado do offcanvas
    toggleOffcanvas();

    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
            }, false)
        })
    })()

    $(document).ready(function () {
        // Obtenha a URL atual
        var currentURL = window.location.pathname;

        // Encontre os itens de menu que correspondem à URL atual
        $('.btn-toggle').each(function () {
            var menuURL = $(this).find('a').attr('href');

            if (currentURL === menuURL) {
                // Adicione a classe "active" ao botão de menu
                $(this).addClass('active');

                // Se o botão de menu estiver em um submenu, também abra o submenu
                var submenu = $(this).next('.collapse');
                if (submenu.length > 0) {
                    submenu.addClass('show');
                }
            }
        });
    });

    function prestadoresShow(){
        setTimeout(() => {
            document.getElementById('prestadores-collapse').classList.add('show')
        }, 10);
    }

    /*$("#plano_margem").mask('##0,00%', {reverse: true});
    $("#plano_taxa_adm").mask('##0,00%', {reverse: true});
    $("#plano_taxa_recarga").mask('##0,00%', {reverse: true});*/

    $('#procedimentosLista').on("change", () => {
        if($('#procedimentosLista').val() != " "){
            var partes = $('#procedimentosLista').val().split('.');
            var extensao = partes.pop();
            if(extensao == "xlsx"){
                //$("#botao-enviar-lista").attr("disabled", false);
                $("#procedimentosLista").removeClass("is-invalid");
            }else{
                //$("#botao-enviar-lista").attr("disabled", true);
                $("#procedimentosLista").addClass("is-invalid");
                $(".invalid-p-arquivo").empty();
                $(".invalid-p-arquivo").append('A extensão precisa ser .xlsx');

            }
        }
    })

    const buttons = document.querySelectorAll('[data-bs-toggle="collapse"]');

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            console.log(button);
            buttons.forEach(function (otherButton) {
                if (otherButton !== button) {
                    otherButton.classList.remove('botao-acionado');
                }
            });

            if(button.parentNode.tagName.toLowerCase() != 'li'){

                button.classList.toggle('botao-acionado');
            }

            for(elemento of document.querySelectorAll('.show')){

                if(elemento.id != "offcanvasScrolling"){
                    elemento.classList.remove('show')
                }
            }
        });
    });

    aplicaMascaraDinamica();
    validaDataMascara();
    validarData();


    </script>
</body>
</html>

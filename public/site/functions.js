const fechaModal = (id) => {
    $(`#${id}`).removeClass('show');
    $(`#${id}`).css('display', 'none');
    $(`#${id}-backdrop`).removeClass('show');
    $(`#${id}-backdrop`).css('display', 'none');
}

const setLocalNome = (body, input) => {
    var inputValue = $(input).val();
    $(body).empty();
    $(body).append(`<p>Deseja criar o Local ${inputValue}?</p>`);
}
const setMaquinaNome = (body, input) => {
    var inputValue = $(input).val();
    $(body).empty();
    $(body).append(`<p>Deseja criar a máquina ${inputValue}?</p>`);
}
const setMensagemIncluirUsuario = (body, local, clientes) => {
    var localValue = $(local).text();
    var clientesValue = $(clientes).text();
    $(body).empty();
    $(body).append(`<p>Realmente deseja incluir o(s) usuário(s) ${clientesValue} no local ${localValue}? Ao incluir, o usuário verá todas as informações deste local.</p>`);
}

const sendFormCriarLocal = () =>{
    showLoader();
    var invalidElements = $('.is-invalid');

    if(invalidElements.length == 0){
        var requiredFields = $('[required]');
    
        // Inicializa uma variável para verificar se há campos vazios
        var emptyFields = false;
    
        // Itera sobre os campos obrigatórios
        requiredFields.each(function() {
            // Verifica se o campo está vazio
            if ($(this).val() == "" || $(this).val() == " ") {
                // Adiciona uma classe de erro ou aplica uma estilização para destacar o campo vazio
                $(this).addClass('is-invalid'); // Exemplo de classe de erro
    
                // Marca que há pelo menos um campo vazio
                emptyFields = true;
            } else {
                // Remove a classe de erro se o campo não estiver vazio
                $(this).removeClass('is-invalid');
            }
        });
        if (!emptyFields) {
            $("#novo-local-form").submit();
        }
    }else{
        hideLoader();
    }
}
const sendFormCriar = (form) =>{
    showLoader();
    var invalidElements = $('.is-invalid');

    if(invalidElements.length == 0){
        var requiredFields = $('[required]');
    
        // Inicializa uma variável para verificar se há campos vazios
        var emptyFields = false;
    
        // Itera sobre os campos obrigatórios
        requiredFields.each(function() {
            // Verifica se o campo está vazio
            if ($(this).val() == "" || $(this).val() == " ") {
                // Adiciona uma classe de erro ou aplica uma estilização para destacar o campo vazio
                $(this).addClass('is-invalid'); // Exemplo de classe de erro
    
                // Marca que há pelo menos um campo vazio
                emptyFields = true;
            } else {
                // Remove a classe de erro se o campo não estiver vazio
                $(this).removeClass('is-invalid');
            }
        });
        if (!emptyFields) {
            $(`#${form}`).submit();
        }
    }else{
        hideLoader();
    }
}

const sendFormCriarQr= (id_form) =>{
    showLoader();
    var invalidElements = $('.is-invalid');

    if(invalidElements.length == 0){
        $(`#${id_form}`).submit();
    }else{
        hideLoader();
    }
}

const setComplementoCliente = () => {
    var valLocais = JSON.parse($("#input_locais").val());
    var valClientes = JSON.parse($("#input_clientes").val());
    
    
    var selectLocais = $("#select-local").val();
    var selectClientes = $("#select-cliente").val();
    
    if(selectLocais.length != 0 && selectClientes.length == 0){
        for(let i = 0; i < valLocais.length; i++){
            if(valLocais[i].id_local == selectLocais[0]){
                for(let i = 0; i < valClientes.length; i++){
                    if(valLocais[i].id_cliente == valClientes[i].id_cliente){
                        $("#select-cliente").val(valClientes[i].id_cliente);
                        $("#select-cliente").trigger('change');
                    }
                }
            }
        }
    }
}



const setComplementoLocal = () => {
    var valLocais = JSON.parse($("#input_locais").val());
    var valClientes = JSON.parse($("#input_clientes").val());
    
    
    var selectLocais = $("#select-local").val();
    var selectClientes = $("#select-cliente").val();
    
    if(selectLocais.length == 0 && selectClientes.length != 0){
        for(let i = 0; i < valLocais.length; i++){
            if(valLocais[i].id_cliente == selectClientes[0]){
                for(let i = 0; i < valClientes.length; i++){
                    $("#select-local").val(valLocais[i].id_local);
                    $("#select-local").trigger('change');
                }
            }
        }
    } 
}

const showLoader = () => {
    $("#loader").css("display", "flex");
}

const hideLoader = () => {
    $("#loader").css("display", "none");
}


const validarEmail = (campoId, mensagemId) => {
        let campo = $(`#${campoId}`);
        let mensagemError = $(`#${mensagemId}`);
        let valorCampo = $(`#${campoId}`).val();
        var validar = true;
        if(campo.hasAttribute('required')){
            validar = true;
        }else{
            if(email != ''){
                validar = true;
            }else{
                validar = false;
            }
        }

        if(validar == true){
            const regexEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            // Verifica se o e-mail atende à expressão regular e não começa com ponto
            const valido = regexEmail.test(valorCampo) && valorCampo.indexOf('.') !== 0;

            if (!valido) {
                campo.classList.add('is-invalid');
                mensagemError.empty();
                mensagemError.append('Campo inválido');
                return false;
            } else {
                campo.classList.remove('is-invalid');
                return true;
            }
        }else{
            campo.classList.remove('is-invalid');
            return true;
        }
}

const validarCelular = (campoId, mensagemId) => {
    let valorCampo = $(`#${campoId}`).val();
    let campo = $(`#${campoId}`);
    let mensagemError = $(`#${mensagemId}`);
    let isValid = false;

    if(valorCampo == ""){
        campo.addClass('is-invalid');
        mensagemError.empty();
        mensagemError.append('Campo obrigatório');
        return false
    }else if(valorCampo.length  < 12){
        mensagemError.empty();
        mensagemError.append('Campo inválido');
        campo.addClass('is-invalid');
        return false
    }else{
        campo.removeClass('is-invalid')
        isValid = true;
        return true
    }
}

const validarCampoNome = (campoId, mensagemId) => {

    let valorCampo = $(`#${campoId}`).val();
    let campo = $(`#${campoId}`);
    let mensagemError = $(`#${mensagemId}`);
    let isValid = false;

    if(valorCampo == ""){
        campo.addClass('is-invalid');
        mensagemError.empty();
        mensagemError.append('Campo obrigatório');
    }else if(valorCampo.length  < 2){
        mensagemError.empty();
        mensagemError.append('Campo inválido');
        campo.addClass('is-invalid');
    }else{
        campo.removeClass('is-invalid')
        isValid = true;
    }
}

const validarSelectLocalCliente = (campoId, mensagemId) => {

    let valorCampo = $(`#${campoId}`).val();
    let campo = $(`#${campoId}`);
    let mensagemError = $(`#${mensagemId}`);
    let isValid = false;

    if(valorCampo == ""){
        campo.addClass('is-invalid');
        mensagemError.empty();
        mensagemError.append('Campo obrigatório');
    }else{
        campo.removeClass('is-invalid')
        isValid = true;
    }
} 

const validarCpfCnpj = (documento, input) => {
    
    // Remover caracteres não numéricos
    const numeroDocumento = documento.replace(/[^\d]+/g, '');

    // Identificar se é CPF ou CNPJ
    if (numeroDocumento.length === 11) {
        // CPF
        const cpfMascarado = numeroDocumento.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        if (validarCPF(numeroDocumento)) {
            document.getElementById(input).value = cpfMascarado;
            document.getElementById(input).classList.remove("is-invalid");
            document.getElementById(input).classList.add("is-valid");
            return true;
        } else {
            document.getElementById(input).classList.remove("is-valid");
            document.getElementById(input).classList.add("is-invalid");
        console.log('Documento inválido:', documento);

            return false;
        }
    } else if (numeroDocumento.length === 14) {
        // CNPJ
        const cnpjMascarado = numeroDocumento.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        if (validarCNPJ(numeroDocumento)) {
            document.getElementById(input).value = cnpjMascarado;
            document.getElementById(input).classList.remove("is-invalid");
            document.getElementById(input).classList.add("is-valid");
            return true;
        } else {
            document.getElementById(input).classList.remove("is-valid");
            document.getElementById(input).classList.add("is-invalid");
        console.log('Documento inválido:', documento);

            return false;
        }
    } else {
        // Tamanho inválido para CPF ou CNPJ
        console.log('Documento inválido:', documento);
        document.getElementById(input).classList.remove("is-valid");
        document.getElementById(input).classList.add("is-invalid");
        return false;
    }
}

const coletaEndereco = async (cep) => {
    try{
        const cepFormatado = cep.replace(/\D/g, '');
        const apiUrl = `https://viacep.com.br/ws/${cepFormatado}/json/`

        const response = await fetch(apiUrl)

        const data = await response.json();

        return data
    }catch(error){
        console.log(error)
    }
}
function preencherEnderecoFocoNumero(cidadeInput, ufInput, logradouroInput, bairroInput, numeroInput, dadoEndereco){
    $(`#${cidadeInput}`).val(dadoEndereco.localidade);
    $(`#${ufInput}`).val(dadoEndereco.uf);
    $(`#${logradouroInput}`).val(dadoEndereco.logradouro);
    $(`#${bairroInput}`).val(dadoEndereco.bairro);
    $(`#${numeroInput}`).focus().select();
}



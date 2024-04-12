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
        $("#novo-local-form").submit();
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



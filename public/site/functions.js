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

const sendFormCriarLocal = () =>{
    var invalidElements = $('.is-invalid');

    if(invalidElements.length == 0){
        $("#novo-local-form").submit();
    }
}

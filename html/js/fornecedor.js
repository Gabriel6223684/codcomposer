import { Requests } from "./Requests.js";
import { Validate } from "./validate.js";

const Cadastrar = document.getElementById('cadastrar');

// Máscaras
$('#cpfcnpj').inputmask({ 'mask': ['999.999.999-99', '99.999.999/9999-99'] });
$('#telefone').inputmask({ 'mask': ['(99) 99999-9999'] });

// Cadastrar fornecedor
Cadastrar.addEventListener('click', async () => {
    const response = await Requests.SetForm('formCadastro').Post('/fornecedor/insert');
    console.log(response);

    if (response.status) {
        alert("Fornecedor cadastrado com sucesso!");
    } else {
        alert(response.msg);
    }
});
/*
// Função editar fornecedor
async function editarFornecedor(id) {

    const res = await fetch(`/fornecedor/listafornecedor?id=${id}`);
    const data = await res.json();

    const fornecedor = data.data.find(f => f[0] == id); // coluna 0 é o ID

    if (fornecedor) {
        document.getElementById("nome").value = fornecedor[1];
        document.getElementById("cpfcnpj").value = fornecedor[2];
        document.getElementById("email").value = fornecedor[3];
        editarId = id;
    }
}

// Excluir fornecedor
async function excluirFornecedor(id) {

    if (!confirm("Deseja realmente excluir este fornecedor?")) return;

    const res = await fetch(`/fornecedor/excluir/${id}`, {
        method: "DELETE"
    });

    const data = await res.json();

    alert(data.msg);
    carregarFornecedor(); 
}

// Carregar tabela DataTable
function carregarFornecedor() {
    $('#tabela').DataTable().ajax.reload(null, false);
}

// Inicializa quando abrir página
carregarFornecedor();

window.editarFornecedor = editarFornecedor;  // deixar acessível no HTML
window.excluirFornecedor = excluirFornecedor;*/

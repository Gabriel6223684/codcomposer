import { DataTables } from "./DataTables.js";

const tabelaClientes = new $('#tabela').DataTable({
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: true,
    stateSave: true,
    select: true,
    processing: true,
    serverSide: true,
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
        searchPlaceholder: 'Digite sua pesquisa...'
    },
    ajax: {
        url: '/cliente/listacliente',
        type: 'POST'
    }
});
DataTables.SetId('tabela').Post('/user/listacliente');


import { Validate } from "./validate.js";
import { Requests } from "./Requests.js";

const formCadastro = document.getElementById('form');
const cadastrar = document.getElementById('cadastrar');

$('#cpfcnpj').inputmask({ 'mask': ['999.999.999-99', '99.999.999/9999-99'] });
$('#telefone').inputmask({ 'mask': ['(99) 99 9 9999-9999'] });

cadastrar.addEventListener('click', async () => {
    // Valida formulário
    Validate.SetForm('form').Validate();

    // Envia dados para o backend
    const response = await Requests.SetForm('form').Post('/cliente/insert');

    console.log(response); // Debug

    if (response.status) {
        alert(response.msg);
        formCadastro.reset(); // Limpa formulário
        carregarCliente();    // Atualiza tabela
    } else {
        alert(response.msg || 'Erro ao salvar cliente');
    }
});

// Função para buscar clientes e preencher tabela
async function carregarCliente() {
    const res = await fetch('/cliente/listacliente', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=list'
    });
    const data = await res.json();

    const tbody = document.querySelector('#tabela');
    tbody.innerHTML = '';

    data.forEach(cliente => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${cliente.id}</td>
            <td>${cliente.nome}</td>
            <td>${cliente.cpf_cnpj}</td>
            <td>${cliente.email}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="editarCliente(${cliente.id})">Editar</button>
                <button class="btn btn-danger btn-sm" onclick="excluirCliente(${cliente.id})">Excluir</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Chama ao carregar a página
carregarCliente();

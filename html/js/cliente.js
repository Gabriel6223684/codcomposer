// Máscaras
$('#cpfcnpj').inputmask({ 'mask': ['999.999.999-99', '99.999.999/9999-99'] });
$('#telefone').inputmask('(99) 9 9999-9999');

// cliente.js
import { Requests } from "./Requests.js";
import $ from 'jquery';
import 'inputmask/dist/jquery.inputmask';

document.addEventListener('DOMContentLoaded', () => {
    const cadastrar = document.getElementById('cadastrar');
    const form = document.getElementById('form'); // ID do formulário no HTML

    // Máscaras
    $('#cpfcnpj').inputmask({ 'mask': ['999.999.999-99', '99.999.999/9999-99'] });

    // Botão cadastrar
    cadastrar.addEventListener('click', async () => {
        try {
            // Validação simples sem jQuery Validation
            const nome = document.getElementById('nome').value.trim();
            const cpfcnpj = document.getElementById('cpfcnpj').value.trim();
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();

            if (!nome || !cpfcnpj || !email || !senha) {
                alert('Todos os campos são obrigatórios!');
                return;
            }

            // Envia dados para o backend
            const response = await Requests.SetForm('form').Post('/cliente/insert');

            console.log('Resposta do servidor:', response);

            if (response.status) {
                alert('Cliente cadastrado com sucesso!');
                form.reset();
            } else {
                alert('Erro ao cadastrar: ' + response.msg);
            }
        } catch (error) {
            console.error('Erro ao cadastrar cliente:', error);
            alert('Erro ao enviar dados. Veja o console para detalhes.');
        }
    });
});

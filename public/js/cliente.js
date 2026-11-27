import { applyMask, validationNumber } from '/js/utils/util.js';

const cpfCnpjInput  = document.getElementById('cpf_cnpj');
const telefoneInput = document.getElementById('telefone');

applyMask(cpfCnpjInput, 'number');
applyMask(telefoneInput, 'number');

cpfCnpjInput?.addEventListener('blur', () => validationNumber(cpfCnpjInput));
telefoneInput?.addEventListener('blur', () => validationNumber(telefoneInput));
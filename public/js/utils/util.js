
export function sanitizeInput(value) {
  return value.replace(/[<>"';()&+\\-]/g, "");
}

export function showFieldError(campo, mensagem) {
  campo.classList.add("invalid", "border-rose-500", "focus:ring-rose-100");

  let errorEl = campo.parentElement.querySelector(".field-error");
  if (!errorEl) {
    errorEl = document.createElement("span");
    errorEl.className = "field-error mt-2 block text-sm font-medium text-rose-600";
    campo.parentElement.appendChild(errorEl);
  }
  errorEl.textContent = mensagem;
  errorEl.style.display = "block";
}

export function hideFieldError(campo) {
  campo.classList.remove("invalid", "border-rose-500", "focus:ring-rose-100");

  const errorEl = campo.parentElement.querySelector(".field-error");
  if (errorEl) {
    errorEl.textContent = "";
    errorEl.style.display = "none";
  }
}

export function validateRequired(campo, mensagem = "Este campo é obrigatório") {
  const value = campo.value.trim();
  if (!value) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}

export function validatePassword(campo, mensagem = "A senha não atende todos os requisitos") {
  const value = campo.value.trim();
  if (value.length > 0 && !allRequirementsPass(value)) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}

export function validateConfirmPassword(campo, senhaCampo, mensagem = "As senhas não coincidem") {
  const value = campo.value.trim();
  const senhaValue = senhaCampo.value.trim();

  if (value !== senhaValue && value.length > 0) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}

export function validationText(campo, mensagem = "Apenas letras são permitidas") {
  const regex = /^[A-Za-zÀ-ÿ\s]*$/;
  if (campo.value && !regex.test(campo.value)) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}

export function validationNumber(campo, mensagem = "Apenas números são permitidos") {
  const regex = /^[0-9]*$/;
  if (campo.value && !regex.test(campo.value)) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}


export function validationAlphaNumeric(campo, mensagem = "Apenas letras e números são permitidos") {
  const regex = /^[A-Za-zÀ-ÿ0-9\s\-_.]*$/;
  if (campo.value && !regex.test(campo.value)) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}

export function validationEmail(campo, errorElement, mensagem) {
  const regex =
    /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]{2,})+$/;
  const isValid = regex.test(campo.value);
  if (!isValid) {
    campo.classList.add("invalid");
    showError(campo, errorElement, mensagem);
  } else if (campo.value === "") {
    campo.classList.remove("invalid");
    hideError(campo, errorElement);
  } else {
    campo.classList.remove("invalid");
    hideError(campo, errorElement);
  }

  return isValid;
}


export function validateIp(campo, mensagem = "Formato de IP inválido (ex: 192.168.1.100:80)") {
  // const regex = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
  const regex = /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)(:\d{1,5})?$/;
  if (campo.value && !regex.test(campo.value)) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}


export function validateIpRequired(campo, mensagem = "Formato de IP inválido (ex: 192.168.1.100:80)") {
  if (!campo.value.trim()) {
    showFieldError(campo, "Este campo é obrigatório");
    return false;
  }
  return validateIp(campo, mensagem);
}


export function validateIps(campo, mensagem) {
  return validateIp(campo, mensagem);
}


export function validateVersion(campo, mensagem = "Formato de versão inválido (ex: 8.2 ou 10.0.1)") {
  const regex = /^\d+(\.\d+){0,2}$/;

  if (campo.value && !regex.test(campo.value)) {
    showFieldError(campo, mensagem);
    return false;
  }
  hideFieldError(campo);
  return true;
}

export function validateVersaoLinguagem(campo, mensagem) {
  return validateVersion(campo, mensagem);
}

export function validateCpfCnpj(campo, mensagem = "CPF (11 digitos) ou CNPJ (14 digitos) inválido") {
  const digit = campo.value.replace(/\D/g, '');
  if (digit.length > 0 && digit.length !== 11 && digit.length !== 14) {
    showFieldErros(campo, mensagens);
    return false;
  }
  hideFieldErros(campo);
  return true;
}

export function validateTelefone(campo,mensagens = "Telefone inválido (10 ou 11 dígitos") {
  const digit = campo.value.replace(/\D/g, '');
  if (digit.length > 0 && digit.length !== 10 && digit.legth !== 11) {
    showFieldErros(campo, mensagens);
  return false
  }
  hideFieldErros(campo);
  return true;
}
// ============================================
// MÁSCARAS DE INPUT (restringe caracteres)
// ============================================

export function maskIp(campo) {
  campo.value = campo.value.replace(/[^0-9.:]/g, '');
}


export function maskVersion(campo) {
  campo.value = campo.value.replace(/[^0-9.]/g, '');
}


export function maskNumber(campo) {
  campo.value = campo.value.replace(/[^0-9]/g, '');
}

export function maskCpfCnpj(campo) {
  let v = campo.value.replace(/\D/g, '').slice(0,14);

  if (v.length <= 11) {
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
  } else {
    v = v.replace(/^(\d{2})(\d)/, '$1.$2');
    v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
    v = v.replace(/(\d{4})(\d)/, '$1-$2');
  }

  campo.value = v;
}

export function maskTelefone(campo) {
  let v = campo.value.replace(/\D/g, '').slice(0, 11);

  if (v.length <= 10) {
    v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
    v = v.replace(/(\d{4})(\d)/, '$1-$2');
  } else {
    v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
    v = v.replace(/(\d{5})(\d)/, '$1-$2');
  }

  campo.value = v;
}

export function applyMask(campo, tipo) {
  if (!campo) return;

  campo.addEventListener('input', function() {
    switch(tipo) {
      case 'ip':
        maskIp(campo);
        break;
      case 'version':
        maskVersion(campo);
        break;
      case 'number':
        maskNumber(campo);
        break;
        case 'cpf_cnpj':
          maskCpfCnpj(campo);
          break;
          case 'telefone':
            maskTelefone(campo);
            break;
    }
  });
}


export const PASSWORD_RULES = [
    { text: 'Mínimo de 8 caracteres',           regex: /.{8,}/ },
    { text: 'Caracter especial ex: #@*',         regex: /[!@#$%^&*()_+\-=\[\]{};':"\\|\/?]/ },
    { text: 'Uma letra maiúscula',               regex: /[A-Z]/ },
    { text: 'Uma letra minúscula',               regex: /[a-z]/ },
    { text: 'Conter número',                     regex: /[0-9]/ },
];

export function construirListadeRequisitosSenha(listaElement) {
    if (!listaElement) return;

    listaElement.innerHTML = '';
    PASSWORD_RULES.forEach(function (rule) {
        const li = document.createElement('li');
        li.className = 'password-requirements__item flex items-center gap-2 text-sm text-slate-500';

        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-xmark text-rose-400';

        const span = document.createElement('span');
        span.textContent = rule.text;

        li.appendChild(icon);
        li.appendChild(span);
        listaElement.appendChild(li);
    });
}

export function checarRequisitosSenha(ulElement, senha) {
    if (!ulElement) return false;

    const items = ulElement.querySelectorAll('.password-requirements__item');
    let allValid = true;

    items.forEach(function (item, index) {
        const passed = PASSWORD_RULES[index].regex.test(senha);
        item.classList.toggle('valid', passed);
        item.classList.toggle('text-emerald-600', passed);
        item.classList.toggle('text-slate-500', !passed);
        item.querySelector('i').className = passed
            ? 'fa-solid fa-check text-emerald-600'
            : 'fa-solid fa-xmark text-rose-400';

        if (!passed) allValid = false;
    });

    return allValid;
}

export function allRequirementsPass(senha) {
    return PASSWORD_RULES.every(function (rule) {
        return rule.regex.test(senha);
    });
}



function showError(campo, errorElement, mensagem) {
  campo.classList.add("invalid", "border-rose-500", "focus:ring-rose-100");
  if (errorElement) {
    errorElement.textContent = mensagem;
    errorElement.style.display = "block";
  }
}

function hideError(campo, errorElement) {
  campo.classList.remove("invalid", "border-rose-500", "focus:ring-rose-100");
  if (errorElement) {
    errorElement.textContent = "";
    errorElement.style.display = "none";
  }
}

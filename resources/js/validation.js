/**
 * Validação em Tempo Real (Vanilla JS)
 * Suporta validação reativa para:
 * - Nome (3 a 100 caracteres)
 * - E-mail (formato válido via regex)
 * - Senha (mínimo de 8 caracteres)
 * - Confirmação de Senha (conferência exata)
 */

export function initRealtimeValidation() {
    setupRegisterForm();
    setupLoginForm();
    setupForgotPasswordForm();
    setupResetPasswordForm();
}

/**
 * Obtém ou cria o elemento de feedback de erro associado ao input
 */
function getOrCreateErrorFeedback(input) {
    const parent = input.closest('.input-group') || input.parentElement;
    let feedback = parent.querySelector('.invalid-feedback');

    if (!feedback) {
        feedback = parent.nextElementSibling?.classList.contains('invalid-feedback')
            ? parent.nextElementSibling
            : null;
    }

    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback fw-semibold mt-1 text-danger small';
        if (input.closest('.input-group')) {
            parent.insertAdjacentElement('afterend', feedback);
        } else {
            input.insertAdjacentElement('afterend', feedback);
        }
    }

    return feedback;
}

/**
 * Obtém ou cria o elemento de feedback de sucesso associado ao input
 */
function getOrCreateSuccessFeedback(input) {
    const parent = input.closest('.input-group') || input.parentElement;
    let feedback = parent.querySelector('.valid-feedback');

    if (!feedback) {
        feedback = parent.nextElementSibling?.classList.contains('valid-feedback')
            ? parent.nextElementSibling
            : null;
    }

    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'valid-feedback fw-semibold mt-1 text-success small';
        if (input.closest('.input-group')) {
            parent.insertAdjacentElement('afterend', feedback);
        } else {
            input.insertAdjacentElement('afterend', feedback);
        }
    }

    return feedback;
}

/**
 * Exibe estado de erro no campo
 */
function showError(input, message) {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid', 'border-danger');

    const feedback = getOrCreateErrorFeedback(input);
    feedback.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i> ${message}`;
    feedback.classList.add('d-block');

    const parent = input.closest('.input-group') || input.parentElement;
    const successFeedback = parent.querySelector('.valid-feedback') || parent.nextElementSibling?.classList.contains('valid-feedback') ? parent.nextElementSibling : null;
    if (successFeedback) {
        successFeedback.classList.remove('d-block');
        successFeedback.style.display = 'none';
    }
}

/**
 * Exibe estado de sucesso no campo
 */
function showSuccess(input, message = '') {
    input.classList.remove('is-invalid', 'border-danger');
    input.classList.add('is-valid');

    const errorFeedback = getOrCreateErrorFeedback(input);
    errorFeedback.classList.remove('d-block');
    errorFeedback.style.display = 'none';

    if (message) {
        const successFeedback = getOrCreateSuccessFeedback(input);
        successFeedback.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${message}`;
        successFeedback.classList.add('d-block');
        successFeedback.style.display = 'block';
    } else {
        const parent = input.closest('.input-group') || input.parentElement;
        const successFeedback = parent.querySelector('.valid-feedback') || (parent.nextElementSibling?.classList.contains('valid-feedback') ? parent.nextElementSibling : null);
        if (successFeedback) {
            successFeedback.classList.remove('d-block');
            successFeedback.style.display = 'none';
        }
    }
}

/**
 * Limpa os estados de validação
 */
function clearStatus(input) {
    input.classList.remove('is-invalid', 'is-valid', 'border-danger');
    const parent = input.closest('.input-group') || input.parentElement;
    const err = parent.querySelector('.invalid-feedback') || (parent.nextElementSibling?.classList.contains('invalid-feedback') ? parent.nextElementSibling : null);
    if (err) {
        err.classList.remove('d-block');
        err.style.display = 'none';
    }
    const succ = parent.querySelector('.valid-feedback') || (parent.nextElementSibling?.classList.contains('valid-feedback') ? parent.nextElementSibling : null);
    if (succ) {
        succ.classList.remove('d-block');
        succ.style.display = 'none';
    }
}

/**
 * Validador de Nome (3 a 100 caracteres)
 */
function validateName(input, isSubmitting = false) {
    const val = input.value.trim();

    if (val.length === 0) {
        if (isSubmitting || input.dataset.touched) {
            showError(input, 'O nome completo é obrigatório.');
            return false;
        }
        clearStatus(input);
        return false;
    }

    if (val.length < 3) {
        showError(input, `O nome deve ter no mínimo 3 caracteres (atual: ${val.length}).`);
        return false;
    }

    if (val.length > 100) {
        showError(input, `O nome não pode ter mais de 100 caracteres (atual: ${val.length}).`);
        return false;
    }

    showSuccess(input);
    return true;
}

/**
 * Validador de E-mail (Regex padrão)
 */
function validateEmail(input, isSubmitting = false) {
    const val = input.value.trim();
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (val.length === 0) {
        if (isSubmitting || input.dataset.touched) {
            showError(input, 'O e-mail é obrigatório.');
            return false;
        }
        clearStatus(input);
        return false;
    }

    if (!emailRegex.test(val)) {
        showError(input, 'Insira um e-mail válido (exemplo: usuario@email.com).');
        return false;
    }

    showSuccess(input);
    return true;
}

/**
 * Validador de Senha (Mínimo de 8 caracteres)
 */
function validatePassword(input, isSubmitting = false) {
    const val = input.value;

    if (val.length === 0) {
        if (isSubmitting || input.dataset.touched) {
            showError(input, 'A senha é obrigatória.');
            return false;
        }
        clearStatus(input);
        return false;
    }

    if (val.length < 8) {
        showError(input, `A senha deve ter no mínimo 8 caracteres (atual: ${val.length}).`);
        return false;
    }

    showSuccess(input);
    return true;
}

/**
 * Validador de Confirmação de Senha
 */
function validatePasswordConfirmation(confirmInput, passwordInput, isSubmitting = false) {
    const confirmVal = confirmInput.value;
    const passVal = passwordInput ? passwordInput.value : '';

    if (confirmVal.length === 0) {
        if (isSubmitting || confirmInput.dataset.touched) {
            showError(confirmInput, 'Confirme sua senha.');
            return false;
        }
        clearStatus(confirmInput);
        return false;
    }

    if (confirmVal !== passVal) {
        showError(confirmInput, 'As senhas não coincidem.');
        return false;
    }

    if (passVal.length >= 8) {
        showSuccess(confirmInput, 'As senhas coincidem.');
    } else {
        showSuccess(confirmInput);
    }
    return true;
}

/**
 * Configura formulário de Registro
 */
function setupRegisterForm() {
    const form = document.querySelector('#registerModal form');
    if (!form) return;

    const nameInput = form.querySelector('input[name="name"]');
    const emailInput = form.querySelector('input[name="email"]');
    const passInput = form.querySelector('input[name="password"]');
    const confirmInput = form.querySelector('input[name="password_confirmation"]');

    if (nameInput) {
        nameInput.setAttribute('minlength', '3');
        nameInput.setAttribute('maxlength', '100');

        nameInput.addEventListener('input', () => {
            nameInput.dataset.touched = 'true';
            validateName(nameInput);
        });
        nameInput.addEventListener('blur', () => {
            nameInput.dataset.touched = 'true';
            validateName(nameInput);
        });
    }

    if (emailInput) {
        emailInput.addEventListener('input', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
        emailInput.addEventListener('blur', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
    }

    if (passInput) {
        passInput.addEventListener('input', () => {
            passInput.dataset.touched = 'true';
            validatePassword(passInput);
            if (confirmInput && (confirmInput.value.length > 0 || confirmInput.dataset.touched)) {
                validatePasswordConfirmation(confirmInput, passInput);
            }
        });
        passInput.addEventListener('blur', () => {
            passInput.dataset.touched = 'true';
            validatePassword(passInput);
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', () => {
            confirmInput.dataset.touched = 'true';
            validatePasswordConfirmation(confirmInput, passInput);
        });
        confirmInput.addEventListener('blur', () => {
            confirmInput.dataset.touched = 'true';
            validatePasswordConfirmation(confirmInput, passInput);
        });
    }

    form.addEventListener('submit', (e) => {
        let valid = true;
        if (nameInput && !validateName(nameInput, true)) valid = false;
        if (emailInput && !validateEmail(emailInput, true)) valid = false;
        if (passInput && !validatePassword(passInput, true)) valid = false;
        if (confirmInput && !validatePasswordConfirmation(confirmInput, passInput, true)) valid = false;

        if (!valid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
    });
}

/**
 * Configura formulário de Login
 */
function setupLoginForm() {
    const form = document.querySelector('#loginModal form');
    if (!form) return;

    const emailInput = form.querySelector('input[name="email"]');
    const passInput = form.querySelector('input[name="password"]');

    if (emailInput) {
        emailInput.addEventListener('input', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
        emailInput.addEventListener('blur', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
    }

    if (passInput) {
        passInput.addEventListener('input', () => {
            if (passInput.value.length > 0) {
                passInput.classList.remove('is-invalid', 'border-danger');
                const parent = passInput.closest('.input-group') || passInput.parentElement;
                const err = parent.querySelector('.invalid-feedback') || (parent.nextElementSibling?.classList.contains('invalid-feedback') ? parent.nextElementSibling : null);
                if (err) {
                    err.classList.remove('d-block');
                    err.style.display = 'none';
                }
            }
        });
    }

    form.addEventListener('submit', (e) => {
        let valid = true;
        if (emailInput && !validateEmail(emailInput, true)) valid = false;
        if (passInput && passInput.value.length === 0) {
            showError(passInput, 'A senha é obrigatória.');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
    });
}

/**
 * Configura formulário de Esqueci a Senha
 */
function setupForgotPasswordForm() {
    const form = document.querySelector('#forgotPasswordModal form');
    if (!form) return;

    const emailInput = form.querySelector('input[name="email"]');

    if (emailInput) {
        emailInput.addEventListener('input', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
        emailInput.addEventListener('blur', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
    }

    form.addEventListener('submit', (e) => {
        if (emailInput && !validateEmail(emailInput, true)) {
            e.preventDefault();
            emailInput.focus();
        }
    });
}

/**
 * Configura tela de Reset de Senha (reset-password.blade.php)
 */
function setupResetPasswordForm() {
    const form = document.querySelector('form[action*="reset-password"]');
    if (!form) return;

    const emailInput = form.querySelector('input[name="email"]');
    const passInput = form.querySelector('input[name="password"]');
    const confirmInput = form.querySelector('input[name="password_confirmation"]');

    if (emailInput) {
        emailInput.addEventListener('input', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
        emailInput.addEventListener('blur', () => {
            emailInput.dataset.touched = 'true';
            validateEmail(emailInput);
        });
    }

    if (passInput) {
        passInput.addEventListener('input', () => {
            passInput.dataset.touched = 'true';
            validatePassword(passInput);
            if (confirmInput && (confirmInput.value.length > 0 || confirmInput.dataset.touched)) {
                validatePasswordConfirmation(confirmInput, passInput);
            }
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', () => {
            confirmInput.dataset.touched = 'true';
            validatePasswordConfirmation(confirmInput, passInput);
        });
    }

    form.addEventListener('submit', (e) => {
        let valid = true;
        if (emailInput && !validateEmail(emailInput, true)) valid = false;
        if (passInput && !validatePassword(passInput, true)) valid = false;
        if (confirmInput && !validatePasswordConfirmation(confirmInput, passInput, true)) valid = false;

        if (!valid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
    });
}

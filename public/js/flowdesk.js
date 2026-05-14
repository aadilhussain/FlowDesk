const authForm = document.querySelector('[data-auth-form]');

if (authForm) {
    const message = document.querySelector('[data-auth-message]');
    const action = authForm.dataset.authAction;
    const endpoint = action === 'register' ? '/api/v1/register' : '/api/v1/login';

    authForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        showMessage(message, action === 'register' ? 'Creating account...' : 'Signing in...');

        const payload = Object.fromEntries(new FormData(authForm));

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            showMessage(message, validationMessage(data), true);
            return;
        }

        localStorage.setItem('flowdesk_token', data.token);
        localStorage.setItem('flowdesk_user', JSON.stringify(data.user));

        if (data.workspace) {
            localStorage.setItem('flowdesk_workspace', JSON.stringify(data.workspace));
        }

        showMessage(
            message,
            action === 'register'
                ? `Workspace created: ${data.workspace.name}. Redirecting to overview...`
                : `Signed in as ${data.user.email}. Redirecting to overview...`,
        );

        window.setTimeout(() => {
            window.location.href = '/';
        }, 900);
    });
}

function showMessage(element, text, isError = false) {
    element.hidden = false;
    element.classList.toggle('error', isError);
    element.textContent = text;
}

function validationMessage(data) {
    if (data.errors) {
        return Object.values(data.errors).flat().join(' ');
    }

    return data.message || 'Something went wrong. Please try again.';
}

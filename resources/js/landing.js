import "./bootstrap";

const form = document.getElementById("landing-login");
const message = document.getElementById("login-message");

function showMessage(text, variant = "") {
    if (!message) return;
    message.textContent = text;
    const classes = ["message", "text-center", "small", "mt-3"];
    if (variant) classes.push(variant);
    message.className = classes.join(" ");
}

async function handleLogin(event) {
    event.preventDefault();
    showMessage("Entrando...");

    const payload = Object.fromEntries(new FormData(form).entries());

    try {
        const response = await fetch("/api/auth/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || "Credenciais invalidas");
        }

        const data = await response.json();
        localStorage.setItem("financas_token", data.token);
        showMessage("Login realizado! Redirecionando...", "success");
        setTimeout(() => {
            window.location.href = "/app";
        }, 600);
    } catch (error) {
        showMessage(error.message || "Falha ao autenticar", "error");
    }
}

form?.addEventListener("submit", handleLogin);

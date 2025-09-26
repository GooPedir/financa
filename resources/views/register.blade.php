<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar • Financas</title>
    <style>
        body{font-family:ui-sans-serif,system-ui;-webkit-font-smoothing:antialiased;margin:0;padding:2rem;background:#f7f7f7}
        form{max-width:480px;margin:2rem auto;background:#fff;padding:1.5rem;border-radius:.75rem;border:1px solid #e5e7eb}
        label{display:block;margin:.5rem 0 .25rem}
        input{width:100%;padding:.65rem;border:1px solid #d1d5db;border-radius:.5rem}
        button{margin-top:1rem;background:#086A54;color:#fff;border:none;padding:.75rem 1rem;border-radius:.5rem}
    </style>
    <script>
        async function submitForm(e){
            e.preventDefault();
            const f=e.target; const payload={name:f.name.value,email:f.email.value,password:f.password.value,tenant_name:f.tenant_name.value};
            const res=await fetch('/api/auth/register',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
            const data=await res.json();
            if(res.ok){ alert('Conta criada! Guarde seu token:\n'+data.token); window.location='/'; } else { alert('Erro: '+(data.message||'Falha no cadastro')); }
        }
    </script>
</head>
<body>
    <form onsubmit="submitForm(event)">
        <h1>Crie sua conta</h1>
        <label>Nome</label>
        <input name="name" required>
        <label>E-mail</label>
        <input name="email" type="email" required>
        <label>Senha</label>
        <input name="password" type="password" minlength="8" required>
        <label>Nome do workspace</label>
        <input name="tenant_name" placeholder="Minha Empresa">
        <button type="submit">Registrar</button>
    </form>
</body>
</html>


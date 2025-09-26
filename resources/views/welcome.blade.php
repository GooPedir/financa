<!doctype html>
<html class="h-full" lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Financas</title>
    <style>
        :root { --green:#086A54; --bg:#fff; --fg:#0b0b0b; }
        .dark:root { --bg:#0b0b0b; --fg:#f3f3f3; }
        html,body { height:100%; margin:0; font-family:ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto; background:var(--bg); color:var(--fg); }
        .container { max-width:1024px; margin:0 auto; padding:2rem; }
        .hero { display:flex; flex-direction:column; gap:1rem; padding:4rem 0; }
        .btn { background:var(--green); color:#fff; border:none; padding:.75rem 1rem; border-radius:.5rem; text-decoration:none; display:inline-block; }
        .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; background:rgba(8,106,84,.1); color:var(--green); font-weight:600; }
        .features { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1rem; }
        .card { border:1px solid rgba(0,0,0,.08); border-radius:.75rem; padding:1rem; background:rgba(255,255,255,.6); }
        .dark .card { background:rgba(255,255,255,.03); border-color:rgba(255,255,255,.12); }
        header { display:flex; align-items:center; justify-content:space-between; padding:1rem 0; }
        .toggle { background:transparent; color:var(--fg); border:1px solid currentColor; border-radius:.5rem; padding:.5rem .75rem; }
        a { color:var(--green); }
    </style>
    <script>
        const setTheme = t => { document.documentElement.classList.toggle('dark', t==='dark'); localStorage.theme=t; };
        (()=>{ setTheme(localStorage.theme || (matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light')); })();
    </script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='48' fill='%23086A54'/></svg>">
</head>
<body>
    <div class="container">
        <header>
            <strong>Financas</strong>
            <div>
                <button class="toggle" onclick="setTheme(document.documentElement.classList.contains('dark')?'light':'dark')">Dark mode</button>
            </div>
        </header>
        <section class="hero">
            <span class="badge">Multi-tenant por workspace</span>
            <h1 style="font-size:2.25rem; line-height:1.2; margin:0">Organize suas finanças com segurança e colaboração</h1>
            <p style="max-width:60ch">Contas separadas e conjuntas, cartões com faturas mensais, categorias hierárquicas, metas e relatórios. Tudo isolado por tenant e com controle de membros.</p>
            <p>
                <a class="btn" href="/register">Criar conta</a>
            </p>
        </section>
        <section class="features">
            <div class="card"><h3>Cartões e Faturas</h3><p>Limite, fechamento, vencimento e compras parceladas com controle do comprometido.</p></div>
            <div class="card"><h3>Categorias & Metas</h3><p>Estruture despesas e receitas e acompanhe o progresso das metas.</p></div>
            <div class="card"><h3>Relatórios</h3><p>Fluxo de caixa, por categoria, evolução de saldo e próximos vencimentos.</p></div>
            <div class="card"><h3>Importação CSV</h3><p>Importe lançamentos por conta e acelere a organização.</p></div>
        </section>
        <footer style="margin-top:3rem; opacity:.7">Tema branco/verde • #086A54 • Dark mode</footer>
    </div>
    </body>
    </html>


<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Financas Workspace</title>
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="#">Financas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="landingNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#recursos">Recursos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/api/docs">API Docs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#login">Entrar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="py-5">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <span class="badge rounded-pill text-bg-primary mb-3">Planejamento colaborativo</span>
                    <h1 class="display-5 fw-bold">Controle total das financas do seu workspace</h1>
                    <p class="lead text-secondary">
                        Acompanhe contas, cartoes e metas financeiras em tempo real. Estruture categorias fixas, gastos parcelados e despesas unicas com poucos cliques.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a class="btn btn-primary btn-lg" href="#login">Entrar agora</a>
                        <a class="btn btn-outline-secondary btn-lg" href="#recursos">Conhecer recursos</a>
                    </div>
                                        <ul class="list-unstyled mt-4 text-secondary small">
                        <li class="mb-1">- Suporte a multi-tenant com controle de membros</li>
                        <li class="mb-1">- Lancamentos recorrentes e parcelados com historico</li>
                        <li>- Metas com reservas de valor e acompanhamento mensal</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="landing-hero card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Visao do mes</h5>
                            <div class="row text-center">
                                <div class="col-6">
                                    <p class="text-secondary small mb-1">Ganhos</p>
                                    <p class="h4 fw-bold text-success">R$ 8.450</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-secondary small mb-1">Despesas</p>
                                    <p class="h4 fw-bold text-danger">R$ 6.120</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-secondary small mb-2">Progresso das metas</p>
                                <div class="progress mb-2" role="progressbar" aria-label="Meta 1" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-success" style="width:65%">Reserva viagem</div>
                                </div>
                                <div class="progress" role="progressbar" aria-label="Meta 2" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-info text-dark" style="width:35%">Fundo emergencia</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="recursos" class="py-5 bg-white">
        <div class="container">
            <h2 class="fw-semibold text-center mb-4">O que voce encontra na plataforma</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Dashboard inteligente</h5>
                            <p class="card-text text-secondary">Visao do mes com ganhos x despesas, comparativo de despesas fixas vs parceladas e metas em andamento.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Lancamentos completos</h5>
                            <p class="card-text text-secondary">Despesas retroativas, parceladas, recorrentes e cartoes com controle de faturas e parcelas futuras.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Reservas para metas</h5>
                            <p class="card-text text-secondary">Movimente valores para metas como uma reserva rapida e acompanhe o saldo atingido a cada aporte.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="login" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h2 class="h4 fw-semibold mb-3">Entrar na plataforma</h2>
                            <p class="text-secondary small">Use as credenciais do seed ou crie um cadastro em poucos segundos.</p>
                            <form id="landing-login" class="mt-3">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required value="owner@example.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Senha</label>
                                    <input type="password" class="form-control" name="password" required value="password">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Entrar</button>
                                <p class="text-center small mt-3 mb-0">Precisa cadastrar novos dados? <a href="/register">Acesse aqui</a>.</p>
                                <div id="login-message" class="message text-center small mt-3"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 bg-dark text-white-50">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <span>&copy; {{ date('Y') }} Financas Workspace</span>
            <div class="d-flex gap-3">
                <a class="text-white-50" href="#recursos">Recursos</a>
                <a class="text-white-50" href="https://laravel.com" target="_blank" rel="noopener">Laravel 11</a>
                <a class="text-white-50" href="/api/docs">API Docs</a>
            </div>
        </div>
    </footer>
</body>
</html>


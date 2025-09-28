<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Financas Workspace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-semibold" href="/">Financas</a>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="text-white-50 small" data-field="tenant-name">-</span>
                <button id="logout" class="btn btn-outline-light btn-sm">Sair</button>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4" id="app" data-page="dashboard">
        <div class="row">
            <div class="col-xl-2 col-lg-3 mb-4">
                <div class="list-group" id="dashboard-nav">
                    <button class="list-group-item list-group-item-action active" data-panel="dashboard">Dashboard</button>
                    <button class="list-group-item list-group-item-action" data-panel="transactions">Lancamentos</button>
                    <button class="list-group-item list-group-item-action" data-panel="categories">Categorias</button>
                    <button class="list-group-item list-group-item-action" data-panel="accounts">Contas</button>
                    <button class="list-group-item list-group-item-action" data-panel="cards">Cartoes</button>
                    <button class="list-group-item list-group-item-action" data-panel="goals">Metas</button>
                    <button class="list-group-item list-group-item-action" data-panel="recurrences">Recorrencias</button>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title text-secondary text-uppercase small">Usuario</h6>
                        <p class="mb-0 fw-semibold" data-field="user-name">-</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-10 col-lg-9">
                <section id="panel-dashboard" class="panel active">
                    <div class="row g-3" id="summary-cards">
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-secondary small text-uppercase">Ganhos do mes</p>
                                    <h3 class="fw-semibold" data-summary="income">R$ 0,00</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-secondary small text-uppercase">Despesas do mes</p>
                                    <h3 class="fw-semibold text-danger" data-summary="expenses">R$ 0,00</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-secondary small text-uppercase">Resultado liquido</p>
                                    <h3 class="fw-semibold" data-summary="net">R$ 0,00</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-lg-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Ganhos x Despesas</h5>
                                    <p class="small text-secondary">Comparativo do periodo selecionado.</p>
                                    <div class="progress bg-body-secondary" style="height: 1.5rem">
                                        <div class="progress-bar bg-success" role="progressbar" data-summary="income-ratio" style="width:50%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-secondary mt-2">
                                        <span>Despesas</span>
                                        <span data-summary="expense-percent">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Despesas por tipo</h5>
                                    <div class="list-group list-group-flush" id="expense-breakdown">
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span>Fixas</span>
                                            <strong data-breakdown="FIXED">R$ 0,00</strong>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span>Parceladas</span>
                                            <strong data-breakdown="INSTALLMENT">R$ 0,00</strong>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span>Unicas</span>
                                            <strong data-breakdown="ONE_TIME">R$ 0,00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-xl-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Ultimos lancamentos</h5>
                                        <button class="btn btn-outline-secondary btn-sm" data-action="refresh-summary">Atualizar</button>
                                    </div>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm align-middle" id="latest-transactions">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Data</th>
                                                    <th>Descricao</th>
                                                    <th>Conta</th>
                                                    <th class="text-end">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card shadow-sm h-100 mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Metas</h5>
                                    <div class="list-group small" id="goal-progress"></div>
                                </div>
                            </div>
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Parcelas futuras</h5>
                                    <ul class="list-group list-group-flush small" id="installment-list"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Recorrencias agendadas</h5>
                            <ul class="list-group list-group-flush" id="recurrence-summary"></ul>
                        </div>
                    </div>
                </section>

                <section id="panel-transactions" class="panel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Novo lancamento</h5>
                                <small class="text-secondary">Suporta lancamentos retroativos e recorrentes</small>
                            </div>
                            <form id="transaction-form" class="row g-3 mt-2">
                                <div class="col-md-3">
                                    <label class="form-label">Tipo</label>
                                    <select name="type" class="form-select" required>
                                        <option value="INCOME">Receita</option>
                                        <option value="EXPENSE">Despesa</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Conta</label>
                                    <select name="account_id" class="form-select" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Categoria</label>
                                    <select name="category_id" class="form-select"></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Valor</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Data</label>
                                    <input type="date" class="form-control" name="date" required>
                                </div>
                                <div class="col-md-3 expense-kind-field d-none">
                                    <label class="form-label">Tipo de despesa</label>
                                    <select name="expense_kind" class="form-select">
                                        <option value="ONE_TIME">Unica</option>
                                        <option value="FIXED">Fixa</option>
                                        <option value="INSTALLMENT">Parcelada</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Descricao</label>
                                    <input type="text" class="form-control" name="description" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Recorrencia</label>
                                    <select name="recurrence_frequency" class="form-select">
                                        <option value="">Nao repetir</option>
                                        <option value="MONTHLY">Mensal</option>
                                        <option value="WEEKLY">Semanal</option>
                                        <option value="YEARLY">Anual</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Proxima execucao</label>
                                    <input type="date" class="form-control" name="recurrence_start">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Numero de ocorrencias</label>
                                    <input type="number" class="form-control" name="recurrence_times" min="1">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Salvar lancamento</button>
                                    <span class="small ms-3" id="transaction-feedback"></span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Compra parcelada no cartao</h5>
                            <form id="card-purchase-form" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Cartao</label>
                                    <select name="card_id" class="form-select" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Categoria</label>
                                    <select name="category_id" class="form-select"></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Valor total</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Data</label>
                                    <input type="date" class="form-control" name="date" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Parcelas</label>
                                    <input type="number" class="form-control" name="installments" min="1" max="60" value="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Descricao</label>
                                    <input type="text" class="form-control" name="description" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-outline-primary">Registrar compra</button>
                                    <span class="small ms-3" id="card-purchase-feedback"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                <section id="panel-categories" class="panel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Categorias</h5>
                                <button class="btn btn-outline-secondary btn-sm" data-action="refresh-categories">Atualizar</button>
                            </div>
                            <form id="category-form" class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label class="form-label">Categoria pai</label>
                                    <select name="parent_id" class="form-select">
                                        <option value="">Nenhuma</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nome</label>
                                    <input name="name" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tipo</label>
                                    <select name="type" class="form-select" required>
                                        <option value="INCOME">Receita</option>
                                        <option value="EXPENSE">Despesa</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Cor</label>
                                    <input name="color" type="color" class="form-control form-control-color" value="#0ea5e9">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                    <span class="small ms-3" id="category-feedback"></span>
                                </div>
                            </form>
                            <div class="row g-3 mt-3" id="category-list"></div>
                        </div>
                    </div>
                </section>

                <section id="panel-accounts" class="panel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Contas bancarias</h5>
                            <form id="account-form" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Nome</label>
                                    <input name="name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo</label>
                                    <select name="type" class="form-select" required>
                                        <option value="CHECKING">Conta corrente</option>
                                        <option value="SAVINGS">Poupanca</option>
                                        <option value="WALLET">Carteira</option>
                                        <option value="INVESTMENT">Investimento</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Moeda</label>
                                    <input name="currency" class="form-control" value="BRL" maxlength="3">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Saldo inicial</label>
                                    <input name="initial_balance" type="number" step="0.01" class="form-control" value="0">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="is_joint" id="isJoint">
                                        <label class="form-check-label" for="isJoint">Conta conjunta</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Salvar conta</button>
                                    <span class="small ms-3" id="account-feedback"></span>
                                </div>
                            </form>
                            <div class="row g-3 mt-3" id="account-list"></div>
                        </div>
                    </div>
                </section>

                <section id="panel-cards" class="panel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Cartoes</h5>
                            <form id="card-form" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Conta vinculada</label>
                                    <select name="account_id" class="form-select" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nome</label>
                                    <input name="name" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Bandeira</label>
                                    <input name="brand" class="form-control" placeholder="VISA">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Limite</label>
                                    <input name="limit_amount" type="number" step="0.01" class="form-control" required>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Fecha</label>
                                    <input name="closing_day" type="number" min="1" max="28" class="form-control" required>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Vence</label>
                                    <input name="due_day" type="number" min="1" max="28" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Salvar cartao</button>
                                    <span class="small ms-3" id="card-feedback"></span>
                                </div>
                            </form>
                            <div class="row g-3 mt-3" id="card-list"></div>
                        </div>
                    </div>
                </section>

                <section id="panel-goals" class="panel">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Metas financeiras</h5>
                            <form id="goal-form" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Categoria</label>
                                    <select name="category_id" class="form-select" required></select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Conta</label>
                                    <select name="account_id" class="form-select">
                                        <option value="">Qualquer conta</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Periodo</label>
                                    <select name="period" class="form-select" required>
                                        <option value="MONTHLY">Mensal</option>
                                        <option value="YEARLY">Anual</option>
                                        <option value="CUSTOM">Personalizado</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Inicio</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fim</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Valor alvo</label>
                                    <input type="number" step="0.01" name="target_amount" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Salvar meta</button>
                                    <span class="small ms-3" id="goal-feedback"></span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Registrar aporte na meta</h5>
                            <form id="goal-contribution-form" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Meta</label>
                                    <select name="goal_id" class="form-select" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Valor</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Data</label>
                                    <input type="date" name="contributed_at" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Anotacao</label>
                                    <input type="text" name="note" class="form-control">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-outline-primary">Guardar valor</button>
                                    <span class="small ms-3" id="goal-contribution-feedback"></span>
                                </div>
                            </form>
                            <div class="row g-3 mt-3" id="goal-list"></div>
                        </div>
                    </div>
                </section>

                <section id="panel-recurrences" class="panel">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recorrencias cadastradas</h5>
                                <button class="btn btn-outline-secondary btn-sm" data-action="refresh-recurrences">Atualizar</button>
                            </div>
                            <p class="text-secondary small mt-2">Crie uma despesa ou receita com recorrencia pelo formulario de lancamento. Gerencie aqui os agendamentos ativos.</p>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="recurrence-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Descricao</th>
                                            <th>Frequencia</th>
                                            <th>Proxima execucao</th>
                                            <th>Restantes</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <span class="small" id="recurrence-feedback"></span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>

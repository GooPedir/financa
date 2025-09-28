import "./bootstrap";

(() => {
const root = document.getElementById("app");
if (!root || root.dataset.page !== "dashboard") {
    return;
}

const state = {
    token: localStorage.getItem("financas_token") || null,
    tenantId: localStorage.getItem("financas_tenant") || null,
    user: null,
    memberId: null,
    categories: [],
    accounts: [],
    cards: [],
    goals: [],
    summary: null,
    recurrences: [],
};

const selectors = {
    navButtons: Array.from(document.querySelectorAll("#dashboard-nav button")),
    panels: Array.from(document.querySelectorAll(".panel")),
    userName: document.querySelector('[data-field="user-name"]'),
    tenantName: document.querySelector('[data-field="tenant-name"]'),
    summaryIncome: document.querySelector('[data-summary="income"]'),
    summaryExpenses: document.querySelector('[data-summary="expenses"]'),
    summaryNet: document.querySelector('[data-summary="net"]'),
    summaryRatio: document.querySelector('[data-summary="income-ratio"]'),
    summaryExpensePercent: document.querySelector('[data-summary="expense-percent"]'),
    breakdownNodes: document.querySelectorAll('[data-breakdown]'),
    latestTransactions: document.querySelector('#latest-transactions tbody'),
    goalProgress: document.getElementById('goal-progress'),
    installmentList: document.getElementById('installment-list'),
    recurrenceSummary: document.getElementById('recurrence-summary'),
    transactionForm: document.getElementById('transaction-form'),
    transactionFeedback: document.getElementById('transaction-feedback'),
    expenseKindField: document.querySelector('.expense-kind-field'),
    cardPurchaseForm: document.getElementById('card-purchase-form'),
    cardPurchaseFeedback: document.getElementById('card-purchase-feedback'),
    categoryForm: document.getElementById('category-form'),
    categoryFeedback: document.getElementById('category-feedback'),
    categoryList: document.getElementById('category-list'),
    accountForm: document.getElementById('account-form'),
    accountFeedback: document.getElementById('account-feedback'),
    accountList: document.getElementById('account-list'),
    cardForm: document.getElementById('card-form'),
    cardFeedback: document.getElementById('card-feedback'),
    cardList: document.getElementById('card-list'),
    goalForm: document.getElementById('goal-form'),
    goalFeedback: document.getElementById('goal-feedback'),
    goalContributionForm: document.getElementById('goal-contribution-form'),
    goalContributionFeedback: document.getElementById('goal-contribution-feedback'),
    goalCards: document.getElementById('goal-list'),
    recurrenceTableBody: document.querySelector('#recurrence-table tbody'),
    recurrenceFeedback: document.getElementById('recurrence-feedback'),
};

function ensureAuth() {
    if (!state.token) {
        window.location.href = "/";
    }
}

function formatCurrency(value, currency = "BRL") {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency }).format(Number(value || 0));
}

function setActivePanel(panelName) {
    selectors.navButtons.forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.panel === panelName);
    });
    selectors.panels.forEach((panel) => {
        panel.classList.toggle('active', panel.id === `panel-${panelName}`);
    });
}

selectors.navButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
        setActivePanel(btn.dataset.panel);
    });
});

function api(path, { method = 'GET', body } = {}) {
    if (!state.token) {
        throw new Error('Sessao expirada');
    }
    const headers = {
        Accept: 'application/json',
        Authorization: `Bearer ${state.token}`,
    };
    if (body) {
        headers['Content-Type'] = 'application/json';
    }
    if (state.tenantId) {
        headers['X-Tenant-ID'] = state.tenantId;
    }
    return fetch(`/api${path}`, {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined,
    }).then(async (response) => {
        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || `Erro ${response.status}`);
        }
        if (response.status === 204) {
            return null;
        }
        return response.json();
    });
}

function populateSelect(select, items, config = {}) {
    if (!select) return;
    const { valueKey = 'id', labelKey = 'name', placeholder } = config;
    const getLabel = typeof labelKey === 'function' ? labelKey : (item) => item[labelKey];
    const prev = select.value;
    select.innerHTML = '';
    if (placeholder !== undefined) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        select.append(opt);
    }
    items.forEach((item) => {
        const opt = document.createElement('option');
        opt.value = item[valueKey];
        opt.textContent = getLabel(item);
        select.append(opt);
    });
    if (prev) {
        select.value = prev;
    }
}

function handleTransactionTypeChange(select) {
    if (!selectors.expenseKindField) return;
    const isExpense = select.value === 'EXPENSE';
    selectors.expenseKindField.classList.toggle('d-none', !isExpense);
    const categorySelect = selectors.transactionForm?.elements['category_id'];
    if (categorySelect) {
        const filtered = state.categories.filter((category) => !category.parent_id && category.type === (isExpense ? 'EXPENSE' : 'INCOME'));
        populateSelect(categorySelect, filtered, { valueKey: 'id', labelKey: 'name', placeholder: 'Selecione' });
    }
}

if (selectors.transactionForm) {
    selectors.transactionForm.elements['type'].addEventListener('change', (event) => {
        handleTransactionTypeChange(event.target);
    });
}

function setToday(input) {
    if (!input) return;
    input.value = new Date().toISOString().slice(0, 10);
}

function renderCategoryCards() {
    if (!selectors.categoryList) return;
    selectors.categoryList.innerHTML = '';
    const roots = state.categories.filter((category) => !category.parent_id);
    roots.forEach((category) => {
        const col = document.createElement('div');
        col.className = 'col-md-4';
        const card = document.createElement('div');
        card.className = 'card h-100 shadow-sm';
        card.innerHTML = `
            <div class="card-body">
                <h6 class="card-title">${category.name}</h6>
                <p class="text-secondary small mb-2">Tipo: ${category.type}</p>
                ${category.children && category.children.length ? `<ul class="small text-secondary ps-3 mb-0">${category.children.map((child) => `<li>${child.name}</li>`).join('')}</ul>` : '<p class="small text-secondary mb-0">Sem subcategorias</p>'}
            </div>
        `;
        col.append(card);
        selectors.categoryList.append(col);
    });
}

function renderAccountCards() {
    if (!selectors.accountList) return;
    selectors.accountList.innerHTML = '';
    state.accounts.forEach((account) => {
        const col = document.createElement('div');
        col.className = 'col-md-4';
        const card = document.createElement('div');
        card.className = 'card h-100 shadow-sm';
        card.innerHTML = `
            <div class="card-body">
                <h6 class="card-title">${account.name}</h6>
                <p class="text-secondary small mb-1">Tipo: ${account.type}</p>
                <p class="text-secondary small mb-0">Saldo inicial: ${formatCurrency(account.initial_balance, account.currency || 'BRL')}</p>
            </div>
        `;
        col.append(card);
        selectors.accountList.append(col);
    });
}

function renderCardCards() {
    if (!selectors.cardList) return;
    selectors.cardList.innerHTML = '';
    state.cards.forEach((card) => {
        const col = document.createElement('div');
        col.className = 'col-md-4';
        const node = document.createElement('div');
        node.className = 'card h-100 shadow-sm';
        node.innerHTML = `
            <div class="card-body">
                <h6 class="card-title">${card.name}</h6>
                <p class="text-secondary small mb-1">Conta: ${card.account?.name || '-'}</p>
                <p class="text-secondary small mb-1">Limite: ${formatCurrency(card.limit_amount, card.account?.currency || 'BRL')}</p>
                <p class="text-secondary small mb-0">Fatura: fecha dia ${card.closing_day} • vence dia ${card.due_day}</p>
            </div>
        `;
        col.append(node);
        selectors.cardList.append(col);
    });
}

function renderGoalCards() {
    if (!selectors.goalCards) return;
    selectors.goalCards.innerHTML = '';
    state.goals.forEach((goal) => {
        const col = document.createElement('div');
        col.className = 'col-md-4';
        const percent = goal.target_amount > 0 ? Math.min(100, Math.round((goal.current_amount / goal.target_amount) * 100)) : 0;
        const card = document.createElement('div');
        card.className = 'card h-100 shadow-sm';
        card.innerHTML = `
            <div class="card-body">
                <h6 class="card-title">${goal.category?.name || 'Meta'}</h6>
                <p class="text-secondary small mb-2">Alvo: ${formatCurrency(goal.target_amount)}</p>
                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${percent}">
                    <div class="progress-bar bg-success" style="width:${percent}%">${percent}%</div>
                </div>
                <p class="text-secondary small mt-2 mb-0">Guardado: ${formatCurrency(goal.current_amount)}</p>
            </div>
        `;
        col.append(card);
        selectors.goalCards.append(col);
    });
}

function renderSummary() {
    if (!state.summary) return;
    const { income, expenses, net, expense_breakdown: breakdown } = state.summary;
    selectors.summaryIncome.textContent = formatCurrency(income);
    selectors.summaryExpenses.textContent = formatCurrency(expenses);
    selectors.summaryNet.textContent = formatCurrency(net);

    const totalFlow = income + expenses;
    const expensePercent = income > 0 ? Math.min(100, Math.round((expenses / income) * 100)) : 0;
    selectors.summaryExpensePercent.textContent = `${expensePercent}%`;
    selectors.summaryRatio.style.width = `${Math.min(100, income > 0 ? (income / (income + expenses || 1)) * 100 : 0)}%`;
    selectors.summaryRatio.textContent = expensePercent > 0 ? `${100 - expensePercent}% livre` : '100%';

    selectors.breakdownNodes.forEach((node) => {
        const key = node.dataset.breakdown;
        node.textContent = formatCurrency(breakdown[key] || 0);
    });

    if (selectors.latestTransactions) {
        selectors.latestTransactions.innerHTML = '';
        state.summary.latest_transactions.forEach((tx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${tx.date}</td>
                <td>${tx.description}</td>
                <td>${tx.account?.name || '-'}</td>
                <td class="text-end ${tx.type === 'INCOME' ? 'text-success' : 'text-danger'}">${formatCurrency(tx.amount)}</td>
            `;
            selectors.latestTransactions.append(tr);
        });
    }

    if (selectors.goalProgress) {
        selectors.goalProgress.innerHTML = '';
        state.summary.goals.forEach((goal) => {
            const item = document.createElement('div');
            item.className = 'list-group-item';
            item.innerHTML = `
                <div>
                    <strong>${goal.label}</strong>
                    <div class="progress mt-1" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${goal.percent}">
                        <div class="progress-bar bg-success" style="width:${Math.min(100, goal.percent)}%"></div>
                    </div>
                </div>
                <span class="badge text-bg-light">${formatCurrency(goal.current)} / ${formatCurrency(goal.target)}</span>
            `;
            selectors.goalProgress.append(item);
        });
    }

    if (selectors.installmentList) {
        selectors.installmentList.innerHTML = '';
        state.summary.installments.forEach((installment) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span>${installment.card || 'Cartao'} • Parcela ${installment.installment}/${installment.total}</span>
                <strong>${formatCurrency(installment.amount)}</strong>
            `;
            selectors.installmentList.append(li);
        });
    }

    if (selectors.recurrenceSummary) {
        selectors.recurrenceSummary.innerHTML = '';
        state.summary.recurrences.forEach((rec) => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.innerHTML = `
                <span>${rec.base_transaction.description || 'Lancamento'}</span>
                <small class="text-secondary">${rec.frequency} • proximo: ${rec.next_run_at}</small>
            `;
            selectors.recurrenceSummary.append(li);
        });
    }
}

function renderRecurrenceTable() {
    if (!selectors.recurrenceTableBody) return;
    selectors.recurrenceTableBody.innerHTML = '';
    state.recurrences.forEach((rec) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rec.base_transaction?.description || '-'}</td>
            <td>${rec.frequency}</td>
            <td>${rec.next_run_at}</td>
            <td>${rec.occurrences_left ?? '-'}</td>
            <td class="text-end"><button class="btn btn-sm btn-outline-danger" data-action="delete-recurrence" data-id="${rec.id}">remover</button></td>
        `;
        selectors.recurrenceTableBody.append(tr);
    });
}

async function loadSummary() {
    const data = await api('/dashboard/summary');
    state.summary = data;
    renderSummary();
}

async function refreshCategories() {
    const data = await api('/categories');
    state.categories = Array.isArray(data) ? data : data.value || [];
    renderCategoryCards();

    const parentSelect = selectors.categoryForm?.elements['parent_id'];
    populateSelect(parentSelect, state.categories.filter((category) => !category.parent_id), { valueKey: 'id', labelKey: 'name', placeholder: 'Nenhuma' });

    const transactionCategory = selectors.transactionForm?.elements['category_id'];
    if (transactionCategory) {
        handleTransactionTypeChange(selectors.transactionForm.elements['type']);
    }

    const goalCategory = selectors.goalForm?.elements['category_id'];
    populateSelect(goalCategory, state.categories.filter((category) => !category.parent_id), { valueKey: 'id', labelKey: 'name', placeholder: 'Selecione' });

    const cardCategory = selectors.cardPurchaseForm?.elements['category_id'];
    populateSelect(cardCategory, state.categories.filter((category) => category.type === 'EXPENSE'), { valueKey: 'id', labelKey: 'name', placeholder: 'Opcional' });
}

async function refreshAccounts() {
    const data = await api('/accounts');
    state.accounts = Array.isArray(data) ? data : data.value || [];
    renderAccountCards();

    const accountSelects = [
        selectors.transactionForm?.elements['account_id'],
        selectors.cardForm?.elements['account_id'],
        selectors.goalForm?.elements['account_id'],
    ];
    accountSelects.forEach((select, index) => {
        if (!select) return;
        const placeholder = index === 2 ? 'Qualquer conta' : 'Selecione';
        populateSelect(select, state.accounts, { valueKey: 'id', labelKey: 'name', placeholder });
    });
}

async function refreshCards() {
    const data = await api('/cards');
    state.cards = Array.isArray(data) ? data : data.value || [];
    renderCardCards();

    const select = selectors.cardPurchaseForm?.elements['card_id'];
    populateSelect(select, state.cards, { valueKey: 'id', labelKey: 'name', placeholder: 'Selecione' });
}

async function refreshGoals() {
    const data = await api('/goals');
    state.goals = Array.isArray(data) ? data : data.value || [];
    renderGoalCards();

    const select = selectors.goalContributionForm?.elements['goal_id'];
    populateSelect(select, state.goals, { valueKey: 'id', labelKey: (goal) => goal.category?.name || `Meta ${goal.id}`, placeholder: 'Selecione' });
}

async function refreshRecurrences() {
    const data = await api('/recurrences');
    state.recurrences = Array.isArray(data) ? data : data.data || [];
    renderRecurrenceTable();
}

async function handleTransactionSubmit(event) {
    event.preventDefault();
    selectors.transactionFeedback.textContent = '';
    const form = event.target;
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());
    payload.account_id = Number(payload.account_id);
    payload.member_id = state.memberId;
    payload.amount = Number(payload.amount);
    payload.date = payload.date || new Date().toISOString().slice(0, 10);
    if (!payload.category_id) delete payload.category_id;
    if (!payload.expense_kind || payload.type !== 'EXPENSE') {
        delete payload.expense_kind;
    }
    const recurrenceFrequency = payload.recurrence_frequency;
    const recurrenceStart = payload.recurrence_start;
    const recurrenceTimes = payload.recurrence_times;
    delete payload.recurrence_frequency;
    delete payload.recurrence_start;
    delete payload.recurrence_times;

    try {
        const transaction = await api('/transactions', { method: 'POST', body: payload });
        if (recurrenceFrequency) {
            await api('/recurrences', {
                method: 'POST',
                body: {
                    base_transaction_id: transaction.id,
                    frequency: recurrenceFrequency,
                    next_run_at: recurrenceStart || payload.date,
                    occurrences_left: recurrenceTimes ? Number(recurrenceTimes) : null,
                },
            });
        }
        selectors.transactionFeedback.textContent = 'Lancamento salvo com sucesso.';
        selectors.transactionFeedback.className = 'text-success small';
        form.reset();
        setToday(form.elements['date']);
        handleTransactionTypeChange(form.elements['type']);
        await Promise.all([loadSummary(), refreshRecurrences(), refreshGoals()]);
    } catch (error) {
        selectors.transactionFeedback.textContent = error.message;
        selectors.transactionFeedback.className = 'text-danger small';
    }
}

async function handleCardPurchaseSubmit(event) {
    event.preventDefault();
    selectors.cardPurchaseFeedback.textContent = '';
    const form = event.target;
    const payload = Object.fromEntries(new FormData(form).entries());
    const cardId = payload.card_id;
    payload.amount = Number(payload.amount);
    payload.installments = Number(payload.installments || 1);
    if (!payload.category_id) delete payload.category_id;
    try {
        await api(`/cards/${cardId}/purchase`, { method: 'POST', body: payload });
        selectors.cardPurchaseFeedback.textContent = 'Compra registrada.';
        selectors.cardPurchaseFeedback.className = 'text-success small';
        form.reset();
        await Promise.all([loadSummary(), refreshCards()]);
    } catch (error) {
        selectors.cardPurchaseFeedback.textContent = error.message;
        selectors.cardPurchaseFeedback.className = 'text-danger small';
    }
}

async function handleCategorySubmit(event) {
    event.preventDefault();
    selectors.categoryFeedback.textContent = '';
    const payload = Object.fromEntries(new FormData(event.target).entries());
    if (!payload.parent_id) delete payload.parent_id;
    try {
        await api('/categories', { method: 'POST', body: payload });
        selectors.categoryFeedback.textContent = 'Categoria salva.';
        selectors.categoryFeedback.className = 'text-success small';
        event.target.reset();
        await refreshCategories();
    } catch (error) {
        selectors.categoryFeedback.textContent = error.message;
        selectors.categoryFeedback.className = 'text-danger small';
    }
}

async function handleAccountSubmit(event) {
    event.preventDefault();
    selectors.accountFeedback.textContent = '';
    const formData = new FormData(event.target);
    const payload = Object.fromEntries(formData.entries());
    payload.initial_balance = Number(payload.initial_balance || 0);
    payload.is_joint = formData.has('is_joint');
    try {
        await api('/accounts', { method: 'POST', body: payload });
        selectors.accountFeedback.textContent = 'Conta salva.';
        selectors.accountFeedback.className = 'text-success small';
        event.target.reset();
        await Promise.all([refreshAccounts(), loadSummary()]);
    } catch (error) {
        selectors.accountFeedback.textContent = error.message;
        selectors.accountFeedback.className = 'text-danger small';
    }
}

async function handleCardSubmit(event) {
    event.preventDefault();
    selectors.cardFeedback.textContent = '';
    const payload = Object.fromEntries(new FormData(event.target).entries());
    payload.account_id = Number(payload.account_id);
    payload.limit_amount = Number(payload.limit_amount);
    payload.closing_day = Number(payload.closing_day);
    payload.due_day = Number(payload.due_day);
    try {
        await api('/cards', { method: 'POST', body: payload });
        selectors.cardFeedback.textContent = 'Cartao salvo.';
        selectors.cardFeedback.className = 'text-success small';
        event.target.reset();
        await refreshCards();
    } catch (error) {
        selectors.cardFeedback.textContent = error.message;
        selectors.cardFeedback.className = 'text-danger small';
    }
}

async function handleGoalSubmit(event) {
    event.preventDefault();
    selectors.goalFeedback.textContent = '';
    const payload = Object.fromEntries(new FormData(event.target).entries());
    if (!payload.account_id) delete payload.account_id;
    try {
        await api('/goals', { method: 'POST', body: payload });
        selectors.goalFeedback.textContent = 'Meta salva.';
        selectors.goalFeedback.className = 'text-success small';
        event.target.reset();
        await Promise.all([refreshGoals(), loadSummary()]);
    } catch (error) {
        selectors.goalFeedback.textContent = error.message;
        selectors.goalFeedback.className = 'text-danger small';
    }
}

async function handleGoalContributionSubmit(event) {
    event.preventDefault();
    selectors.goalContributionFeedback.textContent = '';
    const payload = Object.fromEntries(new FormData(event.target).entries());
    const goalId = payload.goal_id;
    payload.amount = Number(payload.amount);
    if (!payload.contributed_at) delete payload.contributed_at;
    if (!payload.note) delete payload.note;
    try {
        await api(`/goals/${goalId}/contribute`, { method: 'POST', body: payload });
        selectors.goalContributionFeedback.textContent = 'Valor guardado na meta.';
        selectors.goalContributionFeedback.className = 'text-success small';
        event.target.reset();
        await Promise.all([refreshGoals(), loadSummary()]);
    } catch (error) {
        selectors.goalContributionFeedback.textContent = error.message;
        selectors.goalContributionFeedback.className = 'text-danger small';
    }
}

async function handleRecurrenceAction(event) {
    const button = event.target.closest('[data-action="delete-recurrence"]');
    if (!button) return;
    const id = button.dataset.id;
    try {
        await api(`/recurrences/${id}`, { method: 'DELETE' });
        selectors.recurrenceFeedback.textContent = 'Recorrencia removida.';
        selectors.recurrenceFeedback.className = 'text-success small';
        await Promise.all([refreshRecurrences(), loadSummary()]);
    } catch (error) {
        selectors.recurrenceFeedback.textContent = error.message;
        selectors.recurrenceFeedback.className = 'text-danger small';
    }
}

async function bootstrapSession() {
    const data = await api('/tenants/me');
    state.user = data.members?.[0]?.user || null;
    state.memberId = data.members?.[0]?.id || null;
    if (!state.memberId) {
        throw new Error('Usuario sem membro ativo');
    }
    const tenantId = data.tenant?.id?.toString();
    if (tenantId) {
        state.tenantId = tenantId;
        localStorage.setItem('financas_tenant', tenantId);
    }
    if (selectors.userName) selectors.userName.textContent = state.user?.name || '-';
    if (selectors.tenantName) selectors.tenantName.textContent = data.tenant?.name || '-';
}

async function init() {
    ensureAuth();
    try {
        await bootstrapSession();
        await Promise.all([
            refreshCategories(),
            refreshAccounts(),
            refreshCards(),
            refreshGoals(),
        ]);
        await Promise.all([
            loadSummary(),
            refreshRecurrences(),
        ]);
        setToday(selectors.transactionForm?.elements['date']);
        setToday(selectors.cardPurchaseForm?.elements['date']);
        setToday(selectors.transactionForm?.elements['recurrence_start']);
        setActivePanel('dashboard');
    } catch (error) {
        localStorage.removeItem('financas_token');
        localStorage.removeItem('financas_tenant');
        window.location.href = '/';
    }
}

if (selectors.transactionForm) {
    selectors.transactionForm.addEventListener('submit', handleTransactionSubmit);
}
if (selectors.cardPurchaseForm) {
    selectors.cardPurchaseForm.addEventListener('submit', handleCardPurchaseSubmit);
}
if (selectors.categoryForm) {
    selectors.categoryForm.addEventListener('submit', handleCategorySubmit);
}
if (selectors.accountForm) {
    selectors.accountForm.addEventListener('submit', handleAccountSubmit);
}
if (selectors.cardForm) {
    selectors.cardForm.addEventListener('submit', handleCardSubmit);
}
if (selectors.goalForm) {
    selectors.goalForm.addEventListener('submit', handleGoalSubmit);
}
if (selectors.goalContributionForm) {
    selectors.goalContributionForm.addEventListener('submit', handleGoalContributionSubmit);
}
if (selectors.recurrenceTableBody) {
    selectors.recurrenceTableBody.addEventListener('click', handleRecurrenceAction);
}

document.addEventListener('click', (event) => {
    const refresh = event.target.dataset?.action;
    if (refresh === 'refresh-summary') {
        loadSummary();
    }
    if (refresh === 'refresh-categories') {
        refreshCategories();
    }
    if (refresh === 'refresh-accounts') {
        refreshAccounts();
    }
    if (refresh === 'refresh-cards') {
        refreshCards();
    }
    if (refresh === 'refresh-goals') {
        refreshGoals();
    }
    if (refresh === 'refresh-recurrences') {
        refreshRecurrences();
    }
});

init();
})();







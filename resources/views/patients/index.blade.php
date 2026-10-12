<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes | ConectaSUS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

    <main class="container py-5">

        <!-- Cabeçalho -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Pacientes cadastrados</h1>
                <p class="text-secondary mb-0">
                    Consulte os pacientes registrados no ConectaSUS.
                </p>
            </div>

            <a href="/patients/create" class="btn btn-primary">
                Cadastrar paciente
            </a>
        </div>

        <!-- Pesquisa -->
        <form id="search-form" class="mb-4">
            <div class="row g-2">
                <div class="col-md-8">
                    <label for="search" class="form-label">
                        Pesquisar paciente
                    </label>

                    <input
                        type="text"
                        id="search"
                        class="form-control"
                        placeholder="Digite o nome ou CPF">
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        Pesquisar
                    </button>

                    <button
                        type="button"
                        id="clear-search"
                        class="btn btn-outline-secondary">
                        Limpar
                    </button>
                </div>
            </div>
        </form>

        <!-- Mensagens -->
        <div id="list-message" class="mb-3" role="alert"></div>

        <!-- Tabela -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Nascimento</th>
                            <th>Telefone</th>
                            <th>Unidade de saúde</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody id="patients-table">
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                Carregando pacientes...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginação -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <button
                id="previous-page"
                class="btn btn-outline-primary"
                disabled>
                Anterior
            </button>

            <span id="page-info" class="text-secondary">
                Página 1 de 1
            </span>

            <button
                id="next-page"
                class="btn btn-outline-primary"
                disabled>
                Próxima
            </button>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const table = document.getElementById('patients-table');
            const message = document.getElementById('list-message');
            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search');
            const clearButton = document.getElementById('clear-search');
            const previousButton = document.getElementById('previous-page');
            const nextButton = document.getElementById('next-page');
            const pageInfo = document.getElementById('page-info');

            let currentPage = 1;
            let searchTerm = '';

            // Busca pacientes usando o filtro e a página escolhida.
            async function loadPatients(page = 1) {
                message.textContent = '';
                message.className = 'mb-3';

                table.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            Carregando pacientes...
                        </td>
                    </tr>
                `;

                previousButton.disabled = true;
                nextButton.disabled = true;

                try {
                    const params = new URLSearchParams();

                    params.set('page', page);

                    if (searchTerm) {
                        params.set('search', searchTerm);
                    }

                    const response = await fetch(
                        `/api/patients?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Não foi possível carregar os pacientes.'
                        );
                    }

                    const result = await response.json();

                    table.innerHTML = '';

                    if (result.data.length === 0) {
                        table.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    Nenhum paciente encontrado.
                                </td>
                            </tr>
                        `;
                    }

                    result.data.forEach((patient) => {
                        const row = document.createElement('tr');

                        const values = [
                            patient.name,
                            patient.cpf,
                            patient.birth_date,
                            patient.phone || 'Não informado',
                            patient.health_unit ?
                            `${patient.health_unit.name} - ${patient.health_unit.city}` :
                            'Unidade não informada'
                        ];

                        values.forEach((value) => {
                            const cell = document.createElement('td');
                            cell.textContent = value;
                            row.appendChild(cell);
                        });

                        const actionsCell = document.createElement('td');
                        actionsCell.className = 'text-center';



                        const editButton = document.createElement('a');
                        editButton.href = `/patients/${patient.id}/edit`;
                        editButton.className = 'btn btn-sm btn-outline-warning me-2';
                        editButton.textContent = 'Editar';

                        const deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.className = 'btn btn-sm btn-outline-danger';
                        deleteButton.textContent = 'Excluir';

                        deleteButton.addEventListener('click', async () => {
                            const confirmed = confirm(
                                `Tem certeza de que deseja excluir o paciente ${patient.name}?`
                            );

                            if (!confirmed) {
                                return;
                            }

                            deleteButton.disabled = true;

                            try {
                                const response = await fetch(`/api/patients/${patient.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });

                                const result = await response.json();

                                if (!response.ok) {
                                    throw new Error(
                                        result.message || 'Não foi possível excluir o paciente.'
                                    );
                                }

                                // Atualiza a tabela após a exclusão.
                                await loadPatients(currentPage);

                                message.textContent = 'Paciente excluído com sucesso.';
                                message.className = 'alert alert-success';

                            } catch (error) {
                                console.error(error);

                                message.textContent = error.message;
                                message.className = 'alert alert-danger';

                            } finally {
                                deleteButton.disabled = false;
                            }
                        });

                        actionsCell.appendChild(editButton);
                        actionsCell.appendChild(deleteButton);
                        row.appendChild(actionsCell);


                        table.appendChild(row);
                    });

                    currentPage = result.meta.current_page;

                    pageInfo.textContent =
                        `Página ${result.meta.current_page} de ${result.meta.last_page}`;

                    previousButton.disabled = !result.links.prev;
                    nextButton.disabled = !result.links.next;

                } catch (error) {
                    console.error(error);

                    message.textContent = error.message;
                    message.className = 'alert alert-danger';

                    table.innerHTML = '';
                    pageInfo.textContent = 'Erro ao carregar páginas.';
                    previousButton.disabled = true;
                    nextButton.disabled = true;
                }
            }

            // Executa a pesquisa e volta à primeira página.
            searchForm.addEventListener('submit', (event) => {
                event.preventDefault();

                searchTerm = searchInput.value.trim();
                loadPatients(1);
            });

            // Limpa o filtro e exibe todos os pacientes.
            clearButton.addEventListener('click', () => {
                searchInput.value = '';
                searchTerm = '';
                loadPatients(1);
            });

            // Navega para a página anterior.
            previousButton.addEventListener('click', () => {
                if (currentPage > 1) {
                    loadPatients(currentPage - 1);
                }
            });

            // Navega para a próxima página.
            nextButton.addEventListener('click', () => {
                loadPatients(currentPage + 1);
            });

            // Carrega a listagem inicial.
            loadPatients();
        });
    </script>

</body>

</html>
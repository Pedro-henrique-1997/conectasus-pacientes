
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar paciente | ConectaSUS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h1 class="h3 mb-2">Editar paciente</h1>

                        <p class="text-secondary mb-4">
                            Altere os dados do paciente e salve as modificações.
                        </p>

                        <div id="form-message" class="mb-3" role="alert"></div>

                        <form id="patient-form">

                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Nome completo
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="cpf" class="form-label">CPF</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="cpf"
                                    name="cpf"
                                    inputmode="numeric"
                                    maxlength="11"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="birth_date" class="form-label">
                                    Data de nascimento
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="birth_date"
                                    name="birth_date"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    Telefone (opcional)
                                </label>

                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="health_unit_id" class="form-label">
                                    Unidade de saúde
                                </label>

                                <select
                                    class="form-select"
                                    id="health_unit_id"
                                    name="health_unit_id"
                                    required
                                >
                                    <option value="">
                                        Carregando unidades...
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button
                                    type="submit"
                                    id="save-button"
                                    class="btn btn-primary"
                                    disabled
                                >
                                    Salvar alterações
                                </button>

                                <a href="/patients" class="btn btn-outline-secondary">
                                    Voltar
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const form = document.getElementById('patient-form');
            const message = document.getElementById('form-message');
            const select = document.getElementById('health_unit_id');
            const saveButton = document.getElementById('save-button');

            // Obtém o ID do paciente pela URL da página.
            const pathParts = window.location.pathname.split('/');
            const patientId = pathParts[pathParts.length - 2];

            function showMessage(text, type) {
                message.textContent = text;
                message.className = `alert alert-${type}`;
            }

            try {
                // Carrega as unidades de saúde.
                const unitsResponse = await fetch('/api/health-units', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!unitsResponse.ok) {
                    throw new Error('Não foi possível carregar as unidades.');
                }

                const unitsResult = await unitsResponse.json();

                unitsResult.data.forEach((unit) => {
                    const option = document.createElement('option');

                    option.value = unit.id;
                    option.textContent = `${unit.name} - ${unit.city}`;

                    select.appendChild(option);
                });

                // Busca os dados atuais do paciente.
                const patientResponse = await fetch(
                    `/api/patients/${patientId}`,
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!patientResponse.ok) {
                    throw new Error('Não foi possível encontrar o paciente.');
                }

                const patientResult = await patientResponse.json();
                const patient = patientResult.data ?? patientResult;

                // Preenche os campos com os dados existentes.
                form.elements.name.value = patient.name;
                form.elements.cpf.value = patient.cpf;
                form.elements.birth_date.value = patient.birth_date;
                form.elements.phone.value = patient.phone ?? '';
                select.value = patient.health_unit_id;

                saveButton.disabled = false;

            } catch (error) {
                console.error(error);
                showMessage(error.message, 'danger');
            }

            // Envia as alterações para a API.
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                message.textContent = '';
                message.className = 'mb-3';
                saveButton.disabled = true;

                const formData = new FormData(form);
                const patient = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch(
                        `/api/patients/${patientId}`,
                        {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(patient)
                        }
                    );

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            const errors = Object.values(result.errors).flat();
                            throw new Error(errors.join(' '));
                        }

                        throw new Error(
                            result.message || 'Não foi possível atualizar o paciente.'
                        );
                    }

                    showMessage('Paciente atualizado com sucesso!', 'success');

                } catch (error) {
                    showMessage(error.message, 'danger');
                } finally {
                    saveButton.disabled = false;
                }
            });
        });
    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar paciente | ConectaSUS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 mb-2">Cadastrar paciente</h1>
                        <p class="text-secondary mb-4">
                            Preencha os dados do paciente.
                        </p>

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
                                    required>
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
                                    placeholder="Somente números"
                                    required>
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
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    Telefone (opcional)
                                </label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    name="phone">
                            </div>

                            <div class="mb-3">
                                <label for="health_unit_id" class="form-label">
                                    Unidade de saúde
                                </label>
                                <select
                                    class="form-select"
                                    id="health_unit_id"
                                    name="health_unit_id"
                                    required>
                                    <option value="">Selecione uma unidade</option>
                                </select>
                            </div>

                            <div id="form-message" class="mb-3"
                                role="alert" aria-live="polite"></div>

                            
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    Cadastrar paciente
                                </button>

                                <a href="/patients" class="btn btn-outline-secondary flex-grow-1">
                                    Voltar para pacientes
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
            const select = document.getElementById('health_unit_id');
            const form = document.getElementById('patient-form');
            const message = document.getElementById('form-message');

            // Carrega as unidades de saúde.
            try {
                const response = await fetch('/api/health-units');

                if (!response.ok) {
                    throw new Error('Não foi possível carregar as unidades.');
                }

                const result = await response.json();

                result.data.forEach((unit) => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = `${unit.name} - ${unit.city}`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error(error);
                select.innerHTML =
                    '<option value="">Erro ao carregar unidades</option>';
            }

            // Envia o formulário para a API.
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                message.textContent = '';
                message.className = 'mb-3';

                const formData = new FormData(form);
                const patient = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch('/api/patients', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(patient),
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            const errors = Object.values(result.errors).flat();
                            throw new Error(errors.join(' '));
                        }

                        throw new Error(
                            result.message || 'Não foi possível cadastrar o paciente.'
                        );
                    }

                    message.textContent = 'Paciente cadastrado com sucesso!';
                    message.classList.add('alert', 'alert-success');

                    form.reset();
                } catch (error) {
                    message.textContent = error.message;
                    message.classList.add('alert', 'alert-danger');
                }
            });
        });
    </script>
</body>

</html>

</body>


</html>
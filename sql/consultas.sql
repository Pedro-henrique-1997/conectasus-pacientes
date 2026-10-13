
-- 1. Listar pacientes com suas respectivas unidades de saúde.
SELECT
    p.id,
    p.name AS paciente,
    p.cpf,
    p.birth_date,
    p.phone,
    h.name AS unidade_de_saude,
    h.city AS cidade
FROM patients AS p
INNER JOIN health_units AS h
    ON p.health_unit_id = h.id
ORDER BY p.name;


-- 2. Contar quantos pacientes estão vinculados a cada unidade,
-- incluindo unidades que ainda não possuem pacientes.
SELECT
    h.id,
    h.name AS unidade_de_saude,
    h.city AS cidade,
    COUNT(p.id) AS total_pacientes
FROM health_units AS h
LEFT JOIN patients AS p
    ON p.health_unit_id = h.id
GROUP BY h.id, h.name, h.city
ORDER BY h.name;


-- 3. Pesquisar pacientes pelo nome, utilizando parte do nome.
-- Altere o texto entre os sinais de porcentagem para pesquisar
-- outro nome.
SELECT
    p.id,
    p.name AS paciente,
    p.cpf,
    p.birth_date,
    p.phone,
    h.name AS unidade_de_saude
FROM patients AS p
INNER JOIN health_units AS h
    ON p.health_unit_id = h.id
WHERE p.name LIKE '%Maria%'
ORDER BY p.name;

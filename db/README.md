# Gimnasio DB

Base de datos PostgreSQL con Liquibase y Docker.

- Entidades primarias: `entrenadores`, `planes`.
- Entidades secundarias: `miembros`, `clases`, `reservas`.

## Levantar solo DB

```bash
docker compose -f docker/docker-compose.db.yml up --build
```

## Correr Liquibase manualmente

```bash
docker compose -f docker/docker-compose.db.yml run --rm liquibase update
```

## Validar Liquibase

```bash
docker compose -f docker/docker-compose.db.yml run --rm liquibase validate
```

## Entrar a PostgreSQL

```bash
docker exec -it gimnasio_postgres psql -U gimnasio_user -d gimnasio_db
```

Ejemplos:

```sql
SELECT * FROM entrenadores;
SELECT * FROM planes;
SELECT * FROM miembros;
```

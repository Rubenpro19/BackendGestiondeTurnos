Para correr el proyecto de manera local puede ser usando la base de datos en líne agregando la siguiente configuración
dentro del .env de ejemplo en el apartado de la base de datos

DB_CONNECTION=pgsql
DB_URL=postgresql://test_postgres_rtbf_user:omQMEQuKncMyPvaE2Oyr5SWpp7fq9n6v@dpg-ct5rot3qf0us7388lfb0-a.oregon-postgres.render.com/test_postgres_rtbf


O puede simplemente usar la estructura clásica:
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=proyectowebii (Poner una base de datos existente en postgres)
DB_USERNAME=postgres    
DB_PASSWORD= (Aqui agrega tu contraseña del usuario postgres)

Ejecutar las migraciones con php artisan migrate
Ejecutar los seeders con php artisan db:seed
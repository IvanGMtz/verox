CREAR CRUD: (Todos los comandos se ejecutan desde bin)
1. Mapear la tabla de la BD:
    php console doctrine:mapping:import AppBundle yml --filter="Producto"
2. Crear la entidad:
    php console doctrine:generate:entities AppBundle --no-backup
3. Generar el Crud:
    php console doctrine:generate:crud

4. borrar chache en produccion 
bin % php -d memory_limit=500M console cache:clear --env=dev

# Switch from 8.1 to 7.4
brew unlink php@8.1
brew link --overwrite php@7.4
brew link php@7.4 --force

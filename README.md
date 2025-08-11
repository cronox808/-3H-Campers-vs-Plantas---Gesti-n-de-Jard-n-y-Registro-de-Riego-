# My Garden

## Descripción
Este es un proyecto PHP que implementa una arquitectura limpia (Clean Architecture), organizando el código en capas bien definidas para mantener una separación clara de responsabilidades.

## Estructura del Proyecto

```bash

├── app/ 
├── src/ 
│ ├── Application/ # Casos de uso de la aplicación 
│ ├── Domain/ # Entidades y reglas de negocio 
│ └── Infrastructure/ # Implementaciones técnicas y adaptadores 
├── public/ # Punto de entrada de la aplicación 
├── tests/ # Tests automatizados
├── db/ # Scripts y migraciones de base de datos 
└── logs/ # Logs de la aplicación

```
## Requisitos Previos
- PHP 8.2 o superior
- Composer

## Dependencias Principales
- monolog/monolog (v2.10.0) - Para logging
- illuminate/database (v12.21.0) - ORM para base de datos
- php-di/php-di (v7.0.11) - Contenedor de inyección de dependencias
- phpunit - Para testing

## Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/addsdev-campuslands/my-garden-php-dev-container
```
2. Instalar dependencias:
```bash
composer install
```
3. Configurar el ambiente:
```bash
cp .env.example .env
```
4. Configurar las variables de entorno en el archivo `.env`
5. Ejecutar el servidor de pruebas:
```bash
composer start
```

## Desarrollo
Este proyecto sigue los principios de la Arquitectura Limpia con tres capas principales:

- **Domain**: Contiene las entidades del negocio y las reglas de negocio.
- **Application**: Contiene los casos de uso de la aplicación.
- **Infrastructure**: Contiene las implementaciones concretas y adaptadores.



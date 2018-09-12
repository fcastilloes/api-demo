Demostración de aplicación de api con Symfony.

## Instalación

Para instalar el proyecto lo primero que hay que hacer es clonarlo utilizando git.

Una vez descargado el repositorio, y desde la carpeta del proyecto, hay que instalar las dependencias utilizando composer:

```
composer install
```

Con las dependencias ya instaladas, se puede levantar un servidor http mediante la console de symfony con:

```
bin/console server:start
```

Este servidor se puede parar con:

```
bin/console server:stop
```

## Api

Podemos visitar la página `/api/doc` para ver la documentación de los endpoints disponibles.

También podemos ver una documentación en formato json en `/api/doc.json`.

Para poder ver y guardar webhooks es necesario también levantar un servidor **Redis**.

### Autenticación

Para poder utilizar los endpoints hay que generar un token de github. Para eso acceder a la sección **Settings > Developer Settings** y allíse puede generar un **Personal access token** que añadir como cabecera *Authorization* a cada consulta al API.

Si además creamos una **OAuth App** y configuramos el `GITHUB_CLIENT_ID` y el `GITHUB_CLIENT_SECRET` en el fichero `.env`, podremos recibir un código del proceso de OAuth de github para convertirlo en un token. De obtener el código se encarga el cliente.
#Instalación del Repositorio
-Este proyecto web actualmente funciona con el servidor web local de Xampp, por ello es que se debe pullear el repositorio a una dirección semejante a la siguiente:
Este equipo > el disco/partición donde se encuentre instalado el Xampp > htdocs

Por ejemplo: "C:\xampp\htdocs" seria un ejemplo de la dirección para instalarlo
en un equipo de Windows.

#Instalación de la BD
-Para la BD se maneja el motor web de MySQL para red localhost que viene incluido dentro de Xampp, se puede importar por Consola, o usando la aplicación Web "phpMyAdmin"

En caso de utilizar la aplicación Web existen 2 modos para instalarla, en la página de inicio (accesible desde el icono de una casa en la parte superior de la SideBar) nos encontrariamos sin seleccionar ninguna BD de las enlistadas, tras esto ubicamos la Nav-Bar (barra de menu de navegación) y en dicha barra se encuentran los botones de los navigation-item "SQL" e "Importar" como opciones para importar la BD utilizando la App Web.

Para Instalar la BD por script de query (SQL) se necesita copiar el contenido desde el archivo .sql de la Base de Datos correspondiente, cerciosandose de haber incluido las siguientes sentencias en el Script (en caso de no haber sido incluidos al exportar el archivo):

CREATE DATABASE IF NOT EXISTS ``; 
USE ``;

...create database puede añadirsele "DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci" dentros de la misma linea tras los ``.

Y al interior de los `` se encuentre el nombre exacto (sin incluir la extesión .sql) manejado en la BD, "biblioteca_konoha" seria el nombre de la BD manejada

#Recomendaciones adicionales
-dentro de la carpeta config se encuentra el archivo "Connection.php", en este se deben de reemplazar los valores actuales por los correspondientes para el computador del Team Member, por ejemplo, si el dev tiene un puerto diferente en Connection.php al mostrado en la aplicación "Xampp Control Panel" entonces debe modificar la linea que dice "private $port =  '';" para permitir la correcta funcionalidad de este archivo


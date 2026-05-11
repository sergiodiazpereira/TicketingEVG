---
trigger: always_on
---

Yo soy Joseph Joel Quispe Alvarez, para que lo pongas en las cabeceras ese es mi nombre.

Normas Generales
Todos los ficheros de código incluirán una cabecera comentada con la identificación completa del alumno, asignatura, curso y descripción del fichero.
Se evitará mezclar distintos lenguajes en un mismos fichero. Por ejemplo, se evitará incluir código CSS o JavaScript en páginas HTML y también se separará el código PHP del HTML y del SQL.
Los nombres utilizados para ficheros, variables, clases, etc, serán en castellano y descriptivos evitando el uso de acentos, ñ, diéresis y espacios. En general, se utilizarán caracteres de ASCII básico. Se admite el uso de abreviaturas habituales como num, cont, etc.
Todo el código estará adecuadamente comentado. Se buscará la legibilidad y claridad del código para reducir la necesidad de comentarios.
Todo el código estará indentado. Se utilizará tabuladores (TAB) a 8 espacios, omitiendo las llaves opcionales y líneas en blanco (identación K&R en su versión 1TBS.
Por ejemplo:

int main(int argc, char *argv[]){	//Llave en la misma línea
    		...
    		while (x == y) {
        			hacerAlgo();
        			hacerOtraCosa();

        			if (hayError)	//Sin llaves innecesarias
            			corregir();
        			else
            			continuar);
    		}
    		finalizar();
    		…
}	//Lave al mismo nivel de indentación que su función

Normas para Ficheros
La cabecera de cada fichero tendrá información para identificar el proyecto y el autor.
Los nombres de ficheros se escribirán en minúsculas.
Los ficheros se codificarán en UTF-8.
Normas para SQL
Las palabras reservadas de SQL se escribirán en mayúsculas (SELECT, FROM, CREATE TABLE…).
Los nombres de las tablas tendrán la primera letra mayúscula y serán en singular. Si el nombre se formará por varias palabras se utilizará notación C (con subrayado bajo). Por ejemplo Usuario o Usuario_Acceso.
Los nombres de los campos serán en minúsculas. Si el nombre se formara por varias palabras se utilizará notación C.
Las claves sintéticas de las tablas tendrán el nombre “id”.
Las claves externas de las tablas que hagan referencia a claves sintéticas se formarán como id_Nombre_Tabla.
Las tablas de relación se llamarán Tabla1_Tabla2, ordenando las entidades alfabéticamente: Ejemplo: Alumno_Curso
Se utilizarán restricciones de claves externas para asegurar la integridad referencial.
Normas para HTML
Las marcas de html se escribirán en minúsculas.
El código cumplirá las reglas de validación de XML (etiquetas correctamentamente cerradas).
Las etiquetas que requieran varias líneas se iniciarán y terminarán con la etiqueta sola en la línea al mismo nivel de tabulación. Por ejemplo:
<p>
Texto muy largo…
</p>
Los ficheros de estilo se cargarán en la cabecera del documento (<head>).
Los ficheros de JavaScript se cargarán en al final del cuerpo del documento (antes de </body>).

Normas para CSS
Sin especificar


Normas para JavaScript
Se utilizará notación camelCase.
Las clases se nombrarán con la primera letra mayúscula. Por ejemplo: Usuario, UsuarioAdministracion.
Las variables y objetos se nombrarán con minúsculas. Por ejemplo: contador, fechaApertura.
Las constantes se escribirán íntegramente en mayúsculas y en notación C. Por ejemplo: FICHERO, FICHERO_USUARIOS

Normas para PHP
Se utilizará notación C (con subrayado bajo).
Las clases se nombrarán con la primera letra mayúscula. Por ejemplo: Usuario, Usuario_Administracion.
Las variables y objetos se nombrarán con minúsculas. Por ejemplo: $contador, $fecha_apertura.
Las constantes se escribirán íntegramente en mayúsculas. Por ejemplo: FICHERO, FICHERO_USUARIOS
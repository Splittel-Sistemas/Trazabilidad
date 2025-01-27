function success(titulo,mensaje){
    Swal.fire({
      icon: "success",
      title: titulo,
      text: mensaje,
      showConfirmButton: false,
      showCancelButton: true, 
      cancelButtonText: 'Cerrar',
      timer: 0
    });
}
function confirmacion(titulo,mensaje,confirmButtonText,funcion){
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText:"Cancelar",
        confirmButtonText: confirmButtonText,
      }).then((result) => {
        if (result.isConfirmed) {
          eval(funcion);
        }
      });
}
function error(titulo,mensaje){
    Swal.fire({
        icon: "error",
        title: titulo,
        text:mensaje,
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        showConfirmButton: false,
        timer: 5000
      });
}
function errorBD(){
    Swal.fire({
      icon: 'error',
      title: 'Ocurrio un Error',
      text: 'Ocurrio un error!, reviza tu conexión.',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      showConfirmButton: false,
      timer: 10000
    });
}
function CadenaVacia(cadena) {
    return /^\s*$/.test(cadena);
}
function CompararFechas(FechaInicio,FechaFin){
    if(FechaInicio<=FechaFin){
        return true;
    }else{return false;}
}
function RestarDia(fecha){
  fecha = new Date(fecha);
  fecha.setDate(fecha.getDate() - 1);
  nuevaFecha = fecha.toISOString().split('T')[0];  // Formato 'YYYY-MM-DD'
  return nuevaFecha;  // Salida: 2024-12-08
}
function SumarDia(fecha){
  fecha = new Date(fecha);
  fecha.setDate(fecha.getDate() + 1);
  nuevaFecha = fecha.toISOString().split('T')[0];  // Formato 'YYYY-MM-DD'
  return nuevaFecha;  // Salida: 2024-12-08
}
function FormatoFecha(Fecha){
  const fechaObjeto = new Date(Fecha);
  const dia = String(fechaObjeto.getDate()+1).padStart(2, '0');
  const mes = String(fechaObjeto.getMonth() + 1).padStart(2, '0'); // Los meses comienzan en 0
  const año = fechaObjeto.getFullYear();
  return dia+"/"+mes+"/"+año;
}
function RegexNumeros(datos){

    let valor = datos.value;
    // Reemplaza cualquier cosa que no sea un número (elimina letras, espacios y caracteres especiales)
    valor = valor.replace(/[^0-9]/g, '');
  
    // Asigna el valor filtrado de nuevo al campo de texto
    datos.value = valor;
}
function RegexNumerosGuiones(datos){
  let valor = datos.value;
  // Reemplaza cualquier cosa que no sea un número o un -
  valor = valor.replace(/[^0-9-]/g, '');
  // Asigna el valor filtrado de nuevo al campo de texto
  datos.value = valor;
}
function RegexNumeros_valor(datos){

  let valor = datos;
  // Reemplaza cualquier cosa que no sea un número (elimina letras, espacios y caracteres especiales)
  valor = valor.replace(/[^0-9]/g, '');

  // Asigna el valor filtrado de nuevo al campo de texto
  return valor;
}
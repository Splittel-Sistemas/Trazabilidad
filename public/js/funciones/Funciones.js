function success(titulo,mensaje){
    Swal.fire({
        position: "top-center",
        icon: "success",
        title: titulo,
        text:mensaje,
        showConfirmButton: false,
        timer: 2500
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
        confirmButtonText: confirmButtonText,
        cancelbuttonText:"Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          funcion+"()";
        }
      });
}
function error(titulo,mensaje){
    Swal.fire({
        position: "top-center",
        icon: "error",
        title: titulo,
        text:mensaje,
        showConfirmButton: false,
        timer: 2500
      });
}
function errorBD(){
    Swal.fire({
        position: "top-center",
        icon: "error",
        title: "Ocurrio un Error",
        text:"Ocurrio un error!, reviza tu conexión.",
        showConfirmButton: false,
        timer: 3000
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
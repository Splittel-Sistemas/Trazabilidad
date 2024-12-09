function success(titulo,mensaje){
    Swal.fire({
        position: "top-center",
        icon: "error",
        title: Titulo,
        messeage:mensaje,
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
        title: Titulo,
        messeage:mensaje,
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
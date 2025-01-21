<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuministrosController extends Controller
{
    protected $funcionesGenerales;

    public function __construct(FuncionesGeneralesController $funcionesGenerales)
    {
        $this->funcionesGenerales = $funcionesGenerales;
    }

    public function index()
    {
        // Obtener el usuario autenticado
       /* $user = Auth::user();
    
        // Verificar si el usuario tiene el permiso 'Vista Suministro'
        if ($user->hasPermission('Vista Suministro')) {
            // Retornar la vista correspondiente
            return view('suministros.cortes');
        } else {
            // Redirigir a una página de error si no tiene permiso
            return redirect()->away('https://assets-blog.hostgator.mx/wp-content/uploads/2018/10/paginas-de-error-hostgator.webp');
        }*/
    }
    

    public function enviar(Request $request)
{
    // Verifica los datos recibidos
   //Log::info('Datos del formulario:', $request->all());

    $request->validate([
        'ordenFabricacion' => 'required|string',
        'ordenParte' => 'required|string',
        'cantidad' => 'required|integer|min:1',
    ]);

    $data = $request->all();

   // Log::info('Datos validados:', $data);

    // Verificar la conexión con SAP antes de realizar la consulta
    if ($this->funcionesGenerales->checkSapConnection()) {
       // Log::info("Conexión a SAP exitosa.");

        try {
            // Preparamos la consulta para ejecutar en SAP
            $sql = "SELECT * FROM HN_OPTRONICS.OCRD WHERE OrdenFabricacion = ? AND OrdenParte = ?";
            $sapData = $this->funcionesGenerales->ejecutarConsulta($sql, [$data['ordenFabricacion'], $data['ordenParte']]);

            // Aquí puedes manejar los datos obtenidos de SAP si es necesario
            //Log::info("Datos obtenidos de SAP: " . json_encode($sapData));

            // Asegurémonos de que estamos redirigiendo correctamente
           // Log::info('Redirigiendo al formulario...');

            // Si la consulta es exitosa, redirigir con mensaje de éxito
            return redirect()->route('suministros.index')->with('success', 'Datos enviados y consultados correctamente en SAP');

        } catch (\Exception $e) {
           // Log::error('Error al consultar SAP: ' . $e->getMessage());
            return redirect()->route('suministros.index')->with('error', 'Error al consultar SAP: ' . $e->getMessage());
        } finally {
            // Cerrar la conexión SAP
            $this->funcionesGenerales->cerrarConexion();
        }
    } else {
       // Log::error("No se pudo conectar a SAP.");
        return redirect()->route('suministros.index')->with('error', 'No se pudo establecer conexión con SAP');
    }

    // Si llegamos aquí, significa que no hubo un retorno antes
    return redirect()->route('suministros.index')->with('success', 'Datos enviados y consultados correctamente en SAP');
}
}

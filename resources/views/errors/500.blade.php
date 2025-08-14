<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 500 - Problema en el servidor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f2f2f2;
        }
        h1 {
            font-size: 5rem;
            color: #e74c3c;
            margin-top: 0;
            margin-bottom: 0;
        }
        p {
            font-size: 18px;
            color: #555;
            margin-top: 0.5rem;
        }
        .button {
            background-color: #e74c3c;
            color: white;
            padding: 8px 8px;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 1s;
        }
        .button:hover {
            background-color: #cf1e0a;
        }
    </style>
</head>
<body>
    <img id="dimg_rRWeaNL7IpeEwbkPq9OBmQs_203" src="https://cdni.iconscout.com/illustration/premium/thumb/error-500-10492191-8488195.png" style="width: 20%;" alt="Imágenes de Error 500">
    <h1>Error 500</h1>
    <p>Lo sentimos, algo salió mal en el servidor. <br>Por favor, inténtalo de nuevo más tarde o contacta a TI si el problema persiste.</p>
    <a href="{{ route('login')}}" class="button">Regresar al Login</a>
</body>
</html>
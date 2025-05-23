<?php
// Verificar si la librería GD está habilitada
if (!extension_loaded('gd')) {
    die("La biblioteca GD no está habilitada en el servidor.");
}

// Calcular coeficientes binomiales utilizando el Triángulo de Pascal
function coeficienteBinomial($n, $k) {
    static $memo = [];
    if ($k == 0 || $k == $n) {
        return 1;
    }
    if (isset($memo[$n][$k])) {
        return $memo[$n][$k];
    }
    $memo[$n][$k] = coeficienteBinomial($n - 1, $k - 1) + coeficienteBinomial($n - 1, $k);
    return $memo[$n][$k];
}

// Expandir el binomio (a + b)^n
function expandirBinomio($a, $b, $n) {
    $resultado = "";
    for ($k = 0; $k <= $n; $k++) {
        $coef = coeficienteBinomial($n, $k);
        $aExp = $n - $k;
        $bExp = $k;
        
        // Construcción del término
        $termino = ($coef != 1 ? "$coef" : "");
        $termino .= ($aExp > 0 ? "$a" . ($aExp > 1 ? "^$aExp" : "") : "");
        $termino .= ($bExp > 0 ? "$b" . ($bExp > 1 ? "^$bExp" : "") : "");
        
        $resultado .= ($k > 0 ? " + " : "") . $termino;
    }
    return $resultado;
}

// Función para generar la imagen con el binomio expandido
function generarGraficoBinomio($a, $b, $n) {
    $ancho = 800;
    $alto = 300;
    $imagen = imagecreatetruecolor($ancho, $alto);

    // Colores
    $fondoColor = imagecolorallocate($imagen, 255, 255, 255); // Blanco
    $textoColor = imagecolorallocate($imagen, 0, 0, 0); // Negro
    $bordeColor = imagecolorallocate($imagen, 0, 102, 204); // Azul para bordes

    // Fondo de la imagen
    imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $fondoColor);

    // Generar la expresión binomial
    $expresion = "($a + $b)^$n = " . expandirBinomio($a, $b, $n);

    // Dividir la expresión en líneas
    $expresion = wordwrap($expresion, 50, "\n");

    // Escribir el texto en la imagen
    $lineas = explode("\n", $expresion);
    $y = 40;
    foreach ($lineas as $linea) {
        imagestring($imagen, 5, 30, $y, $linea, $textoColor);
        $y += 30;
    }

    // Enviar la imagen al navegador
    header("Content-Type: image/png");
    imagepng($imagen);
    imagedestroy($imagen);
}

// Captura de datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $n = isset($_POST["grado"]) ? max(0, (int)$_POST["grado"]) : 5;
    $a = isset($_POST["a"]) ? htmlspecialchars($_POST["a"]) : "a";
    $b = isset($_POST["b"]) ? htmlspecialchars($_POST["b"]) : "b";
    
    // Normalizar entrada
    $a = trim($a);
    $b = trim($b);

    // Generar gráfico binomial
    generarGraficoBinomio($a, $b, $n);
} else {
    // HTML del formulario con estilo
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Generador de Binomios Expandidos</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                padding: 30px;
                text-align: center;
                max-width: 500px;
                width: 100%;
            }
            h1 {
                font-size: 24px;
                color: #333;
            }
            label {
                font-size: 16px;
                color: #333;
            }
            input[type="number"],
            input[type="text"] {
                padding: 10px;
                font-size: 16px;
                width: 100%;
                margin: 10px 0;
                border: 2px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
            }
            button {
                padding: 10px 20px;
                background-color: #004B87;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            button:hover {
                background-color: #003366;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Binomios con el Triangulo de Pascal...</h1>
            <form method="post">
                <label for="grado">Grado del binomio (n):</label>
                <input type="number" name="grado" id="grado" min="0" required>
                <label for="a">Variable a:</label>
                <input type="text" name="a" id="a" required>
                <label for="b">Variable b:</label>
                <input type="text" name="b" id="b" required>
                <button type="submit">Calcular Expansión</button>
            </form>
        </div>
    </body>
    </html>';
}
?>


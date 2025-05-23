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

// Expandir el binomio y mostrar el paso a paso
function expandirBinomio($a, $b, $n) {
    $resultado = "";
    $pasos = "Expansion de ($a + $b)^$n:\n\n";
    
    for ($k = 0; $k <= $n; $k++) {
        $coef = coeficienteBinomial($n, $k);
        $aExp = $n - $k;
        $bExp = $k;
        
        $aTerm = ($aExp > 0) ? "^$aExp" : "";
        $bTerm = ($bExp > 0) ? "^$bExp" : "";
        
        // Evaluar valores numéricos si es posible
        $aVal = ($aExp > 0) ? pow((float)preg_replace('/[^0-9.-]/', '', $a), $aExp) : 1;
        $bVal = ($bExp > 0) ? pow((float)preg_replace('/[^0-9.-]/', '', $b), $bExp) : 1;
        $valorTermino = $coef * $aVal * $bVal;
        
        // Construcción del término
        $termino = "$coef * $aTerm * $bTerm = $valorTermino$aTerm$bTerm";
        
        // Agregar paso a paso
        $pasos .= "Paso " . ($k + 1) . ": C($n, $k) = $coef → $termino\n";
        
        $resultado .= ($k > 0 ? " + " : "") . $valorTermino . $aTerm . $bTerm;
    }
    $pasos .= "\nResultado final: $resultado";
    return $pasos;
}

// Función para generar la imagen con la resolución paso a paso
function generarGraficoBinomio($a, $b, $n) {
    $ancho = 800;
    $alto = 400;
    $imagen = imagecreatetruecolor($ancho, $alto);

    // Colores
    $fondoColor = imagecolorallocate($imagen, 255, 255, 255); // Blanco
    $textoColor = imagecolorallocate($imagen, 0, 0, 0); // Negro

    // Fondo de la imagen
    imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $fondoColor);

    // Obtener la expansión con los pasos
    $expansion = expandirBinomio($a, $b, $n);
    $lineas = explode("\n", $expansion);
    
    // Escribir el texto en la imagen
    $y = 20;
    foreach ($lineas as $linea) {
        imagestring($imagen, 5, 20, $y, $linea, $textoColor);
        $y += 20;
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

    // Generar gráfico binomial con pasos
    generarGraficoBinomio($a, $b, $n);
} else {
    // Formulario HTML
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Expansión de Binomios Paso a Paso</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            h1 {
                color: #333;
            }
            input, button {
                padding: 10px;
                font-size: 16px;
                margin: 10px;
            }
            button {
                background-color: #004B87;
                color: white;
                border: none;
                cursor: pointer;
            }
            button:hover {
                background-color: #003366;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Expansión de Binomios Paso a Paso</h1>
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


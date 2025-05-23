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

// Expandir un binomio (a + b)^n usando el Triángulo de Pascal
function expandirBinomio($a, $b, $n) {
    $resultado = "";
    for ($k = 0; $k <= $n; $k++) {
        $coef = coeficienteBinomial($n, $k);
        $aExp = $n - $k;
        $bExp = $k;

        $termino = ($coef != 1 ? "$coef" : "");
        $termino .= ($aExp > 0 ? "$a" . ($aExp > 1 ? "^$aExp" : "") : "");
        $termino .= ($bExp > 0 ? "$b" . ($bExp > 1 ? "^$bExp" : "") : "");

        $resultado .= ($k > 0 ? " + " : "") . $termino;
    }
    return $resultado;
}

// Expandir un polinomio con varios términos (generalización)
function expandirPolinomio($terminos, $n) {
    $resultado = "";
    $numTerminos = count($terminos);

    for ($k = 0; $k <= $n; $k++) {
        $coef = coeficienteBinomial($n, $k);
        $expresion = "";

        for ($i = 0; $i < $numTerminos; $i++) {
            $exp = $n - $k;
            if ($exp > 0) {
                $expresion .= $terminos[$i] . ($exp > 1 ? "^$exp" : "");
            }
            $expresion .= ($i < $numTerminos - 1) ? " + " : "";
        }

        $resultado .= ($k > 0 ? " + " : "") . $expresion;
    }

    return $resultado;
}

// Función para generar la imagen con la expansión
function generarGrafico($expresion) {
    $ancho = 800;
    $alto = 300;
    $imagen = imagecreatetruecolor($ancho, $alto);

    // Colores
    $fondoColor = imagecolorallocate($imagen, 255, 255, 255); // Blanco
    $textoColor = imagecolorallocate($imagen, 0, 0, 0); // Negro

    // Fondo de la imagen
    imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $fondoColor);

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
    $terminos = isset($_POST["terminos"]) ? $_POST["terminos"] : ["a", "b"]; // Asegurar que hay al menos dos términos
    $tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : "binomio"; // Determinar tipo de expresión

    // Normalizar entrada
    $terminos = array_map('trim', $terminos);

    // Generar la expansión según el tipo
    if ($tipo == "monomio") {
        // Para monomios simplemente elevamos la variable a la potencia
        $expresion = $terminos[0] . "^$n";
    } elseif ($tipo == "binomio") {
        // Expansión de binomio (a + b)^n
        $expresion = expandirBinomio($terminos[0], $terminos[1], $n);
    } elseif ($tipo == "trinomio") {
        // Expansión de trinomio (a + b + c)^n
        $expresion = expandirPolinomio($terminos, $n);
    } else {
        // Expansión de polinomio general
        $expresion = expandirPolinomio($terminos, $n);
    }

    // Generar gráfico con la expansión
    generarGrafico($expresion);
} else {
    // HTML del formulario con estilo
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Generador de Expansiones de Polinomios</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #e9eff1;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                background-color: #ffffff;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 40px;
                text-align: center;
                max-width: 600px;
                width: 100%;
                box-sizing: border-box;
            }
            h1 {
                font-size: 28px;
                color: #333;
                margin-bottom: 30px;
            }
            label {
                font-size: 16px;
                color: #333;
                display: block;
                margin-bottom: 5px;
            }
            input[type="number"],
            input[type="text"] {
                padding: 12px;
                font-size: 16px;
                width: 100%;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
                background-color: #f9f9f9;
            }
            input[type="number"]:focus,
            input[type="text"]:focus {
                border-color: #66afe9;
                outline: none;
                background-color: #fff;
            }
            button {
                padding: 12px 20px;
                background-color: #004B87;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 20px;
            }
            button:hover {
                background-color: #003366;
            }
            .form-footer {
                font-size: 14px;
                color: #777;
                margin-top: 30px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Generador de Expansiones de Polinomios</h1>
            <form method="post">
                <label for="grado">Grado (n):</label>
                <input type="number" name="grado" id="grado" min="0" required>
                <label for="terminos">Términos (separados por comas):</label>
                <input type="text" name="terminos[]" id="terminos" required placeholder="Ejemplo: a, b, c">
                <label for="tipo">Tipo de expresión:</label>
                <select name="tipo" id="tipo">
                    <option value="monomio">Monomio</option>
                    <option value="binomio">Binomio</option>
                    <option value="trinomio">Trinomio</option>
                    <option value="polinomio">Polinomio</option>
                </select>
                <button type="submit">Generar Expansión</button>
            </form>
            <div class="form-footer">
                <p>Este generador maneja monomios, binomios, trinomios y polinomios de cualquier grado.</p>
            </div>
        </div>
    </body>
    </html>';
}
?>

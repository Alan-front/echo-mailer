<?php


require __DIR__.'/../vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// incluir archivos necesarios
require_once '../config/conexion.php';

//  tokens Groq
$TOKENS_GROQ = [
    $_ENV['TOKENS_GROQ_1'],
    $_ENV['TOKENS_GROQ_2']
];

$PROMPT_IA = "Eres un analizador de respuestas a campañas de mailing de música. Debes responder solo con 4 números separados por comas: estado,audio,video,ficha.
Reglas:
- estado = 0 → el mensaje solicita explícitamente al menos uno de audio, video o ficha (archivos de difusión).  
- estado = 2 → el mensaje NO solicita archivos de difusión, o solicita contratos, permisos, revisión manual, formularios u otras gestiones administrativas; entonces audio=0, video=0, ficha=0.  
- audio = 1 → solicita específicamente audio para difusión; 0 = no.  
- video = 1 → solicita específicamente video para difusión; 0 = no.  
- ficha = 1 → solicita específicamente historia de la banda, ficha técnica, press kit o biografía; 0 = no.  
**Importante:** si estado=0 **nunca deben ir mas de 2 archivos en 0**. 
**Importante:** el resultado final nunca debe ser 0,0,0,0 **.
*Importante:** si estado=2 **los 3 archivos deben estar en 0**. 
***Importante:** si mencionan algun formato de video o 4k, mov, avi, mp4 entonces estan pidiendo video → 0,0,0,1 **. 
Ignora opiniones neutrales y negativas, insultos, comentarios generales negativos o solicitudes de contratos/permisos.  
Ejemplos:
- \"Quiero la biografía del grupo\" → 0,0,0,1  
- \"Envíen audio y video\" → 0,1,1,0  
- \"Gracias, para transmitir necesitamos que firmen unos contratos\" → 2,0,0,0  
- \"Necesitamos que nos autoricen a difundir su música\" → 2,0,0,0  
- \"Mete al orto tu música\" → 2,0,0,0 
  - \"Аха-ха, какие же мы дурачки\" → 2,0,0,0
- \"Hola, no comprendí, hablas rumano?\" → 2,0,0,0";

// variables globales para control de tokens
$tokens_funcionando = [];
$contador_filas = 0;
$token_actual_index = 0;
$modo_respaldo = false;

/**
 * prueba si un token de groq esta funcionando
 */
function testearTokenGroq($token) {
    global $PROMPT_IA;
    
    $mensaje_test = "Envíen el audio para difusión por favor";
    
    $data = [
        "messages" => [
            [
                "role" => "system",
                "content" => $PROMPT_IA
            ],
            [
                "role" => "user", 
                "content" => $mensaje_test
            ]
        ],
        "model" => "llama-3.1-8b-instant",
        "temperature" => 0.1,
        "max_tokens" => 50
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.groq.com/openai/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 && $response !== false) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $content = trim($result['choices'][0]['message']['content']);
            // Verificar que la respuesta tenga el formato esperado (4 números separados por comas)
            if (preg_match('/^\d,\d,\d,\d$/', $content)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * inicializar sistema de tokens
 */
function inicializarTokens() {
    global $TOKENS_GROQ, $tokens_funcionando, $modo_respaldo;
    
    echo "🔧 Iniciando tests de tokens...\n";
    
    foreach ($TOKENS_GROQ as $index => $token) {
        echo "Test Token " . ($index + 1) . ": ";
        if (testearTokenGroq($token)) {
            $tokens_funcionando[] = $index;
            echo "Funcionando\n";
        } else {
            echo "Error\n";
        }
        // pequeña pausa entre tests
        usleep(500000); // 0.5 segundos
    }
    
    $total_funcionando = count($tokens_funcionando);
    
    if ($total_funcionando === 2) {
        echo "Modo: Alternancia normal (ambos tokens activos)\n";
    } elseif ($total_funcionando === 1) {
        $modo_respaldo = true;
        echo " Modo: Respaldo (1 token + regex alternado)\n";
    } else {
        echo "Modo: Solo regex (ningún token funciona)\n";
    }
    
    return $total_funcionando;
}

/**
 * analizar mensaje con IA usando groq
 */
function analizarConIA($mensaje, $token) {
    global $PROMPT_IA;
    
    $data = [
        "messages" => [
            [
                "role" => "system",
                "content" => $PROMPT_IA
            ],
            [
                "role" => "user",
                "content" => $mensaje
            ]
        ],
        "model" => "llama-3.1-8b-instant",
        "temperature" => 0.1,
        "max_tokens" => 50
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.groq.com/openai/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 && $response !== false) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $content = trim($result['choices'][0]['message']['content']);
            // Verificar formato: 4 números separados por comas
            if (preg_match('/^(\d),(\d),(\d),(\d)$/', $content, $matches)) {
                return [
                    intval($matches[1]), // estado
                    intval($matches[2]), // audio  
                    intval($matches[3]), // video
                    intval($matches[4])  // ficha
                ];
            }
        }
    }
    
    return null; // error o formato inválido
}

/**
 * funcion analizar con regex mejorado integrada directamente (BACKUP)
 */
function analizarConRegexMejorado($texto) {
    $texto_lower = strtolower($texto);
    
    $audio = 0;
    $video = 0; 
    $ficha = 0;
    $estado = 0;
    
    // casos especificos
    
    // Caso 80: "NO es necesario el material audiovisual (ni audio ni video)" + pide ficha
    if (strpos($texto_lower, 'no es necesario el material audiovisual') !== false && 
        strpos($texto_lower, 'ficha biográfica') !== false) {
        return [0, 0, 0, 1];
    }
    
    // Caso 81: "If our committee approves" + "only the artist biography is required"
    if (strpos($texto_lower, 'if our committee approves') !== false && 
        strpos($texto_lower, 'only the artist biography is required') !== false) {
        return [0, 0, 0, 1];
    }
    
    
    
    // AUDIO
    $audio_keywords = [
        'audio', 'mp3', 'wav', 'pistas', 'canción', 'cancion', 'música', 'musica',
        'envíe el audio', 'enviar audio', 'tracks', 'song', 'songs',
        'send us the audio', 'send audio', 'the audio', 'audio tracks', 'audio files',
        'high quality audio', 'audio in', 'format wav', 'format mp3',
        "l'audio", 'audio sur', 'fichier audio', 'fichiers audio',
        'аудіо', 'аудио', 'аудио в', 'аудіо файли',
        'オーディオ', 'オーディオは', 'オーディオファイル',
        '오디오', '음원',
        'grabaciones sonoras', 'archivos sonoros', 'archivos de audio', 'máxima fidelidad',
        'master recordings', 'master-records', 'uncompressed format', 'audio master', 'studio quality',
        'enregistrements originaux', 'qualité studio', 'fichiers sonores',
        '音源データ', '音源', '音質',
        '오디오 마스터', '음질', '오디오 파일', 'аудіо-доріжки',
        'мастер-записи', 'студийном качестве', 'аудиофайлы', 'аудіофайли',
        'майстер-записи', 'студійній якості', 'material sonoro',
        'temazo', 'pásenme el audio', '音の肖像', 'retrato del sonido'
    ];
    foreach ($audio_keywords as $keyword) {
        if (strpos($texto_lower, $keyword) !== false) {
            $audio = 1;
            break;
        }
    }
    
    // VIDEO
    $video_keywords = [
        'video', 'vídeo', 'clip', '4k', 'hd video', 'video file', 'mp4', 'avi', 'mov',
        'music video', 'clip musical', 'vidéo', 'diffuser votre vidéo',
        'відео', 'видео', 'видео в 4k',
        'ビデオ', 'ビデオは', 'ビデオファイル', '映像コンテンツ', '動画ファイル',
        '비디오', '뮤직비디오', '영상'
    ];
    foreach ($video_keywords as $keyword) {
        if (strpos($texto_lower, $keyword) !== false) {
            $video = 1;
            break;
        }
    }
    
    // FICHA
    $ficha_keywords = [
        'ficha técnica', 'ficha tecnica', 'ficha', 'historia del proyecto', 'biografía', 'biografia',
        'información del proyecto', 'informacion del proyecto', 'datos del proyecto',
        'technical sheet', 'press kit', 'history of the project', 'biography', 'press materials',
        'band info', 'project info', 'technical data', 'bio',
        'fiche technique', 'histoire du projet', 'biographie',
        'технічну довідку', 'техническую справку', 'историю проекта', 'техническа карта',
        '技術仕様書', 'プレスキット', 'バイオグラフィー', '技術資料', 'プロジェクト',
        '기술 자료', '프로젝트 정보', '기술자료'
    ];
    foreach ($ficha_keywords as $keyword) {
        if (strpos($texto_lower, $keyword) !== false) {
            $ficha = 1;
            break;
        }
    }
    
    // MENSAJES RAROS
    $mensajes_raros = [
        'no entiendo', 'qué quieren', 'que quieren', 'no comprendo',
        'what exactly', 'i dont understand', "don't understand", 'are you a robot', 
        'what are you trying', 'trying to sell',
        'ага-ага', 'аха-ха', 'дурачки', 'не понял вообще о чем речь'
    ];
    
    $es_mensaje_raro = false;
    foreach ($mensajes_raros as $raro) {
        if (strpos($texto_lower, $raro) !== false) {
            if ($audio == 0 && $video == 0 && $ficha == 0) {
                $es_mensaje_raro = true;
                break;
            }
        }
    }
    
    if ($es_mensaje_raro) {
        $estado = 2;
        $audio = 0;
        $video = 0; 
        $ficha = 0;
    } else if ($audio == 0 && $video == 0 && $ficha == 0) {
        $estado = 2;
    } else {
        $estado = 0;
    }
    
    return [$estado, $audio, $video, $ficha];
}

/**
 * funcion principal de analisis con sistema hibridos IA + regex
 */
function analizarMensaje($mensaje) {
    global $TOKENS_GROQ, $tokens_funcionando, $contador_filas, $token_actual_index, $modo_respaldo;
    
    $total_tokens = count($tokens_funcionando);
    
    // si no hay tokens funcionando, usar solo regex
    if ($total_tokens === 0) {
        return analizarConRegexMejorado($mensaje);
    }
    

    $usar_ia = false;
    $token_a_usar = null;
    
    if ($modo_respaldo) {
        
        $bloque = floor($contador_filas / 7);
        $usar_ia = ($bloque % 2 === 0); 
        if ($usar_ia) {
            $token_a_usar = $TOKENS_GROQ[$tokens_funcionando[0]];
        }
    } else {
        
        $bloque = floor($contador_filas / 8);
        $token_actual_index = $bloque % $total_tokens;
        $usar_ia = true;
        $token_a_usar = $TOKENS_GROQ[$tokens_funcionando[$token_actual_index]];
    }
    
    $contador_filas++;
    
    if ($usar_ia && $token_a_usar) {
       
        $resultado_ia = analizarConIA($mensaje, $token_a_usar);
        if ($resultado_ia !== null) {
            return $resultado_ia;
        }
        
    }
    
    // usar regex
    return analizarConRegexMejorado($mensaje);
}


if (!isset($_GET['id_campana']) || empty($_GET['id_campana'])) {
    die("Error: Debe proporcionar el parámetro 'id_campana' en la URL.\nEjemplo: ?id_campana=32\n");
}

$id_campana = intval($_GET['id_campana']);

try {
    // conectar a la base de datos
    $con = conectar();
    

    $tokens_activos = inicializarTokens();
    echo "\n";
    
    // rpeparar y ejecutar consulta para obtener registros
    $stmt_select = $con->prepare("SELECT id, mensaje FROM bandejas_campaña WHERE id_campaña = ? ORDER BY id");
    $stmt_select->bind_param("i", $id_campana);
    $stmt_select->execute();
    $resultado = $stmt_select->get_result();
    
    // verificar si hay resultados
    $total_filas = $resultado->num_rows;
    
    if ($total_filas === 0) {
        echo "No se encontraron registros para la campaña $id_campana\n";
        exit;
    }
    
    echo "Procesando campaña $id_campana ($total_filas registros)...\n";
    echo "Tokens activos: $tokens_activos/2\n\n";
    

    $stmt_update = $con->prepare("UPDATE bandejas_campaña SET estado = ?, inc_audio = ?, inc_video = ?, inc_ficha = ? WHERE id = ?");
    

    $contadores = [
        'procesados' => 0,
        'actualizados' => 0,
        'errores' => 0,
        'estado' => [0 => 0, 1 => 0, 2 => 0],
        'audio' => 0,
        'video' => 0,
        'ficha' => 0,
        'metodo_ia' => 0,
        'metodo_regex' => 0
    ];
    

    $contador_filas = 0;
    

    while ($fila = $resultado->fetch_assoc()) {
        $id = $fila['id'];
        $mensaje = $fila['mensaje'];
        

        $analisis = analizarMensaje($mensaje);
        $estado = $analisis[0];
        $audio = $analisis[1];
        $video = $analisis[2];
        $ficha = $analisis[3];
        
        // acualizar registro
        $stmt_update->bind_param("iiiii", $estado, $audio, $video, $ficha, $id);
        
        if ($stmt_update->execute()) {
            $contadores['actualizados']++;
            
           
            if ($contadores['procesados'] % 10 === 0) {
                echo "Procesando... {$contadores['procesados']}/$total_filas\n";
            }
        } else {
            $contadores['errores']++;
            echo "Error actualizando registro ID $id: " . $stmt_update->error . "\n";
        }
        
    // actualizar contadores
        $contadores['procesados']++;
        $contadores['estado'][$estado]++;
        if ($audio) $contadores['audio']++;
        if ($video) $contadores['video']++;
        if ($ficha) $contadores['ficha']++;
        

        $fila_actual = $contador_filas - 1; 
        if ($tokens_activos > 0) {
            if ($modo_respaldo) {
                $bloque = floor($fila_actual / 8);
                $usar_ia = ($bloque % 2 === 0);
                if ($usar_ia) {
                    $contadores['metodo_ia']++;
                } else {
                    $contadores['metodo_regex']++;
                }
            } else {
                $contadores['metodo_ia']++;
            }
        } else {
            $contadores['metodo_regex']++;
        }
        
        
        usleep(100000); 
    }
    
    // mostrar resumen final
    echo "\n PROCESO COMPLETADO \n";
    echo "========================\n";
    echo "Total procesados: {$contadores['procesados']}\n";
    echo "Actualizados exitosamente: {$contadores['actualizados']}\n";
    echo "Errores: {$contadores['errores']}\n\n";
    
    echo "ESTADÍSTICAS DE ANÁLISIS:\n";
    echo "----------------------------\n";
    echo "Estado 0 (Enviar archivos): {$contadores['estado'][0]}\n";
    echo "Estado 1 (Pendientes): {$contadores['estado'][1]}\n";
    echo "Estado 2 (Mensajes raros): {$contadores['estado'][2]}\n\n";
    
    echo "ARCHIVOS REQUERIDOS:\n";
    echo "-----------------------\n";
    echo "Audio: {$contadores['audio']}\n";
    echo "Video: {$contadores['video']}\n";
    echo "Ficha: {$contadores['ficha']}\n\n";
    
    echo "MÉTODOS UTILIZADOS:\n";
    echo "----------------------\n";
    echo "IA: {$contadores['metodo_ia']}\n";
    echo "Regex: {$contadores['metodo_regex']}\n\n";
    
    // cerrar conexion
    $stmt_select->close();
    $stmt_update->close();
    $con->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
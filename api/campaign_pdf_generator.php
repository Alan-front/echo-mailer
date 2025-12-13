<?php
require_once('tcpdf/tcpdf.php');

class CampaignPDFGenerator extends TCPDF {
    
    // Configuración del header
    public function Header() {
        $this->SetFont('helvetica', 'B', 20);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 15, 'Reporte de Campaña Musical', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(20);
    }

    // Configuración del footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i:s'), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

function generarPDFCampana($datos) {
    // Crear nueva instancia de PDF
    $pdf = new CampaignPDFGenerator(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Configuración del documento
    $pdf->SetCreator('Sistema de Campañas');
    $pdf->SetAuthor('Tu Sistema');
    $pdf->SetTitle('Reporte de Campaña - ' . $datos['nombre_lanzamiento']);
    $pdf->SetSubject('Reporte detallado de campaña musical');
    
    // Configurar márgenes
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
    // Auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // Agregar página
    $pdf->AddPage();
    
    // ========== INFORMACIÓN PRINCIPAL ==========
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(52, 58, 64);
    $pdf->Cell(0, 12, 'Información de la Campaña', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Ln(5);
    
    // Card principal con información básica
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(206, 212, 218);
    $pdf->Rect(10, $pdf->GetY(), 190, 45, 'DF');
    
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetXY(15, $pdf->GetY() + 8);
    
    $info_basica = [
        ['label' => 'ID Campaña:', 'value' => $datos['id_campaña']],
        ['label' => 'Artista:', 'value' => $datos['artista']],
        ['label' => 'Lanzamiento:', 'value' => $datos['nombre_lanzamiento']],
        ['label' => 'Tipo:', 'value' => $datos['tipo_de_lanzamiento']]
    ];
    
    foreach ($info_basica as $item) {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, $item['label'], 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $item['value'], 0, 1, 'L');
    }
    
    $pdf->Ln(10);
    
    // ========== MÉTRICAS PRINCIPALES ==========
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(52, 58, 64);
    $pdf->Cell(0, 12, 'Métricas de Rendimiento', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Ln(5);
    
    // Cards de métricas
    $y_inicial = $pdf->GetY();
    
    // Card 1: Total Enviados
    $pdf->SetFillColor(220, 248, 198);
    $pdf->SetDrawColor(40, 167, 69);
    $pdf->Rect(10, $y_inicial, 60, 35, 'DF');
    $pdf->SetXY(15, $y_inicial + 8);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(40, 167, 69);
    $pdf->Cell(50, 8, $datos['total_enviados'], 0, 1, 'C');
    $pdf->SetXY(15, $y_inicial + 20);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 8, 'Total Enviados', 0, 1, 'C');
    
    // Card 2: Total Respuestas
    $pdf->SetFillColor(179, 229, 252);
    $pdf->SetDrawColor(23, 162, 184);
    $pdf->Rect(75, $y_inicial, 60, 35, 'DF');
    $pdf->SetXY(80, $y_inicial + 8);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(23, 162, 184);
    $pdf->Cell(50, 8, $datos['total_respuestas'], 0, 1, 'C');
    $pdf->SetXY(80, $y_inicial + 20);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 8, 'Total Respuestas', 0, 1, 'C');
    
    // Card 3: Porcentaje de Respuesta
    $pdf->SetFillColor(255, 243, 205);
    $pdf->SetDrawColor(255, 193, 7);
    $pdf->Rect(140, $y_inicial, 60, 35, 'DF');
    $pdf->SetXY(145, $y_inicial + 8);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(255, 193, 7);
    $pdf->Cell(50, 8, $datos['porcentaje_respuesta'] . '%', 0, 1, 'C');
    $pdf->SetXY(145, $y_inicial + 20);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(50, 8, '% de Respuesta', 0, 1, 'C');
    
    $pdf->SetY($y_inicial + 45);
    $pdf->Ln(10);
    
    // ========== FECHAS DE ENVÍO ==========
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(52, 58, 64);
    $pdf->Cell(0, 12, 'Historial de Envíos', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Ln(5);
    
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(206, 212, 218);
    
    foreach ($datos['fechas_envio'] as $index => $fecha) {
        $fecha_formateada = date('d/m/Y H:i', strtotime($fecha));
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(108, 117, 125);
        $pdf->Cell(30, 8, 'Envío ' . ($index + 1) . ':', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->Cell(0, 8, $fecha_formateada, 0, 1, 'L');
    }
    
    $pdf->Ln(10);
    
    // ========== RESPUESTAS POR FECHA ==========
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(52, 58, 64);
    $pdf->Cell(0, 12, 'Distribución de Respuestas por Fecha', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Ln(5);
    
    // Tabla de respuestas por fecha
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(95, 10, 'Fecha', 1, 0, 'C', 1);
    $pdf->Cell(95, 10, 'Número de Respuestas', 1, 1, 'C', 1);
    
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->SetFont('helvetica', '', 10);
    
    $fill = false;
    foreach ($datos['respuestas_por_fecha'] as $fecha => $respuestas) {
        $fecha_formateada = date('d/m/Y', strtotime($fecha));
        $pdf->Cell(95, 8, $fecha_formateada, 1, 0, 'C', $fill);
        $pdf->Cell(95, 8, $respuestas, 1, 1, 'C', $fill);
        $fill = !$fill;
    }
    
    // ========== GRÁFICO VISUAL SIMPLE ==========
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(52, 58, 64);
    $pdf->Cell(0, 12, 'Visualización de Respuestas', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Ln(5);
    
    // Crear un gráfico de barras simple
    $max_respuestas = max($datos['respuestas_por_fecha']);
    $y_grafico = $pdf->GetY();
    $altura_barra_max = 30;
    $ancho_barra = 35;
    $x_inicial = 20;
    
    $colores = [
        [52, 144, 220],   // Azul
        [40, 167, 69],    // Verde
        [255, 193, 7],    // Amarillo
        [220, 53, 69],    // Rojo
        [108, 117, 125]   // Gris
    ];
    
    $i = 0;
    foreach ($datos['respuestas_por_fecha'] as $fecha => $respuestas) {
        $altura_barra = ($respuestas / $max_respuestas) * $altura_barra_max;
        $x_pos = $x_inicial + ($i * ($ancho_barra + 10));
        
        // Dibujar barra
        $color = $colores[$i % count($colores)];
        $pdf->SetFillColor($color[0], $color[1], $color[2]);
        $pdf->Rect($x_pos, $y_grafico + $altura_barra_max - $altura_barra, $ancho_barra, $altura_barra, 'F');
        
        // Etiqueta de fecha
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetXY($x_pos - 5, $y_grafico + $altura_barra_max + 5);
        $fecha_corta = date('d/m', strtotime($fecha));
        $pdf->Cell($ancho_barra + 10, 5, $fecha_corta, 0, 0, 'C');
        
        // Valor encima de la barra
        $pdf->SetXY($x_pos, $y_grafico + $altura_barra_max - $altura_barra - 8);
        $pdf->Cell($ancho_barra, 5, $respuestas, 0, 0, 'C');
        
        $i++;
    }
    
    // Generar el PDF
    return $pdf;
}

// ========== FUNCIÓN PRINCIPAL ==========
function crearPDFDesdeDatos($datos_json) {
    // Si recibes los datos como JSON string, decodifícalos
    if (is_string($datos_json)) {
        $datos = json_decode($datos_json, true);
    } else {
        $datos = $datos_json;
    }
    
    // Generar PDF
    $pdf = generarPDFCampana($datos);
    
    // Definir nombre del archivo
    $nombre_archivo = 'reporte_campana_' . $datos['id_campaña'] . '_' . date('Y-m-d_H-i-s') . '.pdf';
    
    // Puedes elegir una de estas opciones:
    
    // Opción 1: Mostrar en el navegador
    $pdf->Output($nombre_archivo, 'I');
    
    // Opción 2: Forzar descarga
    // $pdf->Output($nombre_archivo, 'D');
    
    // Opción 3: Guardar en servidor
    // $pdf->Output('pdfs/' . $nombre_archivo, 'F');
    
    // Opción 4: Devolver como string para más procesamiento
    // return $pdf->Output($nombre_archivo, 'S');
}

// ========== EJEMPLO DE USO ==========
// Tus datos de ejemplo
$datos_ejemplo = [
    "id_campaña" => 32,
    "artista" => "Acid Plus",
    "nombre_lanzamiento" => "Coconut funk",
    "tipo_de_lanzamiento" => "Single",
    "total_enviados" => 5,
    "fechas_envio" => [
        "2025-08-08 00:40:29",
        "2025-08-21 16:18:28"
    ],
    "total_respuestas" => 21,
    "respuestas_por_fecha" => [
        "2025-08-08" => 3,
        "2025-08-10" => 1,
        "2025-08-12" => 14,
        "2025-08-13" => 3
    ],
    "porcentaje_respuesta" => 420
];

// Para usar el generador:
// crearPDFDesdeDatos($datos_ejemplo);

// O si recibes los datos desde tu PHP existente:
// $datos_desde_api = tu_funcion_que_obtiene_datos($parametro);
// crearPDFDesdeDatos($datos_desde_api);

?>
<?php
	require_once 'conexion.php';
	
	header('Content-Type: application/json');
	
	$connec = conectar();
	
	$idCampana = $_GET['id_campana'] ?? null;
	
	if (!$idCampana) {
		$data = json_decode(file_get_contents('php://input'), true);
		$idCampana = $data['id_campana'] ?? null;
	}
	
	if (!$idCampana) {
		die(json_encode(['error' => 'ID de campaña requerido'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	}
	
	// ============================================
	// PASO 1: Obtener datos de la campaña
	// ============================================
	$sqlCampana = "SELECT id, tipo_de_lanzamiento, enlace, activa, id_email_account 
				   FROM campañas 
				   WHERE id = ?";
	$stmtCampana = $connec->prepare($sqlCampana);
	$stmtCampana->bind_param("i", $idCampana);
	$stmtCampana->execute();
	$campana = $stmtCampana->get_result()->fetch_assoc();
	$stmtCampana->close();
	
	if (!$campana) {
		die(json_encode(['error' => 'Campaña no encontrada'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	}
	
	// ============================================
	// PASO 2: Obtener contactos con estado = 0
	// ============================================
	$sql = "SELECT e.id_contacto, e.estado,
				   c.secondary_language, c.media_type, c.test_email
			FROM enviados e
			INNER JOIN media_contacts c ON e.id_contacto = c.id
			WHERE e.campaña_id = ? AND e.estado = 0";
	
	$stmt = $connec->prepare($sql);
	$stmt->bind_param("i", $idCampana);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$contactos = [];
	while ($row = $result->fetch_assoc()) {
		$contactos[] = $row;
	}
	
	// ============================================
	// RESPUESTA JSON
	// ============================================
	echo json_encode([
		'campana' => $campana,
		'contactos' => $contactos,
		'total' => count($contactos)
	], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	
	$stmt->close();
	$connec->close();
?>
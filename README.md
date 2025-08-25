# Echo Mailer

## Descripción
Echo Mailer es una aplicación diseñada para promocionar artistas emergentes enviando campañas de email a medios (radios, TV, revistas) de manera rápida, automatizada y multilingüe.

Lo que antes llevaba semanas de trabajo manual, ahora se realiza en minutos: envíos masivos, seguimiento de respuestas y asignación automática de tareas según el contenido recibido.

## Funcionalidad destacada
- Envía emails masivos con plantillas y archivos asignados según el idioma del contacto.
- Extrae respuestas automáticamente desde bandejas de correo mediante IMAP.
- Analiza y clasifica respuestas con IA + regex + 2 tokens para máxima confiabilidad.
- Asigna tareas automáticamente: enviar audio, video, ficha técnica o marcar para revisión manual si algo está fuera de contexto.
- Organiza PDFs y archivos por idioma.
- Genera estadísticas de envío y seguimiento de la campaña con gráficos personalizados usando la librería Chart y colores de marca.

## Tecnologías utilizadas
- **Frontend:** Vue 3 (script setup) con Bootstrap personalizado.
- **Backend:** PHP, PHPMailer, IMAP.
- **IA:** Sistema robusto de análisis de mensajes con 2 tokens y regex.
- **Visualización:** Librería Chart personalizada con colores de marca.

## Estado actual
- Lógica principal funcional y testeada con casos reales ✅
- Responsive y ajustes visuales pendientes ⚠️
- Optimizado para PC y tablet; no recomendado para celulares en campañas grandes.

## Demo
🎬 Mira cómo funciona la app en este video:  
[Enlace al video de demostración](https://youtu.be/RX90JK09Tv4)

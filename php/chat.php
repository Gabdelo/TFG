<?php
require '../includes/conexionDB.php';
$conn = conectar();

session_start();
if(!isset($_SESSION['id_usuario'])) exit; // seguridad

$id_usuario = $_SESSION['id_usuario'];
$id_chat = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$id_chat) exit; // si no hay chat seleccionado, no hacer nada

$sqlMensajes = "
    SELECT * FROM mensajes 
    WHERE (id_emisor = ? AND id_receptor = ?) 
       OR (id_emisor = ? AND id_receptor = ?)
    ORDER BY fecha_envio ASC
";

$stmt = $conn->prepare($sqlMensajes);
$stmt->bind_param("iiii", $id_usuario, $id_chat, $id_chat, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

while($msg = $res->fetch_assoc()):
    $clase = ($msg['id_emisor'] == $id_usuario) ? 'enviado' : 'recibido';
?>
<div class="mensaje <?php echo $clase; ?>">
    <p><?php echo htmlspecialchars($msg['mensaje']); ?></p>
    <small><?php echo date("H:i", strtotime($msg['fecha_envio'])); ?></small>
</div>
<?php endwhile; ?>

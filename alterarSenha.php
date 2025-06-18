<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}
include_once './config/config.php';
include_once './classes/Usuario.php';
$usuario = new Usuario($db);

// Verifica se o ID foi fornecido e se corresponde ao usuário logado
if (!isset($_GET['id']) || $_GET['id'] != $_SESSION['usuario_id']) {
    header('Location: portal.php');
    exit();
}

$id = $_GET['id'];
$row = $usuario->lerPorId($id);

if (!$row) {
    header('Location: portal.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $sexo = $_POST['sexo'];
    $fone = $_POST['fone'];
    $email = $_POST['email'];
    $usuario->atualizar($id, $nome, $sexo, $fone, $email);
    header('Location: portal.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário - CSL Times</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <div class="portal-header portal-header-portal">
    <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img" style="width: 120px; height: 100px;">

        <div class="portal-header-content">
            <h1>Editar Usuário</h1>
            <div class="portal-nav">
                <a href="portal.php"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form method="POST" class="portal-form">
            <div class="form-header">
                <i class="fa-solid fa-user-edit"></i>
                <h2>Editar Dados do Usuário</h2>
            </div>

            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
            
            <div class="form-group">
                <label for="nome"><i class="fas fa-user"></i> Nome:</label>
                <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($row['nome']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-venus-mars"></i> Sexo:</label>
                <div class="form-row">
                    <label for="masculino_editar">
                        <input type="radio" id="masculino_editar" name="sexo" value="M" <?php echo ($row['sexo'] === 'M') ? 'checked' : ''; ?> required>
                        <i class="fa-solid fa-mars"></i> Masculino
                    </label>
                    <label for="feminino_editar">
                        <input type="radio" id="feminino_editar" name="sexo" value="F" <?php echo ($row['sexo'] === 'F') ? 'checked' : ''; ?> required>
                        <i class="fa-solid fa-venus"></i> Feminino
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="fone"><i class="fas fa-phone"></i> Telefone:</label>
                <input type="text" name="fone" id="fone" value="<?php echo htmlspecialchars($row['fone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </form>
    </div>

    <script>
        const foneInput = document.getElementById('fone');

        foneInput.addEventListener('input', function(e) {
            let valor = e.target.value;

            valor = valor.replace(/\D/g, '');

            valor = valor.slice(0, 11);

            if (valor.length > 6) {
                valor = valor.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            } else if (valor.length > 2) {
                valor = valor.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (valor.length > 0) {
                valor = valor.replace(/^(\d*)/, '($1');
            }

            e.target.value = valor;
        });
    </script>
</body>
</html>
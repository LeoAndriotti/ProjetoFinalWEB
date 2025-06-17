<?php
include_once './config/config.php';
include_once './classes/Usuario.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = new Usuario($db);
    $nome = $_POST['nome'];
    $sexo = $_POST['sexo'];
    $fone = $_POST['fone'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $usuario->criar($nome, $sexo, $fone, $email, $senha);
    header('Location: portal.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Usuário - CSL Times</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">

</head>
<body class="portal-body">
    <div class="portal-header portal-header-portal">
        <div class="portal-logo">CSL Times</div>
        <div class="portal-header-content">
            <h1>Adicionar Novo Usuário</h1>
            <div class="portal-nav">
                <a href="portal.php"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form method="POST" class="portal-form">
            <div class="form-header">
                <i class="fa-solid fa-user-plus"></i>
                <h2>Cadastro de Usuário</h2>
            </div>

            <div class="form-group">
                <label for="nome"><i class="fas fa-user"></i> Nome:</label>
                <input type="text" name="nome" id="nome" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-venus-mars"></i> Sexo:</label>
                <div class="form-row">
                    <label for="masculino">
                        <input type="radio" id="masculino" name="sexo" value="M" required>
                        <i class="fa-solid fa-mars"></i> Masculino
                    </label>
                    <label for="feminino">
                        <input type="radio" id="feminino" name="sexo" value="F" required>
                        <i class="fa-solid fa-venus"></i> Feminino
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="fone"><i class="fas fa-phone"></i> Telefone:</label>
                <input type="text" name="fone" id="fone" required>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="senha"><i class="fas fa-lock"></i> Senha:</label>
                <input type="password" name="senha" id="senha" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-plus"></i> Adicionar Usuário
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
<?php
include_once './config/config.php';  
include_once './classes/Noticias.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noticias = new Noticias($db);
    $titulo = $_POST['titulo'];
    $noticia = $_POST['noticia'];
    $data = $_POST['data'];
    $autor = $_POST['autor'];
    $categoria_id = $_POST['categoria'];
    $imagem = '';

    // Prioriza upload
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_extension = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_path)) {
            $imagem = $new_filename;
        }
    } elseif (!empty($_POST['imagem_url'])) {
        // Se não houver upload, usa a URL
        $imagem = trim($_POST['imagem_url']);
    }

    $noticias->criar($titulo, $noticia, $data, $autor, $imagem, $categoria_id);
    header('Location: portal.php');
    exit();
}

$sql = "SELECT id, nome FROM usuarios ORDER BY nome ASC";
$stmt = $db->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlCat = "SELECT id,nome FROM categorias ORDER BY nome ASC";
$stmt = $db->query($sqlCat);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./uploads/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <div class="portal-header">
    <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img" style="width: 140px; height: 120px;">

        <div class="portal-nav">
            <a href="portal.php"><i class="fas fa-arrow-left"></i> Voltar ao Portal</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </div>

    <div class="portal-container">
        <div class="form-card">
            <form method="POST" class="portal-form" enctype="multipart/form-data">
                <div class="form-header">
                    <i class="fas fa-newspaper"></i>
                    <h2>Nova Notícia</h2>
                </div>

                <div class="form-group">
                    <label for="titulo">
                        <i class="fas fa-heading"></i> Título
                    </label>
                    <input type="text" name="titulo" id="titulo" placeholder="Digite o título da notícia" required>
                </div>

                <div class="form-group">
                    <label for="noticia">
                        <i class="fas fa-align-left"></i> Conteúdo
                    </label>
                    <textarea name="noticia" id="noticia" rows="8" placeholder="Digite o conteúdo da notícia" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="data">
                            <i class="fas fa-calendar"></i> Data
                        </label>
                        <input type="date" name="data" id="data" required>
                    </div>

                    <div class="form-group">
                        <label for="autor">
                            <i class="fas fa-user"></i> Autor
                        </label>
                        <select name="autor" id="autor" required>
                            <option value="">Selecione um autor</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= htmlspecialchars($usuario['id']) ?>">
                                    <?= htmlspecialchars($usuario['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="categoria">
                            <i class="fas fa-list"></i> Categoria
                        </label>
                        <select name="categoria" id="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categorias): ?>
                                <option value="<?= htmlspecialchars($categorias['id']) ?>">
                                    <?= htmlspecialchars($categorias['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagem">
                        <i class="fas fa-image"></i> Imagem de Capa (upload ou URL)
                    </label>
                    <div class="file-upload">
                        <input type="file" name="imagem" id="imagem" accept="image/*">
                        <label for="imagem" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Escolher arquivo</span>
                        </label>
                    </div>
                    <div style="margin-top: 1rem;">
                        <input type="url" name="imagem_url" id="imagem_url" placeholder="Ou cole a URL da imagem aqui" style="width:100%;padding:0.8rem;border-radius:5px;border:1px solid #e0e0e0;">
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus"></i> Publicar Notícia
                </button>
            </form>
        </div>
    </div>
</body>
</html>
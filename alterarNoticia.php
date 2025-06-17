<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

include_once 'config/config.php';
include_once 'classes/Noticias.php';
include_once 'classes/Categoria.php';
include_once 'classes/Usuario.php';

$database = new Database();
$db = $database->getConnection();

$noticia = new Noticias($db);
$categoria = new Categoria($db);
$usuario = new Usuario($db);

$mensagem = '';
$erro = '';

if (!isset($_GET['id'])) {
    header("Location: portal.php");
    exit();
}

$id = $_GET['id'];
$noticia->id = $id;
$noticia->ler();

if ($noticia->autor_id != $_SESSION['usuario_id']) {
    header("Location: portal.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $noticia->titulo = $_POST['titulo'];
    $noticia->conteudo = $_POST['conteudo'];
    $noticia->categoria_id = $_POST['categoria_id'];
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem = $_FILES['imagem'];
        $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
        $novo_nome = uniqid() . '.' . $extensao;
        $destino = 'uploads/' . $novo_nome;
        
        if (move_uploaded_file($imagem['tmp_name'], $destino)) {
            if ($noticia->imagem && file_exists($noticia->imagem)) {
                unlink($noticia->imagem);
            }
            $noticia->imagem = $destino;
        }
    }
    
    if ($noticia->atualizar($noticia->id, $noticia->titulo, $noticia->conteudo, $noticia->categoria_id, $noticia->imagem)) {
        header("Location: portal.php");
        exit();
    } else {
        $erro = "Erro ao atualizar a notícia.";
    }
}

$categorias = $categoria->lerTodas();
$dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);
$nome_usuario = $dados_usuario ? $dados_usuario['nome'] : '';
function saudacao() {
    $hora = date('H');
    if ($hora >= 6 && $hora < 12) {
        return "Bom dia";
    } elseif ($hora >= 12 && $hora < 18) {
        return "Boa tarde";
    } else {
        return "Boa noite";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia - Portal de Notícias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="uploads/style.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body>
    <div class="portal-header portal-header-portal">
        <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img" style="width: 150px; height: 130px;">
        <div class="portal-header-content">
            <h1><span class="saudacao-portal"><?php echo saudacao(); ?></span>, <?php echo $nome_usuario; ?>!</h1>
            <div class="portal-nav">
                <a href="portal.php"><i class="fas fa-arrow-left"></i> Voltar ao Portal</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="form-container">
            <h2><i class="fas fa-edit"></i> Editar Notícia</h2>
            
            <?php if ($erro): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="news-form">
                <div class="form-group">
                    <label for="titulo"><i class="fas fa-heading"></i> Título</label>
                    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($noticia->titulo); ?>" required>
                </div>

                <div class="form-group">
                    <label for="categoria_id"><i class="fas fa-tag"></i> Categoria</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $noticia->categoria_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="conteudo"><i class="fas fa-align-left"></i> Conteúdo</label>
                    <textarea id="conteudo" name="conteudo" rows="10" required><?php echo htmlspecialchars($noticia->conteudo); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="imagem"><i class="fas fa-image"></i> Imagem</label>
                    <div class="image-upload-container">
                        <?php if ($noticia->imagem): ?>
                            <div class="current-image">
                                <img src="<?php echo htmlspecialchars($noticia->imagem); ?>" alt="Imagem atual" id="current-image-preview">
                                <p>Imagem atual</p>
                            </div>
                        <?php endif; ?>
                        <div class="new-image">
                            <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage(this)">
                            <div class="image-preview" id="image-preview">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Clique para selecionar uma nova imagem</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="portal.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const currentImage = document.getElementById('current-image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                if (currentImage) {
                    currentImage.style.display = 'none';
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = `
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Clique para selecionar uma nova imagem</p>
            `;
            if (currentImage) {
                currentImage.style.display = 'block';
            }
        }
    }
    </script>
</body>
</html>

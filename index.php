<?php
session_start();
include_once 'config/config.php';
include_once 'classes/Usuario.php';
include_once 'classes/Noticias.php';
include_once 'classes/Categoria.php';

$usuario = new Usuario($db);
$noticias = new Noticias($db);
$categoria = new Categoria($db);

$todas_noticias = $noticias->ler();

// Buscar as 5 últimas notícias
$ultimas_noticias = $db->query("SELECT * FROM noticias ORDER BY data DESC, id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$erro_login = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrar'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Buscar usuário pelo email
    $usuario_data = $usuario->buscarPorEmail($email);

    if ($usuario_data && password_verify($senha, $usuario_data['senha'])) {
        $_SESSION['usuario_id'] = $usuario_data['id'];
        header('Location: portal.php');
        exit();
    } else {
        $erro_login = 'Email ou senha inválidos';
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSL Times</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body>
    <div class="currency-ticker">
        <div class="ticker-content">
            <div class="ticker-items">
                <div class="ticker-item">
                    <i class="fas fa-dollar-sign"></i>
                    <span class="currency-name">USD</span>
                    <span class="currency-value" id="usd-value">Carregando...</span>
                </div>
                <div class="ticker-item">
                    <i class="fas fa-euro-sign"></i>
                    <span class="currency-name">EUR</span>
                    <span class="currency-value" id="eur-value">Carregando...</span>
                </div>
                <div class="ticker-item">
                    <i class="fab fa-bitcoin"></i>
                    <span class="currency-name">BTC</span>
                    <span class="currency-value" id="btc-value">Carregando...</span>
                </div>
                <div class="ticker-item">
                    <i class="fas fa-pound-sign"></i>
                    <span class="currency-name">GBP</span>
                    <span class="currency-value" id="gbp-value">Carregando...</span>
                </div>
            </div>
            <button class="login-btn" onclick="openModal()">Entrar</button>
        </div>
    </div>

    <header class="main-header">
        <div class="header-content">
          
        </div>
    </header>

    <main class="news-container">
        <section class="featured-news" style="text-align:center;">
            <img src="./assets/img/logo2.png" alt="Logo CSL Times" class="logo-img" style="display:block;margin:0 auto 10px auto;max-width:250px;">
            <h2>CSL Times - Your window to the world!</h2>
            <?php if (empty($todas_noticias)): ?>
                <div class="empty-state">
                    <p>Publique a sua notícia, acessando o portal!</p>
                </div>
            <?php else: ?>
                <div class="news-grid">
                    <?php foreach ($todas_noticias as $noticia): ?>
                        <article class="news-card" onclick="openNoticiaModal(<?php echo htmlspecialchars(json_encode($noticia)); ?>, '<?php echo htmlspecialchars($usuario->lerPorId($noticia['autor'])['nome'] ?? 'Autor desconhecido'); ?>', '<?php echo htmlspecialchars($categoria->lerPorId($noticia['categoria'])['nome'] ?? 'Sem categoria'); ?>')">
                            <?php if (!empty($noticia['imagem'])): ?>
                                <div class="news-image">
                                    <?php
                                    $img = ltrim($noticia['imagem'], '@');
                                    if (strpos($img, 'http') === 0) {
                                        $src = $img;
                                    } else {
                                        $src = 'uploads/' . $img;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <h3 class="news-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <p class="news-excerpt"><?php echo htmlspecialchars(substr($noticia['noticia'], 0, 150)) . '...'; ?></p>
                                <div class="news-meta">
                                    <div class="news-meta-top">
                                        <span class="news-author">
                                            <i class="fas fa-user"></i>
                                            <?php 
                                                $autor = $usuario->lerPorId($noticia['autor']);
                                                echo htmlspecialchars($autor['nome'] ?? 'Autor desconhecido');
                                            ?>
                                        </span>
                                        <span class="news-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($noticia['data'])); ?>
                                        </span>
                                    </div>
                                    <span class="news-category">
                                        <i class="fas fa-tag"></i>
                                        <?php 
                                            $cat = $categoria->lerPorId($noticia['categoria']);
                                            echo htmlspecialchars($cat['nome'] ?? 'Sem categoria');
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Exibir as 5 últimas notícias -->
        <section class="ultimas-noticias">
            <h2>Últimas Notícias</h2>
            <?php if (!empty($ultimas_noticias)): ?>
                <div class="news-grid">
                    <?php foreach ($ultimas_noticias as $noticia): ?>
                        <article class="news-card" onclick="openNoticiaModal(<?php echo htmlspecialchars(json_encode($noticia)); ?>, '<?php echo htmlspecialchars($usuario->lerPorId($noticia['autor'])['nome'] ?? 'Autor desconhecido'); ?>', '<?php echo htmlspecialchars($categoria->lerPorId($noticia['categoria'])['nome'] ?? 'Sem categoria'); ?>')">
                            <?php if (!empty($noticia['imagem'])): ?>
                                <div class="news-image">
                                    <?php
                                    $img = ltrim($noticia['imagem'], '@');
                                    if (strpos($img, 'http') === 0) {
                                        $src = $img;
                                    } else {
                                        $src = 'uploads/' . $img;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <h3 class="news-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <p class="news-excerpt"><?php echo htmlspecialchars(substr($noticia['noticia'], 0, 150)) . '...'; ?></p>
                                <div class="news-meta">
                                    <div class="news-meta-top">
                                        <span class="news-author">
                                            <i class="fas fa-user"></i>
                                            <?php 
                                                $autor = $usuario->lerPorId($noticia['autor']);
                                                echo htmlspecialchars($autor['nome'] ?? 'Autor desconhecido');
                                            ?>
                                        </span>
                                        <span class="news-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($noticia['data'])); ?>
                                        </span>
                                    </div>
                                    <span class="news-category">
                                        <i class="fas fa-tag"></i>
                                        <?php 
                                            $cat = $categoria->lerPorId($noticia['categoria']);
                                            echo htmlspecialchars($cat['nome'] ?? 'Sem categoria');
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <div class="modal" id="loginModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required placeholder="Seu email">
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" required placeholder="Sua senha">
                </div>
                <button type="submit" name="entrar" class="submit-btn">Entrar</button>
            </form>
            <div class="register-link">
                <p>Não tem uma conta? <a href="./registrar.php">Registre-se aqui</a></p>
            </div>
            
            <?php if (!empty($erro_login)): ?>
                <div class="login-error"><?php echo $erro_login; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal da Notícia -->
    <div class="modal-noticia" id="noticiaModal">
        <div class="modal-noticia-content">
            <span class="close-modal-noticia" onclick="closeNoticiaModal()">&times;</span>
            <div class="modal-noticia-header">
                <h2 id="modal-noticia-titulo"></h2>
                <div class="modal-noticia-meta">
                    <div class="modal-noticia-date">
                        <i class="fas fa-calendar"></i>
                        <span id="modal-noticia-data"></span>
                    </div>
                    <div class="modal-noticia-author">
                        <i class="fas fa-user"></i>
                        <span id="modal-noticia-autor"></span>
                    </div>
                    <div class="modal-noticia-category">
                        <i class="fas fa-tag"></i>
                        <span id="modal-noticia-categoria"></span>
                    </div>
                </div>
            </div>
            <div class="modal-noticia-image" id="modal-noticia-imagem-container" style="display: none;">
                <img id="modal-noticia-imagem" src="" alt="">
            </div>
            <div class="modal-noticia-body">
                <p id="modal-noticia-conteudo"></p>
            </div>
        </div>
    </div>

    <footer class="footer-main">
        <div class="social-links">
            <a href="https://br.linkedin.com" class="linkedin" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
            <a href="https://pt-br.facebook.com" class="facebook" title="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="https://www.instagram.com" class="instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://www.youtube.com/?gl=BR" class="youtube" title="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="https://x.com/" class="twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> CSL Times. Todos os direitos reservados.
        </div>
    </footer>

    <script>
        async function updateCurrencies() {
            try {
                const response = await fetch('https://economia.awesomeapi.com.br/json/last/USD-BRL,EUR-BRL,BTC-BRL,GBP-BRL');
                const data = await response.json();
                
                document.getElementById('usd-value').textContent = `R$ ${parseFloat(data.USDBRL.bid).toFixed(2)}`;
                document.getElementById('eur-value').textContent = `R$ ${parseFloat(data.EURBRL.bid).toFixed(2)}`;
                document.getElementById('btc-value').textContent = `R$ ${parseFloat(data.BTCBRL.bid).toFixed(2)}`;
                document.getElementById('gbp-value').textContent = `R$ ${parseFloat(data.GBPBRL.bid).toFixed(2)}`;
            } catch (error) {
                console.error('Erro ao buscar cotações:', error);
                document.querySelectorAll('.currency-value').forEach(el => {
                    el.textContent = 'Erro ao carregar';
                });
            }
        }

        updateCurrencies();
        setInterval(updateCurrencies, 300000);

        function openModal() {
            document.getElementById('loginModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('loginModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('loginModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Funções para o modal da notícia
        function openNoticiaModal(noticia, autor, categoria) {
            // Preencher o modal com os dados da notícia
            document.getElementById('modal-noticia-titulo').textContent = noticia.titulo;
            
            // Formatar a data no formato brasileiro
            const data = new Date(noticia.data);
            const dataFormatada = data.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            document.getElementById('modal-noticia-data').textContent = dataFormatada;
            
            document.getElementById('modal-noticia-autor').textContent = autor;
            document.getElementById('modal-noticia-categoria').textContent = categoria;
            document.getElementById('modal-noticia-conteudo').textContent = noticia.noticia;
            
            // Configurar a imagem se existir
            const imagemContainer = document.getElementById('modal-noticia-imagem-container');
            const imagem = document.getElementById('modal-noticia-imagem');
            
            if (noticia.imagem && noticia.imagem.trim() !== '') {
                let imgSrc = noticia.imagem;
                if (imgSrc.startsWith('@')) {
                    imgSrc = imgSrc.substring(1);
                }
                if (!imgSrc.startsWith('http')) {
                    imgSrc = 'uploads/' + imgSrc;
                }
                imagem.src = imgSrc;
                imagem.alt = noticia.titulo;
                imagemContainer.style.display = 'flex';
            } else {
                imagemContainer.style.display = 'none';
            }
            
            // Exibir o modal
            document.getElementById('noticiaModal').style.display = 'flex';
        }

        function closeNoticiaModal() {
            document.getElementById('noticiaModal').style.display = 'none';
        }

        // Fechar modal da notícia ao clicar fora dele
        window.addEventListener('click', function(event) {
            const noticiaModal = document.getElementById('noticiaModal');
            if (event.target === noticiaModal) {
                closeNoticiaModal();
            }
        });

        // Fechar modal da notícia com ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
                closeNoticiaModal();
            }
        });
    </script>
</body>
</html>
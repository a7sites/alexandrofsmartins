<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Login</title>
    <link rel="stylesheet" href="css/painel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="login-bg">
    <div class="login-container">
        <div class="login-main">
            <div class="login-logo-box">
                <img src="imgs/a7site.svg" alt="Logo A7 Sites">
                <div class="desc">
                    Somos uma Empresa de criação de sites que é referência nacional!<br>
                    Faça seu site responsivo conosco e conquiste seu sucesso online!
                </div>
            </div>
            <div class="login-box">
                <h2>Administração do Usuário</h2>
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php endif; ?>
                <form action="login.php" method="POST" autocomplete="off">
                    <div class="form-group">
                        <input type="text" id="usuario" name="usuario" required autocomplete="username" placeholder="Usuário">
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" id="senha" name="senha" required autocomplete="current-password" placeholder="Senha">
                            <span class="toggle-password" onclick="togglePassword()"><i class="fa fa-eye" id="eye"></i></span>
                        </div>
                    </div>
                    <button type="submit" class="btn-login">Acessar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="login-footer">
        Precisa de ajuda? <a href="index.php" style="color:#6c2c8f;text-decoration:underline;">Visite nosso site</a>
    </div>
    <script>
        function togglePassword() {
            const input = document.getElementById('senha');
            const eye = document.getElementById('eye');
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
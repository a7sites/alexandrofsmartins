<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Login A7 Sites</title>
    <link rel="stylesheet" href="css/painel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body,
        input,
        button,
        select,
        textarea {
            font-family: 'Josefin Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            font-weight: 200;
        }

        body.login-bg {
            background: url('../imgs/bg.painel.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
            min-height: 100vh;
        }

        .login-logo-box .desc {
            color: #fff !important;
            font-weight: 200;
        }

        .login-box {
            background: #310248 !important;
            border: 2px solid rgba(255, 255, 255, 0.35) !important;
            box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.12) !important;
        }

        .login-box h2 {
            color: #fff !important;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            background: rgba(255, 255, 255, 0.19) !important;
            backdrop-filter: blur(5px) !important;
            color: #fff !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        .login-box input[type="text"]::placeholder,
        .login-box input[type="password"]::placeholder {
            color: #fff !important;
            opacity: 0.7;
        }

        .login-box .input-group .toggle-password i {
            color: #fff !important;
        }
    </style>
</head>

<body class="login-bg">
    <div class="login-container" style="background:rgba(255, 255, 255, 0.11); backdrop-filter: blur(5px);">
        <div class="login-main">
            <div class="login-logo-box">
                <img src="imgs/logo.painel.svg" alt="Logo A7 Sites">
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
        Precisa de ajuda? <a href="index.html" style="color:#6c2c8f;text-decoration:underline;">Visite nosso site</a>
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
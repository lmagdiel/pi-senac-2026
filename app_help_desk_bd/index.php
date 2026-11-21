
<?php
$cadastroSucesso = isset($_GET['usuario']) && $_GET['usuario'] === 'sucesso';
$erroLogin = $_GET['login'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login | App Help Desk</title>
<link rel="icon" href="imagens/logo.png" type="image/x-icon">

<link rel="stylesheet" href="css/index.css">
</head>

<body>

<div class="login-container">

    <!-- LADO ESQUERDO -->
    <section class="login-visual">
        <div class="visual-content">

            <div class="brand-icon">
                <img src="imagens/logo.png" alt="Logo Help Desk">
            </div>

            <h1>Help Desk</h1>

            <p class="subtitle">
                Suporte e atendimento<br>
                de chamados
            </p>

            <div class="beneficios">

                <div class="beneficio">
                    <span class="check">
                        <svg viewBox="0 0 24 24">
                            <path d="m5 12 4 4L19 6"/>
                        </svg>
                    </span>
                    Abra e acompanhe chamados
                </div>

                <div class="beneficio">
                    <span class="check">
                        <svg viewBox="0 0 24 24">
                            <path d="m5 12 4 4L19 6"/>
                        </svg>
                    </span>
                    Solicite suporte técnico
                </div>

                <div class="beneficio">
                    <span class="check">
                        <svg viewBox="0 0 24 24">
                            <path d="m5 12 4 4L19 6"/>
                        </svg>
                    </span>
                    Gerencie atendimentos
                </div>

            </div>
        </div>
    </section>

    <!-- LADO DIREITO -->
    <section class="login-form-area">

        <div class="form-content">

            <div class="form-logo">
                <svg viewBox="0 0 24 24">
                    <path d="M4 13v-2a8 8 0 0 1 16 0v2"/>
                    <path d="M4 12H3a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h2v-7H4z"/>
                    <path d="M20 12h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2v-7h1z"/>
                    <path d="M20 18c0 2-2 3-5 3h-3"/>
                </svg>
            </div>

            <h2 class="form-title">Acesse sua conta</h2>

            <p class="form-subtitle">
                Informe seu e-mail e senha para continuar
            </p>

            <?php if($cadastroSucesso): ?>
                <div class="mensagem sucesso">
                    Usuário cadastrado com sucesso!
                </div>
            <?php endif; ?>

            <?php if($erroLogin === 'erro'): ?>
                <div class="mensagem erro">
                    Usuário ou senha inválido(s)!
                </div>
            <?php endif; ?>

            <?php if($erroLogin === 'erro2'): ?>
                <div class="mensagem erro">
                    Login obrigatório!
                </div>
            <?php endif; ?>

            <form action="valida_login.php" method="GET">

                <div class="form-group">
                    <label class="form-label" for="email">E-mail</label>

                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <path d="m3 7 9 6 9-6"/>
                        </svg>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="Digite seu e-mail"
                            autocomplete="email"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="senha">Senha</label>

                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="5" y="10" width="14" height="11" rx="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                        </svg>

                        <input
                            id="senha"
                            name="senha"
                            type="password"
                            placeholder="Digite sua senha"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                            aria-label="Mostrar senha"
                            id="toggleSenha"
                        >
                            <svg viewBox="0 0 24 24">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button class="btn-login" type="submit">
                    Entrar
                </button>

            </form>

            <div class="cadastro-link">
                Não tem uma conta?
                <a href="cadastro.php">Cadastre-se</a>
            </div>

        </div>
    </section>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('senha');
    const botao = document.getElementById('toggleSenha');

    if (input.type === 'password') {
        input.type = 'text';
        botao.setAttribute('aria-label', 'Ocultar senha');
    } else {
        input.type = 'password';
        botao.setAttribute('aria-label', 'Mostrar senha');
    }
}
</script>

</body>
</html>

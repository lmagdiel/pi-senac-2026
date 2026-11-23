
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

<style>
:root {
    --azul: #0867d5;
    --azul-escuro: #0b376d;
    --texto: #142b4a;
    --muted: #697c94;
    --borda: #dbe5f2;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    font-family: Arial, Helvetica, sans-serif;
    color: var(--texto);
    background: linear-gradient(135deg, #eaf3ff, #f8fbff);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.login-container {
    width: 100%;
    max-width: 1100px;
    min-height: 630px;
    display: grid;
    grid-template-columns: 48% 52%;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 18px 55px rgba(24, 65, 125, .14);
}

/* ÁREA ESQUERDA */

.login-visual {
    position: relative;
    background:
    linear-gradient(135deg, rgba(4, 35, 75, 0.35), rgba(5, 93, 185, 0.25)),
    url("imagens/login-bg.png") center center / cover no-repeat;
    color: #fff;
    padding: 55px 45px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
}

.login-visual::before {
    content: "";
    position: absolute;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,.14);
    top: -160px;
    right: -150px;
}

.login-visual::after {
    content: "";
    position: absolute;
    width: 270px;
    height: 270px;
    border-radius: 50%;
    background: rgba(255,255,255,.045);
    bottom: -145px;
    left: -110px;
}

.visual-content {
    position: relative;
    z-index: 1;
}

.brand-icon {
    width: 66px;
    height: 66px;
    margin-bottom: 20px;
}

.brand-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.login-visual h1 {
    font-size: 34px;
    line-height: 1.2;
    margin: 0 0 10px;
}

.login-visual .subtitle {
    font-size: 16px;
    line-height: 1.6;
    color: #e2efff;
    max-width: 310px;
    margin-bottom: 36px;
}

.beneficios {
    display: flex;
    flex-direction: column;
    gap: 19px;
}

.beneficio {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #edf5ff;
}

.check {
    width: 23px;
    height: 23px;
    flex: none;
    border-radius: 50%;
    background: #1680f4;
    display: grid;
    place-items: center;
}

.check svg {
    width: 14px;
    height: 14px;
    fill: none;
    stroke: white;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ÁREA DIREITA */

.login-form-area {
    padding: 60px 55px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.form-content {
    width: 100%;
    max-width: 365px;
}

.form-logo {
    width: 64px;
    height: 64px;
    margin: 0 auto 22px;
    border-radius: 18px;
    background: #eaf3ff;
    color: var(--azul);
    display: grid;
    place-items: center;
}

.form-logo svg {
    width: 38px;
    height: 38px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.form-title {
    text-align: center;
    font-size: 25px;
    font-weight: 700;
    margin: 0 0 8px;
}

.form-subtitle {
    text-align: center;
    color: var(--muted);
    font-size: 13px;
    margin: 0 0 32px;
}

.form-group {
    margin-bottom: 17px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 8px;
}

.input-wrapper {
    display: flex;
    align-items: center;
    border: 1px solid var(--borda);
    border-radius: 9px;
    background: #fbfdff;
    transition: .2s;
}

.input-wrapper:focus-within {
    border-color: #1680f4;
    box-shadow: 0 0 0 3px rgba(8,103,213,.1);
}

.input-icon {
    width: 19px;
    height: 19px;
    margin-left: 14px;
    flex: none;
    fill: none;
    stroke: #7d91ac;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.input-wrapper input {
    width: 100%;
    min-width: 0;
    height: 46px;
    padding: 0 12px;
    border: 0;
    outline: none;
    background: transparent;
    color: var(--texto);
    font: inherit;
    font-size: 14px;
}

.input-wrapper input::placeholder {
    color: #94a2b6;
}

.password-toggle {
    border: 0;
    background: transparent;
    color: #8496ae;
    padding: 9px 13px;
    cursor: pointer;
    display: grid;
    place-items: center;
}

.password-toggle svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.btn-login {
    display: block;
    width: 100%;
    height: 48px;
    margin-top: 24px;
    border: 0;
    border-radius: 9px;
    background: linear-gradient(90deg, #0759c4, #0878ee);
    color: white;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
}

.btn-login:hover {
    background: linear-gradient(90deg, #064ca8, #0869d4);
    box-shadow: 0 6px 16px rgba(8,103,213,.2);
}

.cadastro-link {
    text-align: center;
    margin-top: 24px;
    font-size: 13px;
    color: var(--muted);
}

.cadastro-link a {
    color: var(--azul);
    text-decoration: none;
    font-weight: 700;
}

.cadastro-link a:hover {
    text-decoration: underline;
}

.mensagem {
    padding: 12px 14px;
    margin-bottom: 18px;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.5;
}

.mensagem.sucesso {
    background: #e8f8f0;
    color: #167b4a;
}

.mensagem.erro {
    background: #fff0f0;
    color: #c43d3d;
}

@media(max-width: 800px) {
    .login-container {
        grid-template-columns: 42% 58%;
    }

    .login-visual {
        padding: 35px 25px;
    }

    .login-form-area {
        padding: 40px 28px;
    }

    .login-visual h1 {
        font-size: 27px;
    }
}

@media(max-width: 650px) {
    body {
        padding: 14px;
    }

    .login-container {
        display: flex;
        flex-direction: column;
        min-height: auto;
    }

    .login-visual {
        padding: 25px;
    }

    .login-visual h1 {
        font-size: 24px;
    }

    .login-visual .subtitle {
        margin-bottom: 0;
        font-size: 13px;
    }

    .brand-icon {
        width: 45px;
        height: 45px;
        margin-bottom: 10px;
    }

    .beneficios {
        display: none;
    }

    .login-form-area {
        padding: 38px 24px 42px;
    }
}
</style>
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

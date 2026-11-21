
<?php
$erroEmail = ($_GET['email'] ?? '') === 'erro';
$erroPerfil = ($_GET['validaperfil'] ?? '') === 'erro';
$cadastro = $_GET['usuario'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cadastro | App Help Desk</title>
<link rel="icon" href="imagens/logo.png" type="image/x-icon">

<style>
:root {
    --azul: #0867d5;
    --azul-escuro: #0b376d;
    --texto: #142b4a;
    --muted: #697c94;
    --borda: #dbe5f2;
}
* { box-sizing: border-box; }

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

.cadastro-container {
    width: 100%;
    max-width: 1100px;
    min-height: 630px;
    display: grid;
    grid-template-columns: 48% 52%;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 18px 55px rgba(24,65,125,.14);
}

/* LADO ILUSTRATIVO */

.cadastro-visual {
    position: relative;
    background: linear-gradient(145deg, #c5ddff, #e8f3ff);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    overflow: hidden;
}

.cadastro-visual::before {
    content: "";
    position: absolute;
    width: 540px;
    height: 540px;
    border-radius: 50%;
    background: rgba(255,255,255,.58);
    top: -260px;
    right: -240px;
}

.cadastro-visual::after {
    content: "";
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: rgba(67,145,240,.15);
    bottom: -220px;
    left: -190px;
}

.visual-content {
    position: relative;
    z-index: 1;
    width: 100%;
    text-align: center;
}

.illustration {
    width: 100%;
    max-width: 390px;
    margin: auto;
}

.illustration svg {
    display: block;
    width: 100%;
    height: auto;
}

.visual-title {
    margin: 25px 0 10px;
    font-size: 24px;
    font-weight: 700;
    color: #174a89;
}

.visual-subtitle {
    font-size: 14px;
    line-height: 1.6;
    color: #55769f;
    max-width: 310px;
    margin: 0 auto;
}

/* LADO DO FORMULÁRIO */

.cadastro-form-area {
    padding: 45px 55px;
    display: flex;
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
    margin: 0 auto 19px;
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
    margin: 0 0 28px;
}

.form-group {
    margin-bottom: 16px;
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

.input-wrapper input,
.input-wrapper select {
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

.input-wrapper select {
    cursor: pointer;
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

.btn-cadastro {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
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

.btn-cadastro:hover {
    background: linear-gradient(90deg, #064ca8, #0869d4);
    box-shadow: 0 6px 16px rgba(8,103,213,.2);
}

.btn-cadastro svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.login-link {
    text-align: center;
    margin-top: 24px;
    font-size: 13px;
    color: var(--muted);
}

.login-link a {
    color: var(--azul);
    text-decoration: none;
    font-weight: 700;
}

.login-link a:hover {
    text-decoration: underline;
}

.mensagem {
    padding: 12px 14px;
    margin-bottom: 17px;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.5;
}

.mensagem.erro {
    background: #fff0f0;
    color: #c43d3d;
}

.mensagem.sucesso {
    background: #e8f8f0;
    color: #167b4a;
}

@media(max-width: 800px) {
    .cadastro-container {
        grid-template-columns: 42% 58%;
    }

    .cadastro-visual {
        padding: 25px;
    }

    .cadastro-form-area {
        padding: 35px 28px;
    }
}

@media(max-width: 650px) {
    body {
        padding: 14px;
    }

    .cadastro-container {
        display: flex;
        flex-direction: column;
    }

    .cadastro-visual {
        padding: 25px;
    }

    .illustration {
        max-width: 210px;
    }

    .visual-title {
        font-size: 20px;
        margin-top: 10px;
    }

    .visual-subtitle {
        font-size: 12px;
    }

    .cadastro-form-area {
        padding: 35px 24px 42px;
    }
}
</style>
</head>

<body>

<div class="cadastro-container">

    <!-- LADO ESQUERDO -->
    <section class="cadastro-visual">
        <div class="visual-content">

            <div class="illustration">
                <svg viewBox="0 0 460 350" xmlns="http://www.w3.org/2000/svg" aria-label="Ilustração de computador e atendimento">
                    <ellipse cx="230" cy="308" rx="185" ry="19" fill="#9bc5f6" opacity=".45"/>

                    <!-- Planta -->
                    <path d="M365 270 Q340 210 362 160" fill="none" stroke="#337bd0" stroke-width="5"/>
                    <path d="M364 229 Q399 193 400 171 Q360 175 364 229Z" fill="#4e99e9"/>
                    <path d="M355 210 Q319 183 318 153 Q358 166 355 210Z" fill="#75b2f4"/>
                    <path d="M362 183 Q380 142 371 115 Q347 143 362 183Z" fill="#317cdb"/>
                    <path d="M344 269 L390 269 L381 310 L353 310Z" fill="#6aa9ed"/>
                    <path d="M344 269 L390 269 L385 278 L349 278Z" fill="#3987d9"/>

                    <!-- Notebook -->
                    <rect x="83" y="98" width="257" height="170" rx="13" fill="#174b91"/>
                    <rect x="94" y="109" width="235" height="146" rx="5" fill="#e7f3ff"/>
                    <path d="M102 232 Q220 115 321 229" fill="none" stroke="#c4dfff" stroke-width="17" opacity=".7"/>

                    <!-- Headset na tela -->
                    <path d="M177 175v-10a46 46 0 0 1 92 0v10" fill="none" stroke="#0867d5" stroke-width="10" stroke-linecap="round"/>
                    <rect x="166" y="171" width="22" height="46" rx="9" fill="#0867d5"/>
                    <rect x="258" y="171" width="22" height="46" rx="9" fill="#0867d5"/>
                    <path d="M270 214 Q269 232 245 232" fill="none" stroke="#0867d5" stroke-width="6" stroke-linecap="round"/>
                    <circle cx="241" cy="232" r="6" fill="#0867d5"/>

                    <!-- Base do notebook -->
                    <path d="M66 269H356L379 292Q381 299 370 302H55Q45 299 50 292Z" fill="#438fe1"/>
                    <path d="M66 269H356L367 285H55Z" fill="#b3d5ff"/>
                    <path d="M187 285H244L251 293H179Z" fill="#6aa7e9"/>

                    <!-- Folhas decorativas -->
                    <path d="M54 283 Q38 245 56 218 Q77 246 54 283Z" fill="#5ca4ed"/>
                    <path d="M55 280 Q24 265 17 242 Q52 240 55 280Z" fill="#2f82d8"/>
                    <path d="M63 284 Q83 250 104 243 Q93 280 63 284Z" fill="#7db7f4"/>
                </svg>
            </div>

            <h2 class="visual-title">Bem-vindo ao Help Desk</h2>
            <p class="visual-subtitle">
                Crie sua conta e tenha acesso a uma experiência de suporte mais organizada.
            </p>

        </div>
    </section>

    <!-- LADO DIREITO -->
    <section class="cadastro-form-area">

        <div class="form-content">

            <div class="form-logo">
                <svg viewBox="0 0 24 24">
                    <path d="M4 13v-2a8 8 0 0 1 16 0v2"/>
                    <path d="M4 12H3a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h2v-7H4z"/>
                    <path d="M20 12h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2v-7h1z"/>
                    <path d="M20 18c0 2-2 3-5 3h-3"/>
                </svg>
            </div>

            <h1 class="form-title">Criar sua conta</h1>
            <p class="form-subtitle">
                Preencha os dados para se cadastrar
            </p>

            <?php if($erroEmail): ?>
                <div class="mensagem erro">E-mail já cadastrado!</div>
            <?php endif; ?>

            <?php if($erroPerfil): ?>
                <div class="mensagem erro">Obrigatório selecionar um perfil!</div>
            <?php endif; ?>

            <?php if($cadastro === 'sucesso'): ?>
                <div class="mensagem sucesso">
                    Usuário cadastrado com sucesso!
                </div>
            <?php elseif($cadastro !== ''): ?>
                <div class="mensagem erro">
                    Erro ao inserir usuário no banco. Contate o administrador.
                </div>
            <?php endif; ?>

            <form action="valida_cadastro.php" method="POST">

                <div class="form-group">
                    <label class="form-label" for="nome">Nome completo</label>

                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                        </svg>

                        <input
                            id="nome"
                            name="nome"
                            type="text"
                            placeholder="Digite seu nome completo"
                            autocomplete="name"
                            required
                            autofocus
                        >
                    </div>
                </div>

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
                            placeholder="Crie uma senha"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="toggleSenha"
                            onclick="togglePassword()"
                            aria-label="Mostrar senha"
                        >
                            <svg viewBox="0 0 24 24">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="perfil">Perfil</label>

                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <circle cx="9" cy="8" r="3"/>
                            <path d="M3 20v-2a6 6 0 0 1 12 0v2M17 11h4M19 9v4"/>
                        </svg>

                        <select id="perfil" name="perfil" required>
                            <option value="" selected disabled>Selecione seu perfil</option>
                            <option value="Usuario">Usuário</option>
                            <option value="Tecnico">Técnico</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                </div>

                <button class="btn-cadastro" type="submit">
                    <svg viewBox="0 0 24 24">
                        <circle cx="9" cy="8" r="3"/>
                        <path d="M3 20v-2a6 6 0 0 1 12 0v2M17 11h4M19 9v4"/>
                    </svg>
                    Cadastrar
                </button>

            </form>

            <div class="login-link">
                Já tem uma conta?
                <a href="index.php">Faça login</a>
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

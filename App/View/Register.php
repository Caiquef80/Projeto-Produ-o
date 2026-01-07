
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
</head>
<body>
    <div class="container">
        <h1>Cadastre-se aqui</h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="suporte.php">Suporte</a></li>
            </ul>
        </nav>
        <div class="register">
            <form action="../Controller/UserController.php" method="POST">
                <label for="name">Nome:</label>
                <input type="text" id="name" name="name" placeholder="Digite seu nome" class="input-style">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Digite seu email" class="input-style">

                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" class="input-style">

                <input type="submit" value="Registrar">
                <?php if(isset($mensagem)){
                    echo htmlspecialchars($mensagem['message']);
                    Header('Location: ../View/Login.php');
                    }?>
            </form>
            <p><a href="Login.php">Já possui uma conta? Clique aqui</a></p>
        </div>
    </div>
</body>
</html>

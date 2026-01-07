
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="suporte.php">Suporte</a></li>
                <li><a href="Register.php">Registrar-se</a></li>
            </ul>
        </nav>
        <div class="login">
            <form action="../Controller/AuthController.php" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Digite seu email" class="input-style">

                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" class="input-style">

                <input type="submit" value="Entrar">

                <?php if(isset($mensagem)){
                    echo htmlspecialchars($mensagem['message']);
                }?>
            </form>
        </div>
    </div>
</body>
</html>

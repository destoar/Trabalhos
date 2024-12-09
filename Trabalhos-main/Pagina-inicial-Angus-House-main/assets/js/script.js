$(document).ready(function () {
    // Inicializa o carrossel (Bootstrap 4)
    $('.carousel').carousel({
        interval: 3000 // Tempo entre os slides (em milissegundos)
    });

    // Captura o clique do botão "Peça agora!"
    $(".botaoPeca").on("click", function () {
        window.location.href = "pedido.html";  // Redireciona para a página de pedido
    });

    // Verificar se o usuário está logado (usando localStorage)
    const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado"));

    if (usuarioLogado) {
        // Exibir perfil do usuário
        $("#login-link").hide();  // Esconde o link de login
        $("#perfilUsuario").show();  // Exibe a seção de perfil
        $("#nomeUsuarioTexto").text(usuarioLogado.nome);  // Exibe o nome do usuário
    }

    // Logout
    $("#logout").on("click", function () {
        // Remover o usuário logado (simulação)
        alert("Você foi desconectado.");
        localStorage.removeItem("usuarioLogado");  // Limpa o localStorage
        $("#perfilUsuario").hide();  // Ocultar a seção de perfil
        $("#login-link").show();  // Mostrar o link de login novamente
        window.location.href = "index.html"; // Redireciona para a página inicial após logout
    });

    // Login
    $("#formLogin").on("submit", async function (event) {
        event.preventDefault(); // Previne o comportamento padrão do formulário

        const formData = new FormData(this);
        console.log([...formData]); // Verifique os dados antes de enviar

        try {
            const response = await fetch("backend/login.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            console.log(result); // Verifique a resposta recebida

            if (result.status === "success") {
                alert(result.message);
                // Armazenando os dados do usuário no localStorage
                localStorage.setItem("usuarioLogado", JSON.stringify(result.user));
                window.location.href = "index.html"; // Redireciona para a página inicial após login
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Ocorreu um erro ao processar o login. Tente novamente mais tarde.");
            console.error("Erro no login:", error);
        }
    });

    // Cadastro
    $("#formCadastro").on("submit", async function (event) {
        event.preventDefault(); // Previne o comportamento padrão do formulário

        const formData = new FormData(this);

        // Verificar se as senhas coincidem
        const senha = $("#senha").val();
        const confirmarSenha = $("#confirmarSenha").val();

        if (senha !== confirmarSenha) {
            alert("As senhas não coincidem.");
            return;
        }

        try {
            const response = await fetch("backend/cadastrar_cliente.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            console.log(result); // Verifique a resposta recebida

            if (result.status === "success") {
                alert(result.message);
                window.location.href = "login.html"; // Redireciona para a página de login após cadastro
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Ocorreu um erro ao processar o cadastro. Tente novamente mais tarde.");
            console.error("Erro no cadastro:", error);
        }
    });
});

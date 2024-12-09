$(document).ready(function() {
    const formLogin = $('#formLogin');
    
    formLogin.on('submit', async function(e) {
        e.preventDefault();
        
        // Reset de mensagens de erro
        $('.invalid-feedback').hide();
        $('.form-control').removeClass('is-invalid');
        
        try {
            const formData = new FormData(this);
            const response = await fetch('backend/api/login.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                // Armazena dados do usuário
                localStorage.setItem('usuarioLogado', JSON.stringify(data.user));
                
                // Redireciona para a página inicial
                window.location.href = 'index.html';
            } else {
                // Mostra mensagem de erro
                if (data.message.includes('email')) {
                    $('#loginEmail').addClass('is-invalid');
                } else if (data.message.includes('senha')) {
                    $('#loginSenha').addClass('is-invalid');
                }
                alert(data.message);
            }
        } catch (error) {
            console.error('Erro no login:', error);
            alert('Erro ao fazer login. Tente novamente mais tarde.');
        }
    });

    // Validação em tempo real do email
    $('#loginEmail').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
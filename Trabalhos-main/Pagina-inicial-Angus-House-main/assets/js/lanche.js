$(document).ready(function () {
    // Função para atualizar o carrinho
    function atualizarCarrinho() {
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        let total = 0;
        let itensCarrinho = "";

        carrinho.forEach(item => {
            total += item.preco * item.quantidade;
            itensCarrinho += `<p>${item.nome} - R$ ${item.preco.toFixed(2)} x ${item.quantidade}</p>`;
        });

        // Exibe os itens no carrinho
        $('#resumoCarrinho').html(itensCarrinho);
        $('#totalCarrinho').text(`R$ ${total.toFixed(2)}`);
    }

    // Adicionando um item ao carrinho
    $(".botão").click(function () {
        let nome = $(this).data("nome");
        let preco = parseFloat($(this).data("preco"));

        // Carrega o carrinho atual
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

        // Verifica se o item já está no carrinho
        let itemExistente = carrinho.find(item => item.nome === nome);

        if (itemExistente) {
            // Se o item já existe, aumenta a quantidade
            itemExistente.quantidade++;
        } else {
            // Caso contrário, adiciona um novo item
            carrinho.push({
                nome: nome,
                preco: preco,
                quantidade: 1
            });
        }

        // Salva o carrinho atualizado no localStorage
        localStorage.setItem('carrinho', JSON.stringify(carrinho));

        // Atualiza o carrinho na interface
        atualizarCarrinho();
    });

    // Carrega o carrinho ao carregar a página
    atualizarCarrinho();
});

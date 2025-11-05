Quick Menu (Dashboard) - modo de funcionamento

Este arquivo descreve como os botões "Menu Rápido" do `dashboard.php` funcionam e como personalizá-los.

1) Comportamento
- Os botões rápidos foram convertidos para formulários que submetem parâmetros automaticamente ao abrir páginas de consulta.
- `consultaBanco.php` é chamado via POST (mais seguro) com os campos necessários: datainicial, datafinal, tipo, opprod, caixa e enviar=Filtrar.
- `consultaTitulos.php`, `provisoesPagamento.php` e `provisoesRecebimento.php` agora aceitam tanto GET quanto POST com o campo `autofilter=1` e usam o campo `id` (data no formato YYYY-MM-DD) recebido. O dashboard envia via POST por padrão.
- As páginas destino exibem um banner informativo quando os dados são carregados a partir do Dashboard.

- Os botões rápidos agora exibem ícones locais (SVG) e utilizam tooltips do Bootstrap para indicar que os dados serão carregados automaticamente.
 - Os botões rápidos agora exibem ícones locais (SVG) e utilizam tooltips do Bootstrap para indicar que os dados serão carregados automaticamente.
 - Ícones agora são injetados inline (SVG) para permitir colorização via CSS. Os ícones do menu principal têm fundo branco e cor de destaque por aba; os ícones dos botões rápidos têm fundo branco e cor legível para melhor contraste.

2) Como alterar o comportamento
- Para mudar a data padrão usada pelo Dashboard, edite `dashboard.php` (valores do campo hidden `id` ou `datainicial`/`datafinal`).
- Para usar outra caixa/polka, altere a lógica que define `$defaultCaixa` no topo de `dashboard.php`.

3) Segurança
- Usamos POST para `consultaBanco.php` por evitar expor parâmetros no URL. Se desejar todas as ações por POST, posso ajustar as páginas que atualmente usam GET para aceitar apenas POST.

4) Estilização
- Os botões usam a classe `.menu-button` (estilos em `css/style.css`). O ícone pequeno dentro do botão usa `.quick-icon`.

5) Como reverter
- Substitua os formulários por links simples (ancoras) em `dashboard.php` se preferir o comportamento anterior.

Se desejar, posso:
- Forçar todo o fluxo para POST (removendo suporte a GET nas páginas destino).
- Adicionar confirmação visual (tooltip/tooltip Bootstrap) ao passar o mouse nos botões. (Já implementado)
- Registrar logs de uso dos quick-links (para monitorar quais são usados mais).
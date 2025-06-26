         function enviarWhatsapp(event) {

         event.preventDefault(); // Previne o envio do formulário padrão

         const nome = document.getElementById("nome").value;
         const msg = document.getElementById("msg").value;
         const telefone = '5531995999029';

         const texto = `Olá! Meu nome é ${nome}, ${msg}. Você pode me contatar pelo whatsapp?`;
         const msgFormatada = encodeURIComponent(texto);

         const url = `https://wa.me/${telefone}?text=${msgFormatada}`;

         window.open(url, '_blank');

      }
console.log('JS carregado!');

// Função para toggle do menu mobile
function toggleMenu() {
   const menu = document.getElementById('menu_header');
   menu.classList.toggle('active');
}

// Fechar menu mobile ao clicar em um link
function fecharMenu() {
   const menu = document.getElementById('menu_header');
   menu.classList.remove('active');
}

function enviarWhatsapp(event) {
  event.preventDefault();

  const nome = document.getElementById("nome").value.trim();
  const whatsapp = document.getElementById("whatsapp").value.trim();
  const msg = document.getElementById("msg").value.trim();
  const erroBox = document.getElementById("erroMensagem");

  console.log('Clique em Enviar WhatsApp:', { nome, whatsapp, msg });

  if (nome === "" || whatsapp === "" || msg === "") {
    erroBox.classList.add("mostrar");
    setTimeout(() => {
      erroBox.classList.remove("mostrar");
    }, 3000);
    return;
  }

  // Telefone do destinatário fixo (seu número)
  const telefoneDestino = '5531995999029';

  // Enviar dados para o processar_contato.php
  const formData = new FormData();
  formData.append('nome', nome);
  formData.append('whatsapp', whatsapp);
  formData.append('mensagem', msg);

  fetch('processar_contato.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    // Após salvar o contato, redirecionar para o WhatsApp
    const texto = `Olá! Meu nome é ${nome} e meu whatsapp é ${whatsapp}, ${msg}. Você pode me contatar pelo whatsapp ${whatsapp}?`;
    const msgFormatada = encodeURIComponent(texto);
    const url = `https://wa.me/${telefoneDestino}?text=${msgFormatada}`;
    window.open(url, '_blank');
  })
  .catch(error => {
    console.error('Erro ao salvar contato:', error);
    // Mesmo com erro, ainda redireciona para o WhatsApp
    const texto = `Olá! Meu nome é ${nome}, ${msg}. Você pode me contatar pelo whatsapp ${whatsapp}?`;
    const msgFormatada = encodeURIComponent(texto);
    const url = `https://wa.me/${telefoneDestino}?text=${msgFormatada}`;
    window.open(url, '_blank');
  });
}

document.addEventListener("DOMContentLoaded", () => {
  // Header scroll
  let lastScrollTop = 0;
  const header = document.querySelector("header");
  window.addEventListener("scroll", () => {
    const currentScroll = window.scrollY || document.documentElement.scrollTop;

    if (currentScroll > lastScrollTop && currentScroll > 100) {
      header.style.top = "-140px";
    } else {
      header.style.top = "0";
    }
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  });

  // Animação menu_header e btn_falecomigo
  const menu = document.querySelector('.menu_header');
  if(menu) menu.classList.add('bounce-in');

  const btnFaleComigo = document.querySelector('.btn_falecomigo');
  if(btnFaleComigo) btnFaleComigo.classList.add('bounce-in');

  // Fechar menu mobile ao clicar em links do menu
  const menuLinks = document.querySelectorAll('.menu_header a');
  menuLinks.forEach(link => {
    link.addEventListener('click', fecharMenu);
  });

  // Fechar menu mobile ao clicar fora dele
  document.addEventListener('click', (e) => {
    const menu = document.getElementById('menu_header');
    const menuBtn = document.querySelector('.menu_mobile_btn');
    
    if (menu && menu.classList.contains('active') && 
        !menu.contains(e.target) && 
        !menuBtn.contains(e.target)) {
      fecharMenu();
    }
  });

  // Função de debounce para reduzir chamadas excessivas
function debounce(func, delay) {
  let timeout;
  return function () {
    clearTimeout(timeout);
    timeout = setTimeout(func, delay);
  };
}

// Função debounce
function debounce(func, delay) {
  let timeout;
  return function () {
    clearTimeout(timeout);
    timeout = setTimeout(func, delay);
  };
}

document.addEventListener("DOMContentLoaded", () => {
  // Header
  let lastScrollTop = 0;
  const header = document.querySelector("header");
  window.addEventListener("scroll", () => {
    const currentScroll = window.scrollY || document.documentElement.scrollTop;
    header.style.top = currentScroll > lastScrollTop && currentScroll > 100 ? "-140px" : "0";
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  });

  // Animações iniciais
  const menu = document.querySelector('.menu_header');
  if (menu) menu.classList.add('bounce-in');
  const btnFaleComigo = document.querySelector('.btn_falecomigo');
  if (btnFaleComigo) btnFaleComigo.classList.add('bounce-in');

 
});

   
});

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('formulario');
  if (form) {
    form.addEventListener('submit', enviarWhatsapp);
    console.log('EventListener de submit adicionado!');
  } else {
    console.log('Formulário não encontrado!');
  }
});

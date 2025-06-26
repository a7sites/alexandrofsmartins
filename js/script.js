function enviarWhatsapp(event) {
  event.preventDefault();

  const nome = document.getElementById("nome").value.trim();
  const msg = document.getElementById("msg").value.trim();
  const erroBox = document.getElementById("erroMensagem");

  if (nome === "" || msg === "") {
    erroBox.classList.add("mostrar");

    setTimeout(() => {
      erroBox.classList.remove("mostrar");
    }, 3000);

    return;
  }

  const telefone = '5531995999029';
  const texto = `Olá! Meu nome é ${nome}, ${msg}. Você pode me contatar pelo whatsapp?`;
  const msgFormatada = encodeURIComponent(texto);

  const url = `https://wa.me/${telefone}?text=${msgFormatada}`;

  window.open(url, '_blank');
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

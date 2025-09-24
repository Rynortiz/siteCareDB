


AOS.init({
    duration: 1000,
    once: false,
    mirror: true,
});
//=====================================================
function ativarMenu() {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}
//=====================================================
function trocarIcone() {
    const icone = document.getElementById("icone");

    if (icone.classList.contains("fa-bars")) {
        icone.classList.remove("fa-bars");
        icone.classList.add("fa-xmark");
    } else {
        icone.classList.remove("fa-xmark");
        icone.classList.add("fa-bars");
    }
}
//=====================================================
const arrowDown = document.getElementById("arrow");
const container = document.getElementById("containerArrow");

window.addEventListener("scroll", () => {
    if (window.scrollY > 120) {
        document.querySelectorAll(".btn i").forEach((el) => {
            el.classList.add("aos-animate");
        });
        arrowDown.style.opacity = 0;
        container.classList.remove("container-arrow");
    } else {
        arrowDown.style.opacity = 1;
        container.classList.add("container-arrow")
    }
});

//===========================================================
const icone = document.getElementById("btnInstagram");
const alvo = document.getElementById("contato");

window.addEventListener("scroll", () => {
    const alvoTopo = alvo.getBoundingClientRect().top;
    const alvoAltura = alvo.offsetHeight;

    if (alvoTopo <= window.innerHeight && alvoTopo + alvoAltura > 0) {

        icone.style.opacity = 0;
    } else {
        icone.style.opacity = 1;
    }
});
//===========================================================
function scrollToSection(event, id) {
    event.preventDefault();
    const target = document.getElementById(id);
    const offset = 80;
    const topPos = target.getBoundingClientRect().top + window.scrollY - offset;

    window.scrollTo({
        top: topPos,
        behavior: 'smooth'
    });
}
//=============================================================
function validarSenhas() {
    const senha = document.getElementById("senha").value;
    const confirmar = document.getElementById("confirmar_senha").value;

    if (senha !== confirmar) {
        alert("As senhas não coincidem!");
        return false;
    }
    return true;
}

<header>

    <!--Menu hamburguer com dropdown -->
    <div class="menu-ham" id="topo">
        <a class="hamburger" onclick="ativarMenu()">
            <i onclick="trocarIcone()" id="icone" class="fa-solid fa-bars" style="color: #606C38;"></i>
        </a>
        <div class="dropdown" id="dropdownMenu">
            <a class="dropBtns" onclick="ativarMenu(), trocarIcone()" href="home">Home</a>
            <a class="dropBtns" onclick="ativarMenu(), trocarIcone()" href="galeria">Galeria</a>
            <a class="dropBtns" onclick="ativarMenu(), trocarIcone()" href="home#sobre">Sobre</a>
            <a class="dropBtns" onclick="ativarMenu(), trocarIcone()" href="home#produto">Produtos</a>
            <a onclick="ativarMenu(), trocarIcone()" class="dropContato" href="home#contatos">Contato</a>
        </div>
    </div>

</header>
<main class="containerForm">
    <img src="images/careMLogoTrans.png">
    <form class="row g-3 form">
        <div class="col-md-6">
            <label for="inputNome" ">Nome</label>
            <input type="text" class="form-control" id="inputNome">
        </div>
        <div class="col-md-6">
            <label for="inputsobreNome" ">Sobrenome</label>
            <input type="text" class="form-control" id="inputsobreNome">
        </div>
        <div class="col-md-6">
            <label for="inputEmail4" ">Email</label>
            <input type="email" class="form-control" id="inputEmail4">
        </div>
        <div class="col-12">
            <label for="inputAddress" ">Endereço</label>
            <input type="text" class="form-control" id="inputAddress" placeholder="Rua, Bairro">
        </div>
        <div class="col-12">
            <label for="inputAddress2" ">Complemento (Opcional) </label>
            <input type="text" class="form-control" id="inputAddress2" placeholder="Apartamento, andar, sala">
        </div>
        <div class="col-md-6">
            <label for="inputCity" ">Cidade</label>
            <input type="text" class="form-control" id="inputCity">
        </div>
        <div class="col-md-4">
            <label for="inputState" ">UF</label>
            <select id="inputState" class="form-select">
                <option selected>Selecione</option>
                <option>PR</option>
                <option>SC</option>
                <option>RS</option>
                <option>SP</option>
                <option>RJ</option>
                <option>BH</option>
                <option>PA</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
    </form>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".form");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const nome = document.getElementById("inputNome");
        const sobrenome = document.getElementById("inputsobreNome");
        const email = document.getElementById("inputEmail4");
        const endereco = document.getElementById("inputAddress");
        const cidade = document.getElementById("inputCity");
        const uf = document.getElementById("inputState");

        if (
            nome.value.trim() === "" ||
            sobrenome.value.trim() === "" ||
            email.value.trim() === "" ||
            endereco.value.trim() === "" ||
            cidade.value.trim() === "" ||
            uf.value === "Selecione"
        ) {
            alert("Preencha todos os campos obrigatórios.");
            return;
        }

        if (!email.value.includes("@") || !email.value.includes(".")) {
            alert("Digite um e-mail válido.");
            return;
        }

        alert("Formulário enviado com sucesso!");
        form.submit();
    });
});
</script>
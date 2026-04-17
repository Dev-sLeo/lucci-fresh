"use strict";

export default function () {
    document.addEventListener("click", function (e) {
        // Se clicou em uma opção
        if (e.target.classList.contains("c-select__option")) {
            e.preventDefault(); // impede reload ou submit

            const option = e.target;
            const select = option.closest(".c-select");
            const label = select.querySelector(".c-select__label");

            // Atualiza o label
            label.innerText = option.innerText;

            // Atualiza o valor armazenado
            select.dataset.value = option.dataset.value;

            // Fecha o dropdown
            select.classList.remove("is-open");

            return; // Não faz mais nada
        }

        // Se clicou no controle do select
        const control = e.target.closest(".c-select__control");
        if (control) {
            e.preventDefault(); // evita comportamento inesperado

            const select = control.closest(".c-select");
            select.classList.toggle("is-open");
            return;
        }

        // Se clicou fora de qualquer select → fecha todos
        document
            .querySelectorAll(".c-select.is-open")
            .forEach((s) => s.classList.remove("is-open"));
    });
}

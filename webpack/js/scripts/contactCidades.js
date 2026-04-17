"use strict";

/**
 * Populates the city select (.input-cidade) based on the selected state (.input-estado)
 * using the public IBGE API: https://servicodados.ibge.gov.br
 */
export default function () {
    const estadoSelect = document.querySelector(".input-estado");
    if (!estadoSelect) return;

    const cidadeSelect = document.querySelector(".input-cidade");
    if (!cidadeSelect) return;

    const defaultOption =
        cidadeSelect.querySelector("option[value='']") || null;
    const defaultLabel = defaultOption ? defaultOption.textContent : "Cidade";

    function resetCidades() {
        cidadeSelect.innerHTML = "";
        const placeholder = document.createElement("option");
        placeholder.value = "";
        placeholder.textContent = defaultLabel;
        cidadeSelect.appendChild(placeholder);
        cidadeSelect.disabled = true;
    }

    async function loadCidades(uf) {
        resetCidades();
        if (!uf) return;

        try {
            const res = await fetch(
                `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${encodeURIComponent(uf)}/municipios?orderBy=nome`,
            );
            if (!res.ok) throw new Error("IBGE API error");

            const cidades = await res.json();

            cidadeSelect.disabled = false;
            cidades.forEach(function (cidade) {
                const opt = document.createElement("option");
                opt.value = cidade.nome;
                opt.textContent = cidade.nome;
                cidadeSelect.appendChild(opt);
            });
        } catch (e) {
            console.error("Erro ao carregar cidades:", e);
        }
    }

    resetCidades();

    estadoSelect.addEventListener("change", function () {
        loadCidades(this.value);
    });
}

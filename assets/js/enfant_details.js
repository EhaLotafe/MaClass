document.addEventListener("DOMContentLoaded", async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const enfantId = urlParams.get("enfant_id");

    if (!enfantId) {
        alert("Aucun enfant sélectionné !");
        window.location.href = "dashboard_parent.html";
        return;
    }

    try {
        const response = await fetch(`../backend/enfant_details.php?enfant_id=${enfantId}`);
        const result = await response.json();

        if (result.success) {
            const { enfant, notes, emploi_du_temps, paiements } = result.data;

            document.getElementById("enfant-nom").textContent = enfant.nom;
            document.getElementById("enfant-classe").textContent = enfant.classe;

            document.getElementById("notes-list").innerHTML = notes.map(n => `
                <tr><td class="border px-4 py-2">${n.matiere}</td><td class="border px-4 py-2">${n.note}</td></tr>
            `).join("");

            document.getElementById("emploi-du-temps").innerHTML = emploi_du_temps.map(e => `
                <li>${e.jour} - ${e.matiere} (${e.heure})</li>
            `).join("");

            const paiementsDates = paiements.map(p => p.date);
            const paiementsMontants = paiements.map(p => p.montant);

            new Chart(document.getElementById("paiementsChart"), {
                type: "line",
                data: {
                    labels: paiementsDates,
                    datasets: [{ label: "Paiements", data: paiementsMontants, borderColor: "blue", fill: false }]
                }
            });

        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Erreur :", error);
    }
});

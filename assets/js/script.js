// Fonction de validation de formulaire pour la page de connexion
document.addEventListener("DOMContentLoaded", function() {
    // Validation du formulaire de connexion
    const loginForm = document.querySelector("form");
    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            const email = document.getElementById("email").value;
            const mot_de_passe = document.getElementById("mot_de_passe").value;

            if (!email || !mot_de_passe) {
                alert("Veuillez remplir tous les champs.");
                event.preventDefault(); // Empêche l'envoi du formulaire si des champs sont vides
            }
        });
    }
});
const logoutButton = document.getElementById("logout");

if (logoutButton) {
    logoutButton.addEventListener("click", () => {
        const confirmLogout = confirm("Êtes-vous sûr de vouloir vous déconnecter ?");
        if (confirmLogout) {
            alert("Vous avez été déconnecté.");
            window.location.href = "index.html";
        }
    });
}

document.getElementById('checkPayments').addEventListener('click', async () => {
    const response = await fetch('../backend/gestion_paiements.php', {
        method: 'GET',
    });
    const data = await response.json();
    console.log(data); // Afficher les paiements dans la console ou dans une table HTML
});
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const response = await fetch("../backend/dashboard_parent.php");
        const result = await response.json();

        if (result.success) {
            const { enfants, paiements } = result.data;
            const enfantsContainer = document.getElementById("enfants-list");

            // Afficher chaque enfant
            enfants.forEach(enfant => {
                const enfantCard = document.createElement("div");
                enfantCard.classList.add("bg-white", "p-6", "rounded-lg", "shadow-lg");

                enfantCard.innerHTML = `
                    <h3 class="text-xl font-semibold mb-2">${enfant.nom}</h3>
                    <p class="text-gray-700">Classe : ${enfant.classe}</p>
                    <p class="text-gray-700">Matricule : ${enfant.matricule}</p>
                    <button onclick="voirPaiements(${enfant.id})" 
                        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Voir les paiements
                    </button>
                `;
                enfantsContainer.appendChild(enfantCard);
            });

            // Afficher les paiements (quand on clique sur "Voir les paiements")
            window.voirPaiements = (enfantId) => {
                const paiementsList = paiements
                    .filter(p => p.eleve_id === enfantId)
                    .map(p => `<li>${p.date} - ${p.montant}€</li>`)
                    .join("");

                alert(`Paiements pour l'enfant ${enfantId} :\n` + (paiementsList || "Aucun paiement trouvé."));
            };

        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Erreur :", error);
        alert("Impossible de charger les données.");
    }
});
const paiementsDates = paiements.map(p => p.date);
const paiementsMontants = paiements.map(p => p.montant);

new Chart(document.getElementById("paiementsChart"), {
    type: "line",
    data: {
        labels: paiementsDates,
        datasets: [{
            label: "Montant des paiements (USD)",
            data: paiementsMontants,
            borderColor: "blue",
            fill: false
        }]
    }
});

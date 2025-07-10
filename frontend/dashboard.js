document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Charger les données dynamiques
        const response = await fetch("../backend/dashboard_admin.php");
        const result = await response.json();

        if (result.success) {
            const { parents, eleves, paiements } = result.data;

            // Afficher les données globales
            document.querySelector("#total-parents").textContent = parents.length;
            document.querySelector("#total-eleves").textContent = eleves.length;
            document.querySelector("#total-paiements").textContent = `${paiements.reduce((sum, p) => sum + parseFloat(p.montant), 0)} USD`;

            // Afficher les parents
            const parentsTable = document.querySelector("#table-parents tbody");
            parents.forEach(parent => {
                const row = `<tr>
                    <td>${parent.id}</td>
                    <td>${parent.nom}</td>
                    <td>${parent.email}</td>
                    <td>${parent.telephone}</td>
                </tr>`;
                parentsTable.innerHTML += row;
            });

            // Afficher les élèves
            const elevesTable = document.querySelector("#table-eleves tbody");
            eleves.forEach(eleve => {
                const row = `<tr>
                    <td>${eleve.id}</td>
                    <td>${eleve.nom}</td>
                    <td>${eleve.classe}</td>
                    <td>${eleve.parent_id}</td>
                </tr>`;
                elevesTable.innerHTML += row;
            });

            // Afficher les paiements
            const paiementsTable = document.querySelector("#table-paiements tbody");
            paiements.forEach(paiement => {
                const row = `<tr>
                    <td>${paiement.id}</td>
                    <td>${paiement.eleve_id}</td>
                    <td>${paiement.montant} USD</td>
                    <td>${paiement.date}</td>
                </tr>`;
                paiementsTable.innerHTML += row;
            });
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Erreur :", error);
        alert("Impossible de charger les données.");
    }

    // Gestion de la navigation entre sections
    document.querySelectorAll(".menu li a").forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            document.querySelectorAll(".section").forEach((section) => section.classList.add("hidden"));
            const sectionId = link.dataset.section;
            document.getElementById(sectionId).classList.remove("hidden");
        });
    });

    console.log("Dashboard JS chargé !");
});
document.addEventListener("DOMContentLoaded", () => {
    chargerCommuniques(); // Charger les communiqués au chargement de la page
});

// Charger les communiqués dynamiquement
async function chargerCommuniques() {
    try {
        const response = await fetch("../backend/dashboard_admin.php?action=get_communiques");
        const result = await response.json();

        if (result.success) {
            const communiquesList = document.getElementById("communiques-list");
            communiquesList.innerHTML = ""; // Vider la liste existante

            result.data.communiques.forEach((communique) => {
                const li = document.createElement("li");
                li.innerHTML = `
                    <span>${communique.texte} (${communique.cible === "ecole" ? "Pour l'école" : "Élève ID: " + communique.cible})</span>
                    <div class="actions">
                        <button onclick="modifierCommunique(${communique.id}, '${communique.texte}', '${communique.cible}')">Modifier</button>
                        <button onclick="supprimerCommunique(${communique.id})">Supprimer</button>
                    </div>
                `;
                communiquesList.appendChild(li);
            });
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Erreur :", error);
    }
}

// Publier un communiqué
async function envoyerCommunique() {
    const texte = document.getElementById("communique-text").value.trim();
    const cible = document.getElementById("communique-cible").value;

    if (!texte) {
        alert("Le texte du communiqué ne peut pas être vide.");
        return;
    }

    try {
        const response = await fetch("../backend/dashboard_admin.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "ajouter_communique", texte, cible }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Communiqué publié avec succès !");
            document.getElementById("communique-text").value = ""; // Réinitialiser le formulaire
            chargerCommuniques(); // Recharger la liste
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Erreur :", error);
    }
}

// Modifier un communiqué
function modifierCommunique(id, texte, cible) {
    const nouveauTexte = prompt("Modifier le communiqué :", texte);
    if (nouveauTexte !== null) {
        fetch("../backend/dashboard_admin.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "modifier_communique", id, texte: nouveauTexte, cible }),
        })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                alert("Communiqué modifié avec succès !");
                chargerCommuniques(); // Recharger la liste
            } else {
                alert(result.message);
            }
        })
        .catch((error) => console.error("Erreur :", error));
    }
}

// Supprimer un communiqué
function supprimerCommunique(id) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce communiqué ?")) {
        fetch("../backend/dashboard_admin.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "supprimer_communique", id }),
        })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                alert("Communiqué supprimé avec succès !");
                chargerCommuniques(); // Recharger la liste
            } else {
                alert(result.message);
            }
        })
        .catch((error) => console.error("Erreur :", error));
    }
}

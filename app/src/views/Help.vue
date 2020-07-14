<template>
    <div class="container">
        <div class="page-item">
            <h2>Raccourcis Clavier à connaître</h2>
            <h4><u>Accueil - Serveur</u></h4>
            <ul>
                <li>Retour arrière d'un dossier : <b>Ctrl + Flèche de Gauche</b>.</li>
                <li>Afficher les options d'un élément dans la liste : <b>Clique Droit</b>.</li>
                <li>Supprimer plusieurs éléments dans la liste : <b>Ctrl + Clique Gauche</b>.</li>
            </ul>
        </div>

        <div class="page-item">
            <h2>Interface</h2>
            <h4><u>Accueil - Serveur</u></h4>
            <ul>
                <li>
                    "Utilisateur(s) connecté(s)" vous renseigne le nombre d'utilisateurs connecté(s) en même temps et
                    dans le même dossier.
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Help',
    };
</script>

<style lang="scss" scoped>
    body[data-theme='dark'] .page-item {
        color: white;
    }

    .container {
        margin-left: var(--navbar-left-width);
    }
</style>

<template>
    <div v-if="step.length !== ''" id="firstLaunch">
        <div v-if="step === 'leftNavbar_Navigation'" id="leftNavbar_Navigation" class="rounded-perso-1 animated fadeIn">
            <div>
                <h3>Navigation</h3>
                <p>
                    Le menu de navigation se trouve sur le côté gauche de la page, cette partie restera toujours au
                    premier plan et à la même position.
                </p>
                <button class="animated fadeInDown rounded-perso-2" @click="stepNext">OK</button>
            </div>
            <div class="arrow"></div>
        </div>
        <div
            v-if="step === 'page_home_server_connection'"
            id="page_home_server_connection"
            class="rounded-perso-1 animated fadeIn"
        >
            <div>
                <h3>Connexion à un serveur</h3>
                <p>
                    Afin de vous connecter à un serveur, il vous suffit de sélectionner l'un des serveurs disponibles
                    dans le sélecteur <b><i>"Liste des serveurs"</i></b> au-dessus.
                </p>
                <p>
                    Si la connexion c'est bien passé, vous devriez voir les fichiers et dossiers apparaitre en dessous.
                </p>
                <button class="animated fadeInDown rounded-perso-2" @click="stepNext">OK</button>
            </div>
            <div class="arrow"></div>
        </div>
        <div
            v-if="step === 'page_home_server_tips_folder'"
            id="page_home_server_tips_folder"
            class="rounded-perso-1 animated fadeIn"
        >
            <h3>Navigation dans un serveur</h3>
            <p>
                <u><b>Dossier</b></u
                ><br />
                Pour vous déplacer dans un dossier, il suffit de faire un double-clique gauche avec votre sourie sur le
                nom de celui-ci afin de naviguer à l'intérieur.
            </p>
            <button class="animated fadeInDown rounded-perso-2" @click="stepNext">OK</button>
        </div>
        <div
            v-if="step === 'page_home_server_tips_contextMenu'"
            id="page_home_server_tips_contextMenu"
            class="rounded-perso-1 animated fadeIn"
        >
            <h3>Navigation dans un serveur</h3>
            <p>
                Pour supprimer ou modifier un élément, il vous suffit de faire un clique droit avec la sourie sur le nom
                de l'élément pour faire afficher les options.
            </p>
            <button class="animated fadeInDown rounded-perso-2" @click="stepNext">OK</button>
        </div>
        <div v-if="step === 'page_home_page_help'" id="page_home_page_help" class="rounded-perso-1 animated fadeIn">
            <div>
                <h3>Avoir plus d'informations</h3>
                <p>Vous pouvez avoir plus d'aide sur la page <b>aide</b>.</p>
                <button class="animated fadeInDown rounded-perso-2" @click="stepNext">OK</button>
            </div>
            <div class="arrow"></div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../EventBus';

    export default {
        name: 'FirstLaunch.vue',
        props: {
            step: String,
        },
        methods: {
            stepNext() {
                EventBus.$emit('firstLaunch_next');
            },
        },
    };
</script>

<style scoped lang="scss">
    @import '../../styles/firstLaunch';
</style>

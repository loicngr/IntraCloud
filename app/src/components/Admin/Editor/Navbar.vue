<template>
    <nav id="navbar">
        <button v-if="!hide" @click="save">Sauvegarder</button>
        <button v-if="!hide" @click="download">Télécharger</button>
        <button v-if="!hide" @click="quit">Fermer</button>
        <button @click="hideUI">
            {{ !hide ? 'Cacher' : 'Afficher' }}
        </button>
    </nav>
</template>

<script>
    import { EventBus } from '../../EventBus';

    export default {
        name: 'EditorNavbar',
        props: {
            hide: {
                type: Boolean,
            },
        },
        methods: {
            save() {
                EventBus.$emit('editor_saveFile');
            },
            download() {
                EventBus.$emit('editor_downloadFile');
            },
            quit() {
                EventBus.$emit('editor_quitFile');
            },
            hideUI() {
                EventBus.$emit('editor_hideUI');
            },
        },
    };
</script>

<style scoped lang="scss">
    nav#navbar {
        width: 300px;
        height: auto;

        position: fixed;
        bottom: -70px;
        right: 0;

        z-index: 999;

        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-around;

        button {
            width: auto;
            height: 25px;

            background-color: white;
            color: gray;

            text-transform: uppercase;
            font-weight: bold;

            padding: 5px 10px;
            margin: 5px 0;

            border: 0;
            border-radius: 20px;
            transition: all 0.2s ease-in-out;

            &:hover {
                cursor: pointer;
                color: #1586f8;
            }

            &:focus {
                outline: none;
            }
        }
    }
</style>

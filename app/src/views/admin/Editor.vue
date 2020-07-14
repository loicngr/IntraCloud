<template>
    <div id="editor">
        <Navbar :hide="uiStatus" />
        <MonacoEditor
            v-if="editor.blob !== null"
            @editorDidMount="editorMounted"
            class="editor"
            v-model="editor.code"
            :language="editor.language"
            :options="editor.options"
            theme="vs-dark"
        />
    </div>
</template>

<script>
    /*
     * Thanks To @trey-miller
     * At : https://github.com/microsoft/monaco-editor-webpack-plugin/issues/32#issuecomment-475319603
     * */
    const publicPath = process.env.BASE_URL;
    function getWorkerUrl(workerId, label) {
        switch (label) {
            case 'json':
                return publicPath + 'json.worker.js';
            case 'css':
                return publicPath + 'css.worker.js';
            case 'less':
                return publicPath + 'css.worker.js';
            case 'scss':
                return publicPath + 'css.worker.js';
            case 'html':
                return publicPath + 'html.worker.js';
            case 'handlebars':
            case 'razor':
                return publicPath + 'html.worker.js';
            case 'javascript':
                return publicPath + 'typescript.worker.js';
            case 'typescript':
                return publicPath + 'typescript.worker.js';
            default:
                return publicPath + 'editor.worker.js';
        }
    }
    window.MonacoEnvironment = {
        getWorkerUrl: function (workerId, label) {
            const url = getWorkerUrl(workerId, label);
            return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                    self.MonacoEnvironment = {
                      baseUrl: '${publicPath}'
                    };
                    importScripts('${url}');`)}`;
        },
    };

    import { EventBus } from '../../components/EventBus';
    import MonacoEditor from 'vue-monaco';

    import EditorNavbar from '../../components/Admin/Editor/Navbar';

    export default {
        name: 'Editor',
        components: {
            MonacoEditor,
            Navbar: EditorNavbar,
        },
        data() {
            return {
                storeEditor: null,
                uiStatus: false,
                pressedKey: new Set(),
                editor: {
                    instance: null,
                    language: null,
                    options: {
                        lineNumbers: true,
                        autoClosingBrackets: true,
                        autoClosingQuotes: true,
                    },
                    code: '',
                    blob: null,
                    content: null,
                    name: null,
                    size: null,
                    mime: null,
                },
            };
        },
        methods: {
            /**
             * Fonctions pour gérer le WebSocket de l'éditeur
             * */
            ws_OnMessage(e) {
                const result = JSON.parse(e.data);
                const user = this.$store.getters.user;

                if (result) {
                    const socket = this.$store.getters.socket;
                    switch (result.type) {
                        case 'ping_ssh': {
                            if (
                                this.storeEditor.server.host === result.server.host &&
                                this.storeEditor.server.port === result.server.port &&
                                this.storeEditor.server.login === result.server.login
                            ) {
                                socket.send(
                                    JSON.stringify({
                                        type: 'pong_edit_file',
                                        email: user.email,
                                        server: this.storeEditor.server,
                                        filename: this.storeEditor.location.split('/').pop(),
                                    })
                                );
                            }
                            break;
                        }
                        default:
                            break;
                    }
                }
            },
            ws_initEvent(socket) {
                socket.addEventListener('message', this.ws_OnMessage);
            },
            ws_disconnectMe() {
                const socket = this.$store.getters.socket || null;
                const user = this.$store.getters.user;

                /**
                 * Si on trouve le socket
                 * et
                 * qu'il soit encore ouvert
                 * */
                if (socket && socket.readyState === 1) {
                    socket.send(
                        JSON.stringify({
                            type: 'close_edit_file',
                            email: user.email,
                        })
                    );

                    socket.removeEventListener('message', this.ws_OnMessage);
                }
            },
            ws_editFile() {
                const socket = this.$store.getters.socket || null;
                if (socket) {
                    const user = this.$store.getters.user;
                    socket.send(
                        JSON.stringify({
                            type: 'ping_edit_file',
                            email: user.email,
                            server: this.storeEditor.server,
                            filename: this.storeEditor.location.split('/').pop(),
                        })
                    );
                }
            },

            /**
             * Quand l'éditeur est monté, on enregistre l'instance
             * */
            editorMounted(editor) {
                this.editor.instance = editor;
                const socket = this.$store.getters.socket || null;
                if (socket) this.ws_initEvent(socket);
            },

            /**
             * Retourne les metadata du fichier blob
             * */
            getFileMetadata(blob) {
                const name = blob.name || this.storeEditor.location.split('/').pop().trim();
                let type = blob.type;
                const size = blob.size;

                if (!type || type.length !== 0) {
                    // Si il y a un un point "." devant le nom du fichier, comme ".gitignore"
                    if (name.indexOf('.') !== -1 && name.indexOf('.') !== 0) {
                        let fileExt = name.split('.')[1];
                        if (fileExt) {
                            switch (fileExt) {
                                case 'php':
                                    type = 'php';
                                    break;
                                case 'html':
                                    type = 'html';
                                    break;
                                case 'js':
                                    type = 'javascript';
                                    break;
                                case 'rs':
                                    type = 'rust';
                                    break;
                                case 'md':
                                    type = 'markdown';
                                    break;
                                case 'py':
                                    type = 'python';
                                    break;
                                case 'json':
                                    type = 'json';
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }

                return { name, type, size };
            },

            /**
             * Retourne le contenu d'un base64 décodé
             * */
            getB64Content() {
                let decodedB64;
                try {
                    if (this.editor.content) decodedB64 = decodeURIComponent(escape(atob(this.editor.content)));
                    else decodedB64 = '';
                } catch (e) {
                    console.error("Une erreur c'est produite pendant l'ouverture du fichier.");
                    console.error(e);
                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message: "Une erreur c'est produite pendant l'ouverture du fichier.",
                    });
                    this.$router.push({ name: 'Home' });
                    return;
                }

                return decodedB64;
            },

            /**
             * Quand le fichier est complétement décodé et que l'éditeur est prêt
             * */
            fileIsFullyLoaded() {
                this.editor.code = this.getB64Content();
                this.ws_editFile();
            },

            /**
             * Quand on a le contenu du fichier
             * On défini le type mime et le contenu dans l'éditeur
             * */
            setFileContent(evt) {
                const fileRowContent = evt.target.result;
                const fileSplited = fileRowContent.split(',');
                this.editor.mime = fileSplited[0].split(':')[1].split(';')[0];
                this.editor.content = fileSplited[1];

                // Ok, le fichier est complétement chargé
                this.fileIsFullyLoaded();
            },

            /**
             * Quand le fichier est envoyé dans l'éditeur
             * */
            fileUploaded(editFile) {
                const file = editFile;

                const fileMetaData = this.getFileMetadata(file);

                this.editor.name = fileMetaData.name;
                this.editor.size = fileMetaData.size;
                this.editor.blob = file;
                this.editor.language =
                    fileMetaData.type.indexOf('/') !== -1 ? fileMetaData.type.split('/')[1] : fileMetaData.type;

                // On lit le contenu du fichier (blob)
                const reader = new FileReader();
                reader.addEventListener('load', this.setFileContent);
                reader.readAsDataURL(file);
            },

            /**
             * Création d'un fichier blob avec le contenu de l'éditeur
             * */
            codeToBlob() {
                return new Blob([this.editor.code], { type: this.editor.mime });
            },

            /**
             * Télécharger le fichier avec le code de l'éditeur
             * */
            downloadFile() {
                const blob = this.codeToBlob();

                const aElement = document.createElement('a');
                const url = URL.createObjectURL(blob);

                aElement.style.display = 'none';
                document.body.appendChild(aElement);
                aElement.href = url;
                aElement.download = this.editor.name;
                aElement.click();

                // Remove a and url element in DOM
                URL.revokeObjectURL(url);
                document.body.removeChild(aElement);
            },

            /**
             * Sauvegarder le fichier dans le serveur via l'API
             * */
            saveFile() {
                EventBus.$emit('SSH_saveFile', {
                    blob: this.codeToBlob(),
                    location: this.storeEditor.location,
                    server: this.storeEditor.server,
                });
            },

            /**
             * Réinitialisation de l'éditeur
             * */
            resetEditor() {
                EventBus.$emit('loaderRequest', true);

                this.editor.language = null;
                this.editor.code = '';
                this.editor.blob = null;
                this.editor.content = null;
                this.editor.name = null;
                this.editor.size = null;
                this.editor.mime = null;

                this.$store
                    .dispatch('setEditorFile', {
                        blob: null,
                        mime: null,
                        location: null,
                        server: null,
                    })
                    .then(() => {
                        EventBus.$emit('loaderRequest', false);
                        this.$router.push({ name: 'Home' });
                    });
            },

            /**
             * Basculer l'affichage des boutons sur l'interface
             * */
            hideUI() {
                this.uiStatus = !this.uiStatus;
            },

            /**
             * Quand on appuie sur une touche du clavier
             * */
            onKeyDown(evt) {
                const keyName = evt.code;

                switch (keyName) {
                    case 'ControlLeft':
                        this.pressedKey.add('ctrl');
                        break;
                    case 'KeyS':
                        this.pressedKey.add('s');
                        break;
                    default:
                        break;
                }

                // Les touches ctrl+s sont pressés ?
                if (this.pressedKey.has('ctrl') && this.pressedKey.has('s') && this.editor.code !== '') {
                    evt.preventDefault();

                    this.saveFile();
                }
            },
            /**
             * Quand on lâche une touche du clavier
             * */
            onKeyUp(evt) {
                const keyName = evt.code;

                switch (keyName) {
                    case 'ControlLeft':
                        this.pressedKey.delete('ctrl');
                        break;
                    case 'KeyS':
                        this.pressedKey.delete('s');
                        break;
                    default:
                        break;
                }
            },
        },
        mounted() {
            const editedFile = this.$store.getters.getEditorFile;
            const isAdmin = this.$store.getters.isAdmin;

            /**
             * Si VueX possède bien un fichier, et si nous somme Administrateur
             * */
            if (!editedFile || !isAdmin) {
                EventBus.$emit('loaderRequest', false);
                try {
                    this.$router.back();
                } catch (e) {
                    this.$router.push({ name: 'Home' });
                    throw new Error(e);
                }
                return;
            }

            this.storeEditor = editedFile;
            this.fileUploaded(editedFile.blob);

            /*
             * Child components events
             * */
            EventBus.$on('editor_saveFile', this.saveFile);
            EventBus.$on('editor_downloadFile', this.downloadFile);
            EventBus.$on('editor_quitFile', this.resetEditor);
            EventBus.$on('editor_hideUI', this.hideUI);

            window.addEventListener('keydown', this.onKeyDown);
            window.addEventListener('keyup', this.onKeyUp);
        },
        beforeDestroy() {
            EventBus.$off('SSH_saveFile');
            EventBus.$off('editor_saveFile');
            EventBus.$off('editor_downloadFile');
            EventBus.$off('editor_quitFile');
            EventBus.$off('editor_hideUI');

            this.ws_disconnectMe();
        },
    };
</script>

<style lang="scss">
    #editor {
        overflow: hidden;

        input:first-of-type {
            position: absolute;
            top: 20px;
            right: 60px;

            color: white;

            z-index: 2;
        }

        .editor {
            width: 100vw;
            height: 100vh;
        }
    }
</style>

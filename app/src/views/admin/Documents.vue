<template>
    <div id="page-container">
        <div class="page-item">
            <h4>
                Retour aux serveurs
                <span class="no-bg">
                    <button class="rounded-perso-2" @click="$router.push({ name: 'AdminServers' })">Retour</button>
                </span>
            </h4>
            <h4>
                Supprimer les documents
                <span class="no-bg">
                    <button class="rounded-perso-2" @click="deleteDocuments">Supprimer</button>
                </span>
            </h4>
            <h4>
                Rechercher
                <span class="no-bg span-btn">
                    <form id="search" style="width: 100%; height: 100%;">
                        <input
                            name="query"
                            v-model="searchQuery"
                            class="rounded-perso-1"
                            placeholder=" Rechercher un Document"
                            style="width: 100%; border: 0; height: 100%;"
                        />
                    </form>
                </span>
            </h4>
        </div>
        <div class="page-item">
            <DocumentsTable
                :elements="gridData"
                :columns="gridColumns"
                :filter-key="searchQuery"
                :isLoading="documentsLoading"
            />
        </div>
    </div>
</template>

<script>
    import DocumentsTable from '../../components/Admin/DocumentsTable';
    import { EventBus } from '../../components/EventBus';
    import { Server } from '../../api/Server';

    const serverAPI = new Server();
    export default {
        name: 'Documents',
        components: {
            DocumentsTable,
        },
        data() {
            return {
                searchQuery: '',
                gridColumns: ['icone', 'nom', 'emplacement', 'taille', 'date'],
                gridData: [],
                documentsLoading: true,
            };
        },
        computed: {
            documents() {
                return this.$store.getters.documentsServer.documents;
            },
            server() {
                return this.$store.getters.documentsServer.server;
            },
        },
        methods: {
            createGridData() {
                this.gridData = [];

                this.documents.forEach((document) => {
                    this.gridData.push({
                        icone: '-_documents_-',
                        nom: document.name,
                        emplacement: document.location,
                        taille: document.size,
                        date: document.created_at,
                        other: {
                            user: document.user,
                            id: document.id,
                        },
                    });
                });
                this.documentsLoading = false;
            },
            deleteDocuments() {
                EventBus.$emit('loaderRequest', true);
                serverAPI.deleteDocuments(this.server.id, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null)  EventBus.$emit('expiredJWT', result.tokens);

                        if (result.status) {
                            this.createGridData();
                            this.$router.push({ name: 'AdminServers' });
                        }

                        EventBus.$emit('loaderRequest', false);
                    });
            },
        },
        mounted() {
            this.createGridData();
        },
        beforeMount() {
            if (this.documents && this.documents.length !== 0) {
                return true;
            }

            this.$router.push({ name: 'Home' });
        },
    };
</script>

<style scoped></style>
<template>
    <div class="bloc-item">
        <label for="ipt_serveur">Serveur</label>
        <select name="servers" id="ipt_serveur"  class="rounded-perso-1" :disabled="isSelectLoading" @change="onClickServer">
            <option value="default" :selected="selectedServerID === -1">Liste des serveurs</option>
            <option
                    v-for="server in selectServersData"
                    :key="server.id"
                    :value="server.name"
                    :data-id="server.id"
                    :data-login="server.login"
                    :data-adresse="server.adresse"
                    :data-port="server.port"
                    :selected="selectedServerID === server.id"
            >
                {{ server.name }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: 'ServersSelect',
        props: {
            isSelectLoading: Boolean,
            selectServersData: Array,
            selectedServerID: Number,
        },
        methods: {
            onClickServer(evt) {
                this.$emit('selectServer', evt);
            },
        },
    };
</script>

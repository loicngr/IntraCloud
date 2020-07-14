<template>
    <div>
        <label for="selectFilter">
            Trier par
            <select name="selectFilter" id="selectFilter" class="btn-blue rounded-perso-2" @click="selectedSortOption">
                <option value="name" selected>Nom</option>
                <option value="date">Date</option>
                <option value="size">Taille</option>
            </select>
        </label>
        <button class="btn-blue rounded-perso-2" @click="$emit('uploadFile', $event)">
            Téléverser
            <input name="file_upload" type="file" accept="*/*" style="display: none;" multiple />
        </button>
        <button class="btn-blue rounded-perso-2" @click="$emit('newSubFolder')">Nouveau dossier</button>

        <!-- TODO - Finir l'implémentation -->
        <button class="btn-blue rounded-perso-2" @click="$emit('selectDirectoryLocally')">
            Ouvrir un dossier local
        </button>
    </div>
</template>

<script>
    export default {
        name: 'TableNav',
        methods: {
            selectedSortOption(evt) {
                const optionValue = evt.target.selectedOptions[0]?.value || false;

                if (optionValue) this.$emit('selectFilter', optionValue);
            },
        },
    };
</script>

<style lang="scss" scoped>
    div {
        width: calc(100% - var(--navbar-left-width));
        height: auto;

        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-items: center;

		margin: 10px 0;

        button {
            height: 25px;

            padding: 2px 20px;
            margin: 10px 10px;
        }

        label {
            margin: 0 10px;
            color: var(--color-black);
            font-weight: normal;

			&:first-of-type {
				margin: 0 10px 0 0;
			}

            select {
                height: 25px;
                padding: 2px 20px;
            }
        }
    }
</style>

<template>
	<main id="directoryPreview" v-show="baseDirectory !== null">

		<button class="btn-blue rounded-perso-2" @click="closeFolder">Fermer le dossier</button>
		<ul id="localFilesRender"></ul>
	</main>
</template>

<script>
    import {EventBus} from "../EventBus";

	export default {
        name: "LocalDirectoryPreview",
		props: {
			bus: Object,
		},
		data() {
			return {
				baseDirectory: null,
				entriesElements: [],
				entriesLoading: false,
				baseDomRef: 'localFilesRender',
				ignoredDirectory: [
					'.git',
					'node_modules',
					'vendor',
					'target',
					'var'
				]
			}
		},
		methods: {
			AddIgnoredDirectory(evt) {
				const {value} = evt.target[0];
				evt.target[0].value = '';
				this.ignoredDirectory.push(value);
				evt.preventDefault();
			},
			imageOrFile(name) {
				const response = ['image', 'file'];
				let index = 1;

				if ((/\.(gif|jpe?g|tiff|png|webp|bmp)$/i).test(name)) index = 0;

				return response[index];
			},
			closeFolder() {
				this.entriesElements = [];
				this.baseDirectory = null;
			},

			async createEntriesElements(entries, cpt = -1, parentElement) {

				const tmpEntries = [];

				for await (const entry of entries) {
					const liElement = document.createElement('li');
					cpt ++;

					let kind = 'folder';
					let tmpEntry = {id: cpt, name: entry.name, type: kind, files: null};

					if (entry.isFile && entry.name && entry.name.length >= 2) {
						tmpEntry = {id: cpt, name: entry.name, type: this.imageOrFile(entry.name)};
						tmpEntries.push(tmpEntry);
						liElement.innerText = entry.name;

						parentElement.appendChild(liElement);
					}
					else if (entry.isDirectory && this.ignoredDirectory.indexOf(entry.name) === -1) {
						const detailsElement = document.createElement('details');
						const summaryElement = document.createElement('summary');
						const ulElement = document.createElement('ul');

						summaryElement.innerText = entry.name;

						detailsElement.appendChild(summaryElement);
						liElement.appendChild(detailsElement);

						const [subEntries, subCpt, childUlElement] = await this.createEntriesElements(await entry.getEntries(), cpt, ulElement);
						detailsElement.appendChild(childUlElement);
						tmpEntry.files = subEntries;
						tmpEntries.push(tmpEntry);
						cpt = subCpt;

						parentElement.appendChild(liElement);
					}
				}


				return [tmpEntries, cpt, parentElement];
			},
			async selectEntries() {
				/**
				 * Fonction appelé quand le composant reçoit l'évenement "openPreviewDirectorySelector" du parent.
				 * */
				this.entriesElements = [];

				// Handle Directory
				let fileHandle;
				try {
					fileHandle = await window.chooseFileSystemEntries({
						type: 'open-directory',
					});
				} catch (e) {
					this.entriesLoading = false;
					console.error(e, "Can't read directory.");
					EventBus.$emit('sendToast', {
						type: 'error',
						message: "Le dossier ne peut être ouvert.",
						timer: 5000
					});
					return;
				}

				if (fileHandle.isFile) return;
				this.entriesLoading = true;
				this.baseDirectory = fileHandle;

				const folderEntries = await fileHandle.getEntries();
				EventBus.$emit('sendToast', {
					type: 'success',
					message: "Dossier ouvert avec succès.",
					timer: 5000
				});
				const domRefElement = document.getElementById(this.baseDomRef);
				domRefElement.innerHTML = "";
				const [response, length] = await this.createEntriesElements(folderEntries, -1, domRefElement);
				this.entriesElements = response;
				this.entriesLoading = false;
			}
		},
		mounted() {
			this.bus.$on('openPreviewDirectorySelector', this.selectEntries);
		}
	}
</script>

<style scoped lang="scss">
	#directoryPreview {
		max-height: 600px;
		width: auto;
		max-width: 300px;

		overflow: auto;
		ul {
			list-style: none;
			text-align: left;

			margin: 0;
			padding: 0;
		}

	}
</style>

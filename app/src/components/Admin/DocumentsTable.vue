<template>
    <div>
        <div id="multipleDeletionsActions" v-if="deletionsSelected.length > 0">
            <button class="btn-blue rounded-perso-2" @click="deleteMultipleDeletions">Supprimer</button>
            <button class="btn-blue rounded-perso-2" @click="cancelMultipleDeletions">
                Annuler
            </button>
            <h6>{{ deletionsSelected.length }} éléments sélectionnés</h6>
        </div>
        <div class="tooltip table" role="tooltip">
            <button data-action="infos" @click="tooltipClick">
                Informations
            </button>
            <button data-action="select" @click="tooltipClick">
                Sélectionner / Désélectionner
            </button>
            <button data-action="delete" @click="tooltipClick">Supprimer</button>

            <span class="arrow" data-popper-arrow></span>
        </div>

        <div class="table-grid-container animated fadeIn">
            <table v-if="!isLoading && elements.length !== 0">
                <thead>
                    <tr>
                        <th v-for="key in columns" :key="key" :class="{ active: sortKey === key }">
                            <span v-if="key !== 'icone'" @click="sortBy(key)">
                                {{ key | upperCase }}
                                <span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"> </span>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(entry, keyEntry) in filteredElements" :key="keyEntry" class="animated fadeIn">
                        <td v-for="(element, keyElement) in entry" :key="element.nom">
                            <div v-if="keyElement !== 'other'">
                                <svg
                                    v-if="element === '-_documents_-'"
                                    width="19"
                                    height="17"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M17 0H1.889A1.89 1.89 0 000 1.889v3.778a1.89 1.89 0 001.889 1.889H17a1.89 1.89 0 001.889-1.89V1.89A1.89 1.89 0 0017 0zM1.889 5.667V1.889H17l.002 3.778H1.889zM17 9.444H1.889A1.89 1.89 0 000 11.334v3.777A1.89 1.89 0 001.889 17H17a1.89 1.89 0 001.889-1.889v-3.778A1.89 1.89 0 0017 9.444zM1.889 15.111v-3.778H17l.002 3.778H1.889z"
                                        fill="#9B9CAA"
                                    />
                                    <path
                                        d="M14.167 2.833h1.889v1.89h-1.89v-1.89zm-2.834 0h1.89v1.89h-1.89v-1.89zm2.834 9.445h1.889v1.889h-1.89v-1.89zm-2.834 0h1.89v1.889h-1.89v-1.89z"
                                        fill="#9B9CAA"
                                    />
                                </svg>
                                <span v-else-if="keyElement === 'taille'">
                                    {{ element | sizeParser }}
                                </span>
                                <span v-else-if="keyElement === 'date'">
                                    {{ element | dateParser }}
                                </span>

                                <span v-else data-table="nom">
                                    <span
                                        @click="selectDocument"
                                        @contextmenu="show"
                                        aria-describedby="tooltip"
                                        :data-name="entry.nom"
                                        :data-location="entry.emplacement"
                                        :data-user_id="entry.other.user.id"
                                        :data-user_firstname="entry.other.user.firstname"
                                        :data-user_lastname="entry.other.user.lastname"
                                        :data-id="entry.other.id"
                                        :data-size="entry.taille"
                                    >
                                        {{ element }}
                                    </span>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <span v-else-if="!isLoading && elements.length === 0">
                Il n'y a aucun documents...
            </span>
        </div>
    </div>
</template>

<script>
    import { formatDate, formatSize } from '../../utils/functions.js';
    import { createPopper } from '@popperjs/core';
    import Swal from 'sweetalert2';
    import { EventBus } from '../EventBus';

    import { Document } from '../../api/Document.js';

    const documentAPI = new Document();
    export default {
        name: 'DocumentsTable',
        props: {
            elements: Array,
            columns: Array,
            filterKey: String,
            isLoading: Boolean,
        },
        data() {
            let sortOrders = {};
            this.columns.forEach((key) => {
                if (key === 'nom') sortOrders[key] = -1;
                else sortOrders[key] = 1;
            });
            return {
                sortKey: 'nom',
                sortOrders: sortOrders,
                popperInstance: null,
                popperNode: null,
                previousPopper: null,
                multipleDeletions: false,
                deletionsSelected: [],
            };
        },
        computed: {
            filteredElements() {
                let sortKey = this.sortKey;
                let filterKey = this.filterKey && this.filterKey.toLowerCase();
                let order = this.sortOrders[sortKey] || 1;
                let elements = this.elements;
                if (filterKey) {
                    elements = elements.filter((row) => {
                        return Object.keys(row).some((key) => {
                            return String(row[key]).toLowerCase().indexOf(filterKey) > -1;
                        });
                    });
                }
                if (sortKey) {
                    elements = elements.slice().sort((a, b) => {
                        a = a[sortKey];
                        b = b[sortKey];
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    });
                }
                return elements;
            },
        },
        filters: {
            capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            },
            upperCase(str) {
                return str.toUpperCase();
            },
            dateParser(str) {
                return formatDate(str.date);
            },
            sizeParser: function (str) {
                return formatSize(str);
            },
        },
        methods: {
            sortBy(key) {
                /**
                 * Cette fonction s'occupe de modifier la clé pour le trie des éléments dans le tableau.
                 *
                 * la fonction filteredElements (computed) se mettra automatique à jour au changement de la clé de trie
                 * */

                /**
                 * On récupère l'élément <tbody> du DOM
                 * */
                const tbodyElement = document.querySelector('.table-grid-container table tbody');

                /**
                 * On récupère tous les TR avec les attributs "data-selected_delete"
                 * */
                const currentTrSelected = [...tbodyElement.children].filter((trElement) =>
                    trElement.hasAttribute('data-selected_delete')
                );

                /**
                 * Récupération des textes dans les TR
                 * */
                const currentElementsSelected = currentTrSelected.map((trElement) =>
                    trElement.childNodes[1].firstChild.firstChild.firstChild.textContent.trim()
                );

                /**
                 * On supprime les attributs "data-selected_delete" des TR
                 * */
                currentTrSelected.forEach((element) => element.removeAttribute('data-selected_delete'));

                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;

                if (currentElementsSelected) this.sortUpdateSelectedElements(currentElementsSelected);
            },
            sortUpdateSelectedElements(textSelectedElements) {
                /**
                 * On boucle sur tous les éléments qui sont triés
                 * */
                [...this.filteredElements].forEach((element, index) => {
                    /**
                     * On boucle sur les éléments sélectionnés
                     * */
                    textSelectedElements.forEach((textContent) => {
                        /**
                         * Si le texte de l'élément trié est égale au texte de l'élément sélectionné
                         * */
                        if (element.nom === textContent) {
                            const trElement = this.getTrDomElementByIndex(index);
                            trElement.toggleAttribute('data-selected_delete');
                        }
                    });
                });
            },
            getTrDomElementByIndex(index) {
                /**
                 * On retourne le TR dans le tbody avec l'index passé en paramètres
                 * */
                const tbodyElement = document.querySelector('.table-grid-container table tbody');
                return [...tbodyElement.children][index];
            },

            tooltipClick(evt) {
                const actionType = evt.target.dataset.action;

                switch (actionType) {
                    case 'infos':
                        this.openDocumentModal();
                        break;
                    case 'delete':
                        this.deleteDocument();
                        break;
                    case 'select':
                        this.selectDocument();
                        break;
                    default:
                        break;
                }
                evt.preventDefault();
            },

            cancelMultipleDeletions() {
                Array.from(window.document.querySelectorAll('tr[data-selected_delete]')).forEach((trElement) =>
                    trElement.removeAttribute('data-selected_delete')
                );
                this.deletionsSelected = this.getDeletionsSelected();
            },
            deleteMultipleDeletions() {
                Swal.fire({
                    icon: 'question',
                    text: `Êtes-vous sûr de vouloir supprimer les éléments sélectionnés ?`,
                    confirmButtonText: 'Oui, supprimer.',
                    cancelButtonText: 'Non, ne pas supprimer.',
                    confirmButtonColor: '#f25e5e',
                    showCancelButton: true,
                }).then((value) => {
                    if (value.value !== undefined && value.value) this.deleteDocuments();
                });
            },
            getDeletionsSelected() {
                return Array.from(window.document.querySelectorAll('tr[data-selected_delete]')).map((nodeElement) => [
                    {
                        name: nodeElement.children[1].firstChild.firstChild.firstChild.textContent.trim(),
                        id: parseInt(nodeElement.children[1].firstChild.firstChild.firstChild.dataset.id),
                    },
                ]);
            },
            removeDeletionsSelected() {
                Array.from(window.document.querySelectorAll('tr[data-selected_delete]')).forEach((nodeElement) =>
                    nodeElement.removeAttribute('data-selected_delete')
                );
            },
            selectDocument(evt) {
                if (evt) {
                    if (this.multipleDeletions) {
                        const trElement = evt.target.parentNode.parentNode.parentNode.parentNode;

                        trElement.toggleAttribute('data-selected_delete');
                        this.deletionsSelected = this.getDeletionsSelected();
                    } else {
                        if (evt.target.nodeName === 'BUTTON') {
                            const trElement = evt.target.parentNode.parentNode.parentNode.parentNode.parentNode;

                            trElement.toggleAttribute('data-selected_delete');
                            this.deletionsSelected = this.getDeletionsSelected();
                        }
                    }
                } else {
                    const spanElement = this.popperNode;
                    const trElement = spanElement.parentNode.parentNode.parentNode.parentNode;

                    if (this.multipleDeletions) {
                        const trElement = spanElement.parentNode.parentNode.parentNode.parentNode;

                        trElement.toggleAttribute('data-selected_delete');
                        this.deletionsSelected = this.getDeletionsSelected();
                    } else {
                        trElement.toggleAttribute('data-selected_delete');
                        this.deletionsSelected = this.getDeletionsSelected();
                    }
                }
            },
            async deleteDocuments() {
                this.removeDeletionsSelected();
                for await (let elem of this.deletionsSelected) {
                    const documentId = elem[0].id;
                    const arrayId = this.elements.findIndex((document) => document.other.id === documentId);

                    const result = await documentAPI.deleteByID(documentId, this.$store.getters.token);
                    if (result.tokens !== null) {
                        EventBus.$emit('expiredJWT', result.tokens);
                    }
                    if (result.status) {
                        this.elements.splice(arrayId, 1);
                        EventBus.$emit('sendToast', {
                            type: 'success',
                            message: 'Document bien supprimé.',
                        });
                    } else {
                        EventBus.$emit('sendToast', {
                            type: 'success',
                            message: 'Document non supprimé.',
                        });
                    }
                }
            },
            deleteDocument() {
                const spanElement = this.popperNode;
                Swal.fire({
                    title: 'Êtes-vous sûr de vouloir supprimer le document ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'Non',
                    confirmButtonText: 'Oui',
                }).then((result) => {
                    if (result.value) {
                        const documentId = parseInt(spanElement.dataset.id);
                        const arrayId = this.elements.findIndex((document) => document.other.id === documentId);

                        documentAPI.deleteByID(documentId, this.$store.getters.token).then((result) => {
                            if (result.tokens !== null) {
                                EventBus.$emit('expiredJWT', result.tokens);
                            }
                            if (result.status) {
                                this.elements.splice(arrayId, 1);
                                EventBus.$emit('sendToast', {
                                    type: 'success',
                                    message: 'Document bien supprimé.',
                                });
                            } else {
                                EventBus.$emit('sendToast', {
                                    type: 'success',
                                    message: 'Document non supprimé.',
                                });
                            }

                            EventBus.$emit('loaderRequest', false);
                        });
                    }
                });
            },

            openDocumentModal() {
                const spanElement = this.popperNode;
                const document_id = spanElement.dataset.id;
                const document_location = spanElement.dataset.location;
                const document_name = spanElement.dataset.name;
                const document_size = spanElement.dataset.size;
                const document_user = {
                    id: spanElement.dataset.user_id,
                    firstname: spanElement.dataset.user_firstname,
                    lastname: spanElement.dataset.user_lastname,
                };

                const ulElementParent = window.document.createElement('ul');
                ulElementParent.style.textAlign = 'left';
                ulElementParent.innerHTML += '<h3>Document</h3>';

                Object.entries({
                    identifiant: document_id,
                    emplacement: document_location,
                    nom: document_name,
                    taille: document_size,
                    user: {
                        identifiant: document_user.id,
                        prenom: document_user.firstname,
                        nom: document_user.lastname,
                    },
                }).forEach((elem) => {
                    const keyElem = elem[0];
                    const dataElem = elem[1];

                    if (keyElem !== 'user') {
                        const liElement = window.document.createElement('li');
                        liElement.innerText = `${this.$options.filters.capitalize(keyElem)} : ${dataElem}`;
                        ulElementParent.appendChild(liElement);
                    } else {
                        const brElement = window.document.createElement('br');
                        ulElementParent.appendChild(brElement);
                        ulElementParent.innerHTML += '<h3>Utilisateur</h3>';

                        for (let keyUser in dataElem) {
                            const liElement = window.document.createElement('li');

                            liElement.innerText = `${
                                keyUser === 'prenom' ? 'Prénom' : this.$options.filters.capitalize(keyUser)
                            } : ${dataElem[keyUser]}`;

                            ulElementParent.appendChild(liElement);
                        }
                    }
                });
                Swal.fire({
                    html: ulElementParent,
                });
            },

            show(evt) {
                evt.preventDefault();
                if (this.previousPopper !== null) {
                    this.hide(this.previousPopper);
                }

                const button = evt.target;
                const tooltip = document.querySelector('.tooltip.table');
                this.popperNode = button;

                this.previousPopper = tooltip;
                tooltip.setAttribute('data-show', '');
                this.create(tooltip, button);
            },
            hide(tooltip) {
                tooltip.removeAttribute('data-show');
                this.destroy();
            },
            create(tooltip, button) {
                this.popperInstance = createPopper(button, tooltip, {
                    placement: 'left',
                    modifiers: [
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 10],
                            },
                        },
                    ],
                });
            },
            destroy() {
                if (this.popperInstance) {
                    this.popperInstance.destroy();
                    this.popperInstance = null;
                    this.popperNode = null;
                }
            },
        },
        mounted() {
            document.body.addEventListener('click', (evt) => {
                evt.stopPropagation();
                if (this.previousPopper !== null) {
                    if (evt.target.childNodes.length >= 1) {
                        const tooltip = evt.target.childNodes[1] || null;
                        if (!tooltip) this.hide(this.previousPopper);
                        else if (!tooltip.isSameNode(this.previousPopper)) this.hide(this.previousPopper);
                    }
                }
            });
            window.addEventListener('keydown', (evt) => {
                this.multipleDeletions = evt.code === 'ControlLeft' ? true : this.multipleDeletions;
            });
            window.addEventListener('keyup', (evt) => {
                this.multipleDeletions = evt.code === 'ControlLeft' ? false : this.multipleDeletions;
            });
        },
    };
</script>

<style scoped lang="scss">
    @import '../../styles/Table';
    @import '../../styles/ContextMenu';

    .tooltip {
        width: 170px !important;
    }

    .table-grid-container {
        table {
            tbody {
                tr {
                    -webkit-user-select: auto;
                    -moz-user-select: auto;
                    -ms-user-select: auto;
                    user-select: auto;

                    td:nth-child(1) {
                        padding-left: 10px !important;
                    }
                    td:nth-child(2) {
                        width: 170px !important;
                        padding-left: 8px !important;
                    }

                    td:nth-child(3) {
                        max-width: 100% !important;

                        padding: 0 !important;
                    }
                    td:nth-child(4),
                    td:nth-child(5),
                    td:nth-child(6) {
                        padding: 0 !important;
                    }

                    &[data-selected_delete] {
                        background-color: rgba(201, 10, 10, 0.32);
                    }
                }
            }
        }
    }
</style>

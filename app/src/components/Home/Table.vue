<template>
    <div id="tableServer">
        <div id="multipleDeletionsActions" v-if="deletionsSelected.length > 0">
            <h6>
                {{ deletionsSelected.length }} éléments sélectionnés
                <button class="btn-blue rounded-perso-2" @click="deleteMultipleDeletions">Supprimer</button>
                <button class="btn-blue rounded-perso-2" @click="cancelMultipleDeletions">
                    Annuler
                </button>
            </h6>
        </div>
        <div class="table-grid-container">
            <div id="container-server" v-if="elements.length !== 0">
                <button id="goBackArrow" data-action="goBack" @click="onClickBtn"></button>
                <div
                    v-for="element in filteredElements"
                    class="item"
                    :key="element.name"
                    :data-type="element.type"
                    :data-name="element.name"
                    :data-date="element.date"
                    :data-size="element.size"
                    @dblclick="elementOnDblClick"
                    @mouseover="hoverTargetElement = $event.target"
                    @mouseleave="(hoverTargetElement = null), hideToolTip"
                >
                    <img v-if="element.type === 'file'" src="../../assets/file.svg" alt="icon" />
                    <img v-else-if="element.type === 'folder'" src="../../assets/folder.svg" alt="icon" />
                    <img v-else-if="element.type === 'image'" src="../../assets/image.svg" alt="icon" />
                    <img v-else-if="element.type === 'symlink'" src="../../assets/symlink.svg" alt="icon" />
                    <i
                        v-if="
                            element.type === 'file' &&
                            [...usersInEditor].findIndex((item) => item.filename === element.name) !== -1
                        "
                        @mouseover="tooltipFileEditor"
                        @mouseleave="tooltipFileEditor"
                    ></i>
                    <h6 v-on-hover="'overflow-ellipsis'" class="overflow-ellipsis animated fadeIn">
                        {{ element.name }}
                    </h6>
                    <h6 v-on-hover="'overflow-ellipsis'" class="overflow-ellipsis h6-date animated fadeIn">
                        {{ element.date }}
                    </h6>
                    <h6 v-on-hover="'overflow-ellipsis'" class="overflow-ellipsis h6-size animated fadeIn">
                        {{ element.size | sizeParser }}
                    </h6>
                    <div class="buttonsList animated fadeIn">
                        <!-- Sélectionner -->
                        <button
                            class="rounded-perso-2"
                            data-action="select"
                            :data-type="element.type"
                            :data-name="element.name"
                            @click="onClickBtn"
                            @mouseover="showToolTip"
                            @mouseleave="hideToolTip"
                        >
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 15 15"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12.85.25h-8.4c-.772 0-1.4.628-1.4 1.4v8.4c0 .772.628 1.4 1.4 1.4h8.4c.772 0 1.4-.628 1.4-1.4v-8.4c0-.772-.628-1.4-1.4-1.4zm-8.4 9.8v-8.4h8.4l.001 8.4H4.45z"
                                    fill="#9B9CAA"
                                />
                                <path
                                    d="M1.65 4.45H.25v8.4c0 .772.628 1.4 1.4 1.4h8.4v-1.4h-8.4v-8.4zm6.253 2.463L6.695 5.705l-.99.99 2.292 2.292 3.991-4.788-1.076-.898-3.009 3.612z"
                                    fill="#9B9CAA"
                                />
                            </svg>
                            <div class="tooltip" role="tooltip">
                                Séléctionner
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </button>

                        <span v-if="element.type === 'file'">
                            <!-- Editer un fichier-->
                            <button
                                v-if="isAdmin"
                                class="rounded-perso-2"
                                data-action="editFile"
                                :data-type="element.type"
                                :data-name="element.name"
                                @click="onClickBtn"
                                @mouseover="showToolTip"
                                @mouseleave="hideToolTip"
                            >
                                <svg width="15" height="12" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.25 6a3 3 0 100-6 3 3 0 000 6zm2.1.75h-.392a4.084 4.084 0 01-3.417 0H3.15A3.15 3.15 0 000 9.9v.975C0 11.495.504 12 1.125 12h6.443a1.125 1.125 0 01-.061-.5l.16-1.427.027-.26 1.997-1.997A3.118 3.118 0 007.35 6.75zm1.062 3.405l-.16 1.43a.373.373 0 00.413.412l1.427-.159 3.232-3.232-1.68-1.68-3.232 3.23zm6.424-3.853l-.889-.888a.562.562 0 00-.792 0l-.982.982 1.683 1.68.98-.98a.565.565 0 000-.794z"
                                        fill="#9B9CAA"
                                    />
                                </svg>
                                <div class="tooltip" role="tooltip">
                                    Éditer
                                    <div class="arrow" data-popper-arrow></div>
                                </div>
                            </button>
                        </span>
                        <span v-if="element.type === 'file' || element.type === 'image'">
                            <!-- Preview Image / Fichier -->
                            <button
                                class="rounded-perso-2"
                                data-action="preview"
                                :data-type="element.type"
                                :data-name="element.name"
                                @click="onClickBtn"
                                @mouseover="showToolTip"
                                @mouseleave="hideToolTip"
                            >
                                <svg
                                    width="19"
                                    height="15"
                                    viewBox="0 0 19 15"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M6.984 7.725a2.405 2.405 0 104.81 0 2.405 2.405 0 00-4.81 0zm11.728-.554C16.677 2.883 13.6.725 9.475.725 5.348.725 2.273 2.883.238 7.173a1.295 1.295 0 000 1.106c2.035 4.288 5.112 6.446 9.237 6.446 4.127 0 7.202-2.158 9.237-6.449a1.295 1.295 0 000-1.105zM9.39 11.504a3.78 3.78 0 110-7.559 3.78 3.78 0 010 7.559z"
                                        fill="#9B9CAA"
                                    />
                                </svg>
                                <div class="tooltip" role="tooltip">
                                    Prévisualiser
                                    <div class="arrow" data-popper-arrow></div>
                                </div>
                            </button>
                        </span>

                        <!-- Renommer -->
                        <button
                            class="rounded-perso-2"
                            data-action="rename"
                            :data-type="element.type"
                            :data-name="element.name"
                            @click="onClickBtn"
                            @mouseover="showToolTip"
                            @mouseleave="hideToolTip"
                        >
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 15 15"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12.853 3.046h-.7v1.4h.7v5.6h-.7v1.4h.7c.772 0 1.4-.627 1.4-1.4v-5.6c0-.77-.628-1.4-1.4-1.4zm-9.8 2.8H9.35v2.8H3.053v-2.8z"
                                    fill="#9B9CAA"
                                />
                                <path
                                    d="M10.753 1.65h2.097V.25h-5.6v1.4h2.103v1.397h-7.7c-.772 0-1.4.627-1.4 1.4v5.6c0 .772.628 1.4 1.4 1.4h7.7v1.403H7.25v1.4h5.6v-1.4h-2.097V1.65zm-9.1 8.396v-5.6h7.7v5.6h-7.7z"
                                    fill="#9B9CAA"
                                />
                            </svg>
                            <div class="tooltip" role="tooltip">
                                Renommer
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </button>

                        <!-- Déplacer -->
                        <button
                            class="rounded-perso-2"
                            data-action="move"
                            :data-type="element.type"
                            :data-name="element.name"
                            @click="onClickBtn"
                            @mouseover="showToolTip"
                            @mouseleave="hideToolTip"
                        >
							<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.45 6.55h-3.5v-3.5h2.1L7.25.25l-2.8 2.8h2.1v3.5h-3.5v-2.1l-2.8 2.8 2.8 2.8v-2.1h3.5v3.5h-2.1l2.8 2.8 2.8-2.8h-2.1v-3.5h3.5v2.1l2.8-2.8-2.8-2.8v2.1z" fill="#9B9CAA"/></svg>
                            <div class="tooltip" role="tooltip">
                                Déplacer vers
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </button>

                        <!-- Télécharger -->
                        <button
                            class="rounded-perso-2"
                            data-action="download"
                            :data-type="element.type"
                            :data-name="element.name"
                            @click="onClickBtn"
                            @mouseover="showToolTip"
                            @mouseleave="hideToolTip"
                        >
                            <svg
                                width="13"
                                height="15"
                                viewBox="0 0 13 15"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12.167 5.542H9.056V.875H4.389v4.667H1.278l5.444 6.222 5.445-6.222zM.5 13.319h12.444v1.556H.5v-1.556z"
                                    fill="#9B9CAA"
                                />
                            </svg>
                            <div class="tooltip" role="tooltip">
                                Télécharger
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </button>
                        <!-- Supprimer -->
                        <button
                            class="rounded-perso-2"
                            data-action="delete"
                            :data-type="element.type"
                            :data-name="element.name"
                            @click="onClickBtn"
                            @mouseover="showToolTip"
                            @mouseleave="hideToolTip"
                        >
                            <svg
                                width="12"
                                height="15"
                                viewBox="0 0 12 15"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M1.278 12.944A1.556 1.556 0 002.833 14.5h6.223a1.556 1.556 0 001.555-1.556V3.611H1.278v9.333zM3.19 7.407L4.288 6.31l1.656 1.649 1.65-1.649L8.69 7.407 7.041 9.056l1.649 1.648-1.097 1.097-1.649-1.649-1.648 1.65-1.097-1.098 1.649-1.648-1.657-1.65zm5.476-6.13L7.889.5H4l-.778.778H.5v1.555h10.889V1.278H8.667z"
                                    fill="#9B9CAA"
                                />
                            </svg>
                            <div class="tooltip" role="tooltip">
                                Supprimer
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <span v-if="!isLoading && elements.length === 0">
                Le dossier est vide...
                <button class="btn-blue rounded-perso-2" data-action="goBack" @click="onClickBtn">
                    Retour.
                </button>
            </span>
        </div>
    </div>
</template>

<script>
    import { createPopper } from '@popperjs/core';
    import { EventBus } from '../EventBus';

    export default {
        name: 'Table-Home',
        props: {
            elements: Array,
            columns: Array,
            filterKey: String,
            isLoading: Boolean,
            usersEditFiles: Array,
            toggleMode: Number,
            bus: Object,
        },
        data() {
            let sortOrders = {};
            this.columns.forEach((key) => {
                if (key !== 'type') sortOrders[key] = 1;
            });
            return {
                sortKey: '',
                sortOrders: sortOrders,
                hoverTargetElement: null,
                popperInstance: null,
                popperInstanceEditor: null,
                popperNode: null,
                popperTarget: null,
                popperStyleBtnsNode: null,
                popperNodeEditor: null,
                previousPopper: null,
                deletionsSelected: [],
            };
        },
        directives: {
            'on-hover': {
                bind(el, binding, vNode) {
                    el.addEventListener('mouseover', () => {
                        el.classList.remove(binding.value);
                    });
                    el.addEventListener('mouseleave', () => {
                        el.classList.add(binding.value);
                    });
                },
                unbind(el, binding, vNode) {
                    el.removeEventListener('mouseenter', () => {
                        el.classList.remove(binding.value);
                    });
                    el.removeEventListener('mouseleave', () => {
                        el.classList.add(binding.value);
                    });
                },
            },
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
                        a = a[sortKey].toLowerCase();
                        b = b[sortKey].toLowerCase();
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    });
                }
                return elements;
            },
            usersInEditor() {
                return this.usersEditFiles;
            },
            isAdmin() {
                return this.$store.getters.isAdmin;
            },
        },
        filters: {
            capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            },
            upperCase(str) {
                return str.toUpperCase();
            },
            sizeParser(str) {
                return (str += ' Bytes');
            },
        },
        watch: {
            sortKey(key) {
                this.sortOrders[key] = this.sortOrders[key] * -1;
            },
            initSortKey(newValue, oldValue) {
                console.log(newValue, oldValue);
            },
        },
        methods: {
            tooltipFileEditor(evt) {
                /**
                 * La fonction est appelé au passage de la sourie sur le texte "Fichier en cours d'édition",
                 * et aussi quand la sourie sort de ce texte.
                 * */
                const targetElement = evt.target;

                switch (evt.type) {
                    case 'mouseover':
                        // Création du Popper avec son style
                        const filename = targetElement.parentNode.dataset.name;

                        const tooltipElement = document.createElement('span');
                        const tooltipArrowElement = document.createElement('span');
                        tooltipArrowElement.classList.add('arrow');
                        tooltipArrowElement.toggleAttribute('data-popper-arrow');

                        // Récupération des utilisateurs
                        let usersList = '<h3 style="padding: 0; margin: 0; text-align: center">Utilisateur(s) : </h3>';
                        this.usersEditFiles.forEach((item) => {
                            if (item.filename === filename)
                                usersList += `<p style="padding: 0; margin: 0">${item.email}</p>`;
                        });

                        tooltipElement.setAttribute('role', 'tooltip');
                        tooltipElement.innerHTML = usersList;
                        tooltipElement.appendChild(tooltipArrowElement);
                        tooltipElement.setAttribute('data-show', '');
                        tooltipElement.classList.add('tooltip');

                        // Style du tooltip
                        tooltipElement.style.backgroundColor = '#333';
                        tooltipElement.style.color = 'white';
                        tooltipElement.style.padding = '0px 5px';
                        tooltipElement.style.width = '170px';
                        tooltipElement.style.textAlign = 'center';
                        tooltipElement.style.fontSize = '13px';
                        tooltipElement.style.borderRadius = '4px';
                        tooltipElement.style.position = 'absolute';
                        tooltipElement.style.left = '0';
                        tooltipElement.style.zIndex = '4';

                        targetElement.parentElement.appendChild(tooltipElement);
                        this.popperNodeEditor = tooltipElement;

                        this.popperInstanceEditor = createPopper(targetElement, tooltipElement, {
                            placement: 'bottom',
                            modifiers: [
                                {
                                    name: 'offset',
                                    options: {
                                        offset: [0, 10],
                                    },
                                },
                            ],
                        });
                        break;
                    case 'mouseleave':
                        // Destruction du popper
                        targetElement.parentElement.removeChild(this.popperNodeEditor);
                        this.popperInstanceEditor.destroy();
                        this.popperInstanceEditor = null;
                        this.popperNodeEditor = null;
                        break;
                    default:
                        break;
                }
            },

            /**
             * Fonctions qui gère la selection multiples
             * */
            cancelMultipleDeletions() {
                Array.from(window.document.querySelectorAll('div[data-selected_delete]')).forEach((trElement) =>
                    trElement.removeAttribute('data-selected_delete')
                );
                this.deletionsSelected = this.getSelectedDeletions();
            },
            deleteMultipleDeletions() {
                EventBus.$emit('SSH_deleteElements', this.deletionsSelected);
            },
            getSelectedDeletions() {
                return Array.from(window.document.querySelectorAll('div[data-selected_delete]')).map(
                    (nodeElement) => nodeElement.dataset.name
                );
            },

            /**
             * Quand l'utilisateur clique sur un bouton dans un élément du serveur
             * */
            onClickBtn(evt) {
                let actionTarget = this.hoverTargetElement || evt.target;

                /**
                 * On récupère dans le dom le bouton
                 * */
                if (actionTarget?.nodeName.toLowerCase() === 'svg') actionTarget = this.hoverTargetElement.parentNode;
                else if (actionTarget?.nodeName.toLowerCase() === 'path')
                    actionTarget = this.hoverTargetElement.parentNode.parentNode;

                let actionType = actionTarget.dataset?.action;

                /**
                 * Selon l'action, on envoi des évènements au composant parent (Home.vue)
                 * */
                switch (actionType) {
                    case 'goBack':
                        EventBus.$emit('SSH_goBack');
                        break;
                    case 'delete':
                        EventBus.$emit('SSH_deleteElement', {
                            name: actionTarget.dataset?.name,
                        });
                        break;
                    case 'download':
                        if (actionTarget.dataset?.type === 'file' || actionTarget.dataset?.type === 'image') {
                            EventBus.$emit('SSH_downloadElement', {
                                name: actionTarget.dataset?.name,
                            });
                        } else {
                            EventBus.$emit('SSH_downloadZIP', {
                                name: actionTarget.dataset?.name,
                            });
                        }
                        break;
                    case 'preview':
                        if (actionTarget.dataset?.type === 'file') {
                            EventBus.$emit('SSH_previewFile', {
                                name: actionTarget.dataset?.name,
                            });
                        } else {
                            EventBus.$emit('SSH_previewImage', {
                                name: actionTarget.dataset?.name,
                                type: '',
                            });
                        }
                        break;
                    case 'rename':
                        EventBus.$emit('SSH_renameElement', {
                            name: actionTarget.dataset?.name,
                        });
                        break;
                    case 'editFile':
                        EventBus.$emit('SSH_editFile', {
                            name: actionTarget.dataset?.name,
                        });
                        break;
                    case 'select':
                        const baseElement = actionTarget.parentElement.parentElement;
                        if (baseElement.nodeName.toLowerCase() === 'div') {
                            baseElement.toggleAttribute('data-selected_delete');
                            this.deletionsSelected = this.getSelectedDeletions();
                        }
                        break;
					case 'move':
						EventBus.$emit('SSH_moveElement', {
							name: actionTarget.dataset?.name
						});
						break;
                    default:
                        break;
                }
            },

            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
            },

            /**
             * Au click sur des éléments du tableau
             * */
            elementOnDblClick(evt) {
                evt.preventDefault();

                const nodeName = evt.target.nodeName;
                const nodeType = evt.target.dataset.type;
                if (nodeName.toLocaleLowerCase() === 'div') {
                    if (nodeType === 'symlink') {
                        let folderName = evt.target.dataset.name.split('->')[0];
                        if (folderName.charAt(folderName.length - 1) !== '/') folderName += '/';
                        EventBus.$emit('SSH_openDir', {
                            name: folderName,
                        });
                    } else if (nodeType === 'folder') {
                        const folderName = evt.target.dataset.name;
                        EventBus.$emit('SSH_openDir', {
                            name: folderName,
                        });
                    }
                }
            },

            /**
             * Initialisation des événements
             * */
            initEventsListeners() {
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
            },

            showToolTip(evt) {
                let actionTarget = evt.target;

                if (actionTarget.nodeName.toLowerCase() === 'svg') actionTarget = actionTarget.parentNode;
                else if (actionTarget.nodeName.toLowerCase() === 'path')
                    actionTarget = actionTarget.parentNode.parentNode;

                const tooltip = actionTarget.children[1];
                if (tooltip && tooltip?.nodeName?.toLowerCase() === 'div') {
                    tooltip.setAttribute('data-show', '');

                    this.popperInstance = createPopper(actionTarget, tooltip, {
                        placement: 'top',
                        modifiers: [
                            {
                                name: 'offset',
                                options: {
                                    offset: [0, 7],
                                },
                            },
                        ],
                    });
                }
            },
            hideToolTip(evt = null) {
                if (evt) {
                    let actionTarget = evt.target;

                    if (actionTarget.nodeName.toLowerCase() === 'svg') actionTarget = actionTarget.parentNode;
                    else if (actionTarget.nodeName.toLowerCase() === 'path')
                        actionTarget = actionTarget.parentNode.parentNode;

                    const tooltip = actionTarget.children[1];

                    tooltip.removeAttribute('data-show');
                }

                if (this.popperInstance) {
                    this.popperInstance.destroy();
                    this.popperInstance = null;
                }
            },
        },
        mounted() {
            this.initEventsListeners();
            this.bus.$on('sortKey', (key) => {
                this.sortBy(key);
            });
        },
    };
</script>

<style lang="scss" scoped>
    #goBackArrow {
        width: 20px;
        height: 20px;

        border: 0;
        outline: 0;
        margin: 0;
        padding: 0;

        background-color: transparent;
        background-image: url('../../assets/left-arrow.svg');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 100%;

        position: absolute;
        left: 10px;
        top: 5px;
    }
    .overflow-ellipsis {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        max-width: 100%;
        display: inline-block;
    }

    .tooltip {
        background: #333;
        color: white;

        font-weight: bold;
        padding: 4px 8px;
        font-size: 13px;
        border-radius: 4px;

        word-break: normal !important;

        display: none;

        z-index: 999;
        position: absolute;

        &[data-show] {
            display: block;
        }

		&[data-popper-placement^='top'] > .arrow {
			bottom: -4px;
		}

		&[data-popper-placement^='bottom'] > .arrow {
			top: -4px;
		}

		&[data-popper-placement^='left'] > .arrow {
			right: -4px;
		}

		&[data-popper-placement^='right'] > .arrow {
			left: -4px;
		}

		.arrow {
			position: absolute;
			width: 8px;
			height: 8px;
			z-index: -1;

			&::before {
				position: absolute;
				width: 8px;
				height: 8px;
				z-index: -1;

				content: '';
				transform: rotate(45deg);
				background: #333;
			}
		}
    }

    #container-server {
        width: auto;
        height: auto;

        max-width: 800px;
        max-height: 700px;

        padding: 20px;
        box-sizing: border-box;

        border-radius: 5px;

        background-color: var(--color-home-server-bg);
        overflow-y: auto;
        overflow-x: hidden;

        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;

        position: relative;

        .item {
            width: 100%;
            height: 50px;

            box-sizing: border-box;
            transition: all 0.2s ease-in-out;
            margin: 5px 25px;

            word-break: break-all;

            background-color: var(--color-main-blue-07);

            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-around;

            transition: all 0.2s ease-in-out;
            border-radius: 7px;

            img {
                width: 25px;
                height: auto;

                pointer-events: none;
                margin: 0 10px;
            }

            i {
                width: 20px;
                height: 20px;

                position: absolute;
                top: 5px;
                left: 5px;

                border-radius: 10px;

                background-image: url('../../assets/edit.svg');
                background-repeat: no-repeat;
                background-position: center;
                background-size: auto;
            }

            h6 {
                width: 150px;

                padding: 0;
                margin: 0;

                color: white;
                font-size: 0.8rem;
                pointer-events: auto;
                cursor: text;

                &:first-of-type {
                    width: 200px;
                }

                &:not(:first-of-type) {
                    text-align: center;
                }

                &.h6-size,
                &.h6-date {
                    display: block;
                }
            }

            &[data-selected_delete] {
                background-color: rgba(223, 17, 17, 0.59);
            }

            &:hover {
                cursor: pointer;
                z-index: 4;

                .buttonsList {
                    display: flex;
                }

                h6 {
                    &.h6-size,
                    &.h6-date {
                        display: none;
                    }
                }
            }

            .buttonsList {
                max-width: 250px;
                width: auto;
                height: 50px;

                position: relative;
                display: none;

                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;

                transition: all 0.2s ease-in-out;

                button {
                    height: 25px;
                    min-width: 30px;

                    margin: 0 2.5px;

                    font-weight: bold;
                    color: var(--color-text-blue);

                    border: none;
                    position: relative;

                    background-color: var(--color-main-white);

                    svg {
                        margin-top: 2px;
                    }
                }
            }
        }
    }
</style>

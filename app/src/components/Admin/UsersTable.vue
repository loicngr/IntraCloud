<template>
    <div class="table-grid-container animated fadeIn">
        <table v-if="!isLoading && elements.length !== 0">
            <thead>
                <tr>
                    <th v-for="key in columns" :key="key" :class="{ active: sortKey === key }">
                        <span v-if="key !== 'icone' && key !== 'actions'" @click="sortBy(key)">
                            {{ key | upperCase }}
                            <span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"> </span>
                        </span>
                        <span v-else-if="key === 'actions'">
                            {{ key | upperCase }}
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(entry, keyEntry) in filteredElements" :key="entry.email" class="animated fadeIn">
                    <td v-for="(element, keyElement) in entry" :key="element">
                        <span v-if="element === '-_user_-'" @click="openUserModal" :data-email="entry.email.trim()">
                            <div aria-describedby="tooltip" @mouseenter="show" @mouseleave="hide" class="iconeUser">
                                <svg
                                    width="15"
                                    height="17"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="animated"
                                >
                                    <path
                                        d="M7.5 7.51c-2.352 0-2.828-3.563-2.828-3.563C4.392 2.093 5.242 0 7.469 0c2.237 0 3.087 2.093 2.807 3.947 0 0-.424 3.563-2.776 3.563zm0 2.663l2.818-1.886c2.476 0 4.682 2.414 4.682 4.693v2.58s-3.781 1.17-7.5 1.17c-3.781 0-7.5-1.17-7.5-1.17v-2.58c0-2.33 2.01-4.64 4.63-4.64l2.87 1.833z"
                                        fill="#9B9CAA"
                                    />
                                </svg>
                                <div class="tooltip" role="tooltip">
                                    Plus d'informations
                                    <div class="arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </span>
                        <span v-else-if="element === '-_actions_-'">
                            <span v-if="editMod && editedUser.email === entry.email.trim()">
                                <button
                                    class="rounded-perso-2"
                                    data-action="validate"
                                    aria-describedby="tooltip"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="updateUser"
                                >
                                    <svg width="14" height="12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M4.79 11.44L.396 7.044l2.003-2.003 2.393 2.4L11.784.44l2.004 2.003L4.79 11.44z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Valider
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                                <button
                                    class="rounded-perso-2"
                                    data-action="cancel"
                                    aria-describedby="tooltip"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="cancelUpdate"
                                >
                                    <svg width="12" height="12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M6 0a6 6 0 110 12A6 6 0 016 0zm0 1.2a4.8 4.8 0 00-3.792 7.746l6.738-6.738A4.803 4.803 0 006 1.2zm0 9.6a4.8 4.8 0 003.792-7.746L3.054 9.792A4.803 4.803 0 006 10.8z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Annuler
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                            </span>

                            <span v-else-if="!editMod && $store.getters.user.email !== entry.email.trim()">
                                <button
                                    class="rounded-perso-2"
                                    data-action="delete"
                                    :data-email="entry.email.trim()"
                                    aria-describedby="tooltip"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="deleteUser"
                                >
                                    <svg width="10" height="13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.167 11.167A1.333 1.333 0 002.5 12.5h5.333a1.334 1.334 0 001.334-1.333v-8h-8v8zm1.64-4.747l.94-.94 1.42 1.413L6.58 5.48l.94.94-1.413 1.413L7.52 9.247l-.94.94-1.413-1.414-1.414 1.414-.94-.94 1.414-1.414-1.42-1.413zM7.5 1.167L6.833.5H3.5l-.667.667H.5V2.5h9.333V1.167H7.5z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Supprimer
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                                <button
                                    class="rounded-perso-2"
                                    data-action="edit"
                                    aria-describedby="tooltip"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="editUser"
                                >
                                    <svg width="15" height="12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5.25 6a3 3 0 100-6 3 3 0 000 6zm2.1.75h-.392a4.084 4.084 0 01-3.417 0H3.15A3.15 3.15 0 000 9.9v.975C0 11.495.504 12 1.125 12h6.443a1.125 1.125 0 01-.061-.5l.16-1.427.027-.26 1.997-1.997A3.118 3.118 0 007.35 6.75zm1.062 3.405l-.16 1.43a.373.373 0 00.413.412l1.427-.159 3.232-3.232-1.68-1.68-3.232 3.23zm6.424-3.853l-.889-.888a.562.562 0 00-.792 0l-.982.982 1.683 1.68.98-.98a.565.565 0 000-.794z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Modifier
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                            </span>
                            <span v-else>
                                C'est vous.
                            </span>
                        </span>
                        <span v-else-if="keyElement === 'role'">
                            <span
                                v-if="
                                    editMod &&
                                    editedUser.email === entry.email.trim() &&
                                    $store.getters.user.email !== editedUser.email
                                "
                            >
                                <span class="dropdown-check-list">
                                    <span
                                        @click="
                                            $event.target.nextSibling.style.display =
                                                $event.target.nextSibling.style.display === 'block' ? 'none' : 'block'
                                        "
                                    >
                                        Liste des Roles
                                    </span>
                                    <ul class="animated fadeIn">
                                        <li v-for="role in listRoles">
                                            <input
                                                type="radio"
                                                name="roles"
                                                :value="role"
                                                :id="role"
                                                :checked="(role === roleChanger(element))"
                                                @change="roleClicked"
                                            />
                                            <label :for="role">{{ role }}</label>
                                        </li>
                                    </ul>
                                </span>
                            </span>
                            <span v-else>{{ roleChanger(element) }}</span>
                        </span>
                        <span v-else> {{ element }} </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <span v-else-if="!isLoading && elements.length === 0">
            Il n'y a aucun utilisateurs...
        </span>
    </div>
</template>

<script>
    import Swal from 'sweetalert2';
    import { EventBus } from '../EventBus';

    import { createPopper } from '@popperjs/core';

    import { User } from '@/api/User.js';
    import { formatDate } from '../../utils/functions.js';

    const userAPI = new User();
    export default {
        name: 'UsersTable',
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
                updateLoading: false,
                sortKey: 'nom',
                sortOrders: sortOrders,
                popperInstance: null,
                previousPopper: null,
                editMod: false,
                editedUser: {
                    node: null,
                    email: null,
                    roles: null,
                },
            };
        },
        computed: {
            listRoles() {
                const roles = [];

                if (typeof process.env.VUE_APP_ROLES !== undefined) {
                    if (process.env.VUE_APP_ROLES !== null) {
                        process.env.VUE_APP_ROLES.split('/').filter((role) => {
                            switch (role) {
                                case 'ROLE_USER':
                                    roles.push('Membre');
                                    break;
                                case 'ROLE_NOT_VERIFIED':
                                    roles.push('Non vérifié');
                                    break;
                                case 'ROLE_ADMIN':
                                    roles.push('Administrateur');
                                    break;
                                default:
                                    break;
                            }
                        });
                        return roles.reverse();
                    }
                }
                EventBus.$emit('sendToast', {
                    type: 'error',
                    message: "Aucun rôles n'est défini dans le fichier d'environement.",
                });
            },
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
        },
        methods: {
            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
            },
            openUserModal(evt) {
                const baseParentElement = evt.target.parentNode;
                const spanElement =
                    baseParentElement.nodeName === 'SPAN'
                        ? baseParentElement
                        : baseParentElement.parentNode.nodeName === 'SPAN'
                        ? baseParentElement.parentNode
                        : baseParentElement.parentNode.parentNode;

                const userEmail = spanElement.dataset.email;
                if (userEmail) {
                    userAPI
                        .byEmail(userEmail, this.$store.getters.token)
                        .then((result) => {
                            if (result.tokens !== null) {
                                EventBus.$emit('expiredJWT', result.tokens);
                            }
                            if (result.status) {
                                const user = result.data;

                                const ulElement = document.createElement('ul');
                                ulElement.style.textAlign = 'left';
                                let date;

                                for (let key in user) {
                                    const liElement = document.createElement('li');
                                    switch (key) {
                                        case 'created_at':
                                            date = formatDate(user[key].date);
                                            liElement.innerText = `Date de création : ${date}`;
                                            break;
                                        case 'updated_at':
                                            if (user[key] !== null) {
                                                date = formatDate(user[key].date);
                                                liElement.innerText = `Date de mise à jour : ${date}`;
                                            } else {
                                                liElement.innerText = `Date de mise à jour : N'a jamais été mis à jour.`;
                                            }
                                            break;
                                        case 'firstname':
                                            liElement.innerText = `Prénom : ${user[key]}`;
                                            break;
                                        case 'lastname':
                                            liElement.innerText = `Nom : ${user[key]}`;
                                            break;
                                        case 'roles':
                                            liElement.innerText = `Rôles : ${user[key]}`;
                                            break;
                                        case 'verified':
                                            liElement.innerText = `Compte vérifié ? : ${user[key] ? 'Oui' : 'Non'}`;
                                            break;
                                        case 'id':
                                            liElement.innerText = `Identifiant : ${user[key]}`;
                                            break;
                                        case 'email':
                                            liElement.innerText = `Email : ${user[key]}`;
                                            break;
                                        default:
                                            break;
                                    }
                                    ulElement.appendChild(liElement);
                                }

                                Swal.fire({
                                    icon: 'info',
                                    title: `Informations sur ${user.firstname}.`,
                                    html: ulElement,
                                });
                            }
                        })
                        .catch((err) => console.error(err));
                }
            },
            roleChanger(role, mode = 1) {
                const roles = role.split(' - ');
                if (roles.length > 1) role = roles.shift();

                switch (mode) {
                    case 1:
                        switch (role) {
                            case 'ROLE_USER':
                                return 'Membre';
                            case 'ROLE_NOT_VERIFIED':
                                return 'Non vérifié';
                            case 'ROLE_ADMIN':
                                return 'Administrateur';
                            default:
                                break;
                        }
                        break;
                    case 2:
                        switch (role) {
                            case 'Membre':
                                return 'ROLE_USER';
                            case 'Non vérifié':
                                return 'ROLE_NOT_VERIFIED';
                            case 'Administrateur':
                                return 'ROLE_ADMIN';
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
            },
            roleClicked(evt) {
                const ROLE = evt.target.value;
                this.editedUser.roles = this.roleChanger(ROLE, 2);
            },
            editUser(evt) {
                this.editMod = true;
                const baseParentElement = evt.target.parentNode;

                const trElement =
                    baseParentElement.nodeName === 'TR'
                        ? baseParentElement
                        : baseParentElement.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode
                        : baseParentElement.parentNode.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode.parentNode
                        : baseParentElement.parentNode.parentNode.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode.parentNode.parentNode
                        : baseParentElement.parentNode.parentNode.parentNode.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode.parentNode.parentNode.parentNode
                        : baseParentElement.parentNode.parentNode.parentNode.parentNode.parentNode;

                const userEmail = trElement.children.item(2).children.item(0).textContent.trim();
                const userRoles = trElement.children.item(3).children.item(0).textContent.trim();

                this.editedUser.node = trElement;
                this.editedUser.email = userEmail;
                this.editedUser.roles = this.roleChanger(userRoles, 2);

                this.editedUser.node.setAttribute('data-edit', '');
            },
            deleteUser(evt) {
                const baseParentElement = evt.target;

                const buttonElement =
                    baseParentElement.nodeName === 'BUTTON'
                        ? baseParentElement
                        : baseParentElement.parentNode.nodeName === 'BUTTON'
                        ? baseParentElement.parentNode
                        : baseParentElement.parentNode.parentNode;

                const userEmail = buttonElement.dataset.email;
                if (userEmail) {
                    Swal.fire({
                        title: 'Êtes-vous sûr de supprimer cette utilisateur ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        cancelButtonText: 'Non',
                        confirmButtonText: 'Oui',
                    }).then((result) => {
                        EventBus.$emit('loaderRequest', true);
                        if (result.value) {
                            userAPI
                                .byEmail(userEmail, this.$store.getters.token)
                                .then((result) => {
                                    if (result.tokens !== null) {
                                        EventBus.$emit('expiredJWT', result.tokens);
                                    }
                                    if (result.status) {
                                        const user = result.data;

                                        userAPI
                                            .deleteUser(user.id, this.$store.getters.token)
                                            .then((result) => {
                                                if (result.tokens !== null) {
                                                    EventBus.$emit('expiredJWT', result.tokens);
                                                }
                                                if (result.status) {
                                                    if (result.data === 'user deleted.') {
                                                        EventBus.$emit('loaderRequest', false);
                                                        EventBus.$emit('sendToast', {
                                                            type: 'success',
                                                            message: 'Utilisateur bien supprimé.',
                                                        });
                                                        EventBus.$emit('tableUserUpdated');
                                                    }
                                                }
                                            })
                                            .catch((err) => console.error(err));
                                    }
                                })
                                .catch((err) => console.error(err));
                        } else {
                            EventBus.$emit('loaderRequest', false);
                        }
                    });
                }
            },
            updateUser() {
                EventBus.$emit('loaderRequest', true);
                userAPI
                    .byEmail(this.editedUser.email, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) {
                            EventBus.$emit('expiredJWT', result.tokens);
                        }
                        if (result.status) {
                            const user = result.data;
                            const userRole = this.roleChanger(user.roles.join(' - '));

                            userAPI
                                .updateUser(
                                    {
                                        id: user.id,
                                        firstname: null,
                                        lastname: null,
                                        email: null,
                                        role: userRole !== this.editedUser.roles ? this.editedUser.roles : null,
                                        password: null,
                                    },
                                    this.$store.getters.token
                                )
                                .then((result) => {
                                    if (result.tokens !== null) {
                                        EventBus.$emit('expiredJWT', result.tokens);
                                    }
                                    if (result.status) {
                                        EventBus.$emit('loaderRequest', false);
                                        EventBus.$emit('sendToast', {
                                            type: 'success',
                                            message: 'Mise à jour effectué.',
                                            timer: 6000,
                                        });
                                        this.editedUser.node.removeAttribute('data-edit');
                                        this.editedUser.node = null;
                                        this.editedUser.email = null;
                                        this.editedUser.roles = null;
                                        this.editMod = false;
                                        EventBus.$emit('tableUserUpdated');
                                    }
                                })
                                .catch((err) => console.error(err));
                        }
                    })
                    .catch((err) => console.error(err));
            },
            cancelUpdate() {
                this.editedUser.roles = null;
                this.editedUser.node.removeAttribute('data-edit');
                this.editedUser.node = null;
                this.editedUser.email = null;
                this.editMod = false;
            },
            show(evt) {
                const button = evt.target;
                const tooltip = button.children[1];

                tooltip.setAttribute('data-show', '');
                this.create(tooltip, button);
            },
            hide(evt) {
                const button = evt.target;
                const tooltip = button.children[1];

                tooltip.removeAttribute('data-show');
                this.destroy();
            },
            create(tooltip, button) {
                this.popperInstance = createPopper(button, tooltip, {
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
            },
            destroy() {
                if (this.popperInstance) {
                    this.popperInstance.destroy();
                    this.popperInstance = null;
                }
            },
        },
    };
</script>

<style lang="scss" scoped>
    @import '../../styles/Table';
    @import '../../styles/SelectInput';

    .iconeUser {
        svg {
            animation-duration: 7s;
            animation-delay: 1s;
            animation-name: iconeUserFlash;
            animation-iteration-count: infinite;
        }
    }
    @keyframes iconeUserFlash {
        0%,
        25% {
            opacity: 1;
        }
        25%,
        50% {
            opacity: 0.5;
        }
        50%,
        100% {
            opacity: 1;
        }
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

        z-index: 5;

        &[data-show] {
            display: block;
        }
        .arrow {
            position: absolute;
            width: 8px;
            height: 8px;
            z-index: -1;
            bottom: -4px;
            left: -4px !important;

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
    .table-grid-container {
        table {
            tbody {
                tr {
                    &[data-edit] {
                        background-color: rgba(192, 192, 192, 0.377);
                    }

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
                    td:nth-child(4) {
                        padding: 0 !important;
                    }
                    td:nth-child(5) {
                        padding: 0 !important;

                        button {
                            height: 20px;
                            width: 40px;

                            margin: 0 2.5px;

                            font-weight: bold;
                            color: white;

                            border: none;
                            position: relative;

                            background-color: var(--color-main-white);
                            transition: all 0.2s ease-in-out;

                            svg {
                                padding: 2px 0 0 0;
                                path {
                                    fill: var(--color-text-blue);
                                    opacity: 0.8;
                                }
                            }

                            &:hover {
                                transform: scale(1.05);
                                cursor: pointer;
                            }

                            &.btn-disabled {
                                &:hover {
                                    cursor: not-allowed;
                                    transform: none;
                                    background-color: rgba(192, 192, 192, 0.42) !important;
                                }
                                background-color: rgba(192, 192, 192, 0.42);
                            }

                            &[data-action='delete'],
                            &[data-action='cancel'] {
                                &:hover {
                                    background-color: var(--color-main-red);
                                }
                            }
                            &[data-action='edit'] {
                                &:hover {
                                    background-color: var(--color-main-blue-07);
                                }
                            }
                            &[data-action='validate'] {
                                &:hover {
                                    background-color: var(--color-main-green-05);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>

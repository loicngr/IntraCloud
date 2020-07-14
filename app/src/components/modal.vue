<template>
    <transition name="modal-fade">
        <div class="modal-backdrop" v-if="!isLoading">
            <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription">
                <header class="modal-header" id="modalTitle">
                    {{ title }}
                    <button type="button" class="btn-close" @click="close" aria-label="Close modal">
                        x
                    </button>
                </header>
                <section class="modal-body" id="modalDescription">
                    <div v-html="textContent"></div>
                </section>
                <footer class="modal-footer">
                    <button type="button" class="btn-blue" v-for="btn of buttons" @click="btn.cb">
                        {{ btn.name }}
                    </button>
                </footer>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: 'modal',
        props: {
            content: Object,
        },
        data() {
            return {
                buttons: {},
                title: null,
                textContent: null,
                isLoading: true,
            };
        },
        methods: {
            close() {
                this.$emit('close');
            },
        },
        mounted() {
            const buttons = this.content.footer.buttons;
            this.title = this.content.title;
            this.textContent = this.content.html.outerHTML;
            for (let btn in buttons) {
                this.buttons[btn] = {
                    name: '',
                    cb: null,
                };

                const value = buttons[btn].split(',');
                this.buttons[btn].name = value[0];

                switch (value[1]) {
                    case 'close':
                        this.buttons[btn].cb = this.close;
                    default:
                        this.buttons[btn].cb = this.close;
                        break;
                }
            }
            this.isLoading = false;
        },
    };
</script>

<style scoped lang="scss">
    .modal-fade-enter,
    .modal-fade-leave-active {
        opacity: 0;
    }

    .modal-fade-enter-active,
    .modal-fade-leave-active {
        transition: opacity 0.5s ease;
    }

    .modal-backdrop {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.3);
        display: flex;
        justify-content: center;
        align-items: center;

        z-index: 999;
    }

    .modal {
        max-width: 90vw;

        background: #ffffff;
        box-shadow: 2px 2px 20px 1px;
        overflow-x: auto;
        display: flex;
        flex-direction: column;
    }

    .modal-header,
    .modal-footer {
        padding: 15px;
        display: flex;
    }

    .modal-header {
        border-bottom: 1px solid #eeeeee;
        color: #1833ff;
        justify-content: space-between;
        align-items: center;
    }

    .modal-footer {
        border-top: 1px solid #eeeeee;
        justify-content: flex-end;
    }

    .modal-body {
        max-height: 50vh;
        min-width: 200px;

        position: relative;
        padding: 20px 10px;

        overflow: auto;

        div {
            width: 100%;
        }
    }

    .btn-close {
        cursor: pointer;

        padding: 20px;
        font-size: 20px;
        font-weight: bold;

        border: none;

        background: transparent;

        color: #1833ff;
    }

    .btn-blue {
        color: white;
        background: #1833ff;
        border: 1px solid #2039b1;
        border-radius: 2px;
    }
</style>

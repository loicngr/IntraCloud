<template>
    <div id="left-navbar">
        <slot name="page_logo" />
        <slot name="pages" />
    </div>
</template>

<script>
    import { createPopper } from '@popperjs/core';

    export default {
        name: 'left-navbar',
        data() {
            return {
                popperInstance: null,
            };
        },
        methods: {
            show(tooltip, button) {
                tooltip.setAttribute('data-show', '');
                this.create(tooltip, button);
            },
            hide(tooltip) {
                tooltip.removeAttribute('data-show');
                this.destroy();
            },
            create(tooltip, button) {
                this.popperInstance = createPopper(button, tooltip, {
                    placement: 'right',
                    modifiers: [
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 20],
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
        mounted() {
            const showEvents = ['mouseenter', 'focus'];
            const hideEvents = ['mouseleave', 'blur'];

            const a_elements = document.querySelectorAll('ul > li > a');

            Array.from(a_elements).forEach((elem) => {
                const button = elem;
                const tooltip = button.nextElementSibling;
                showEvents.forEach((event) => {
                    button.addEventListener(event, () => this.show(tooltip, button));
                });

                hideEvents.forEach((event) => {
                    button.addEventListener(event, () => this.hide(tooltip));
                });
            });
        },
    };
</script>

<style lang="scss" scoped>
    @import '../styles/LeftNavbar';
</style>

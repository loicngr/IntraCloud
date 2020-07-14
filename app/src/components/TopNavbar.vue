<template>
    <div id="top-navbar">
        <slot name="page_name" />
        <slot name="user_name" />
    </div>
</template>

<script>
    export default {
        name: 'top-navbar',
    };
</script>

<style lang="scss" scoped>
    #top-navbar {
        height: var(--navbar-top-height);
        width: calc(100% - var(--navbar-left-width));

        background-color: var(--navbar-color-bg);

        position: absolute;
        top: 0;
        left: var(--navbar-left-width);

        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 30px;
        box-sizing: border-box;

        h2,
        h5 {
            color: var(--color-black);
            font-weight: 500;
        }

        h5 {
            display: flex;

            align-items: center;

            a {
                margin: 0 10px;

                display: flex;
                align-items: center;
            }
        }
    }
</style>

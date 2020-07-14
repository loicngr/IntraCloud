<template>
    <div v-if="currentElement">
        <span>{{ currentElement }} / {{ queueMaxElements }}</span>
    </div>
</template>

<script>
    export default {
        name: 'queueViewer',
        props: {
            currentElement: Number,
            queueMaxElements: Number,
        },
    };
</script>

<style lang="scss" scoped>
    div {
        width: 200px;
        height: 200px;

        color: var(--color-main-blue);

        position: fixed;
        top: calc(45vh - 150px);
        left: calc(50vw - 100px);
        z-index: 9999;

        display: flex;
        justify-content: center;
        align-items: center;

        font-size: 2rem;
        transition: all 0.2s ease-in-out;
    }
</style>

<template>
    <div class="notification animated fadeInDown" v-if="show">
        <header>
            <h3><slot name="title" /></h3>
            <button @click="show = false">X</button>
        </header>
        <main>
            <p><slot name="message" /></p>
        </main>
        <footer><slot name="buttons" /></footer>
    </div>
</template>

<script>
    export default {
        name: 'Notifications',
        data() {
            return {
                show: true,
            };
        },
    };
</script>

<style scoped lang="scss">
    div.notification {
        width: 400px;
        min-height: 100px;
        height: auto;

        background-color: var(--notification-color-bg);
        color: white;

        z-index: 9;

        position: absolute;
        top: 5px;
        left: 5px;

        border-radius: 7px;

        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;

        overflow: hidden;

        box-shadow: black 2px 2px 10px;

        header {
            width: 100%;

            h3 {
                text-transform: uppercase;
                margin: 10px 0;
            }

            button {
                width: 20px;
                height: 20px;

                border: none;
                border-radius: 20px;
                background-color: white;

                position: absolute;
                top: 5px;
                right: 5px;

                color: var(--notification-color-bg);
                font-family: 'Montserrat Bold';
            }
        }
        main {
            p {
                width: 100%;

                padding: 0px 10px 5px 10px;
                box-sizing: border-box;
            }
        }
        footer {
            width: 50%;

            display: flex;
            justify-content: space-around;

            margin: 5px 0;

            button {
                width: auto;
                height: auto;

                border: none;
                border-radius: 20px;
                background-color: white;
                color: var(--notification-color-bg);

                font-family: 'Montserrat Bold';
                font-weight: bold;

                text-transform: uppercase;
                padding: 2.5px 10px;

                transition: all 0.2s ease-in-out;
            }
        }
    }
</style>

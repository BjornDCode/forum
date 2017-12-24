<template>
    <button type="submit" :class="classes" @click="toggle">
        <span class="glyphicon glyphicon-heart"></span>
        <span v-text="count"></span>
    </button>
</template>

<script>
    export default {
        props: ['reply'],

        data() {
            return {
                count: this.reply.favoritesCount,
                isFavorited: this.reply.isFavorited
            }
        },

        computed: {
            classes() {
                return ['btn', this.isFavorited ? 'btn-primary' : 'btn-default'];
            },

            endpoint() {
                return '/replies/' + this.reply.id + '/favorites';
            }
        },

        methods: {
            toggle() {
                 this.isFavorited ? this.destroy() : this.create();
            },

            create() {
                axios.post(this.endpoint);

                this.isFavorited = true;
                this.count++;
            },

            destroy() {
                axios.delete(this.endpoint);

                this.isFavorited = false;
                this.count--;
            }
        }
    }
</script>

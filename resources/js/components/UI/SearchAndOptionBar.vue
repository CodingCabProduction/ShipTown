<template>
    <div ref="stickyDiv" class="sticky-container row mb-2 pl-1 pr-1 bg-white d-flex flex-nowrap" :class="{ 'sticky-top': isStickable, 'py-2': isStuckToTop}">
        <div class="flex-fill">
            <slot/>
        </div>
        <slot name="buttons"></slot>
        <button v-if="isStuckToTop && isStickable" type="button" class="btn btn-primary ml-1 md:ml-2" @click="scrollToTop">
            <font-awesome-icon icon="angles-up" class="fa-lg"></font-awesome-icon>
        </button>
    </div>
</template>

<script>
import url from "../../mixins/url";

export default {
    mixins: [url],
    props: {
        isStickable: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            isStuckToTop: false
        }
    },
    mounted() {
        this.$eventBus.$on('observer-status', (isVisible) => {
            this.isStuckToTop = !isVisible;
        });
    },
    methods: {
        scrollToTop() {
            window.scrollTo(0, 0);
        }
    },
}
</script>

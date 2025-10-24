<template>
    <template v-if="isPageTableContent">
        <div class="dashboard-page-content-container">
            <slot></slot>
        </div>
    </template>
    <template v-else>
        <slot></slot>
    </template>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        isPageTableContent: {
            default: true,
        },
    },
    setup() {
        return {};
    },
});
</script>

<template>
    <div
        :style="{
            padding: '10px 16px',
            background: themeMode == 'dark' ? '#141414' : '#fff',
        }"
        class="mb-20"
    >
        <slot></slot>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import common from "../../common/composable/common";

export default defineComponent({
    setup() {
        const { themeMode } = common();

        return {
            themeMode,
        };
    },
});
</script>

<template>
    <a-switch
        checkedValue="horizontal"
        unCheckedValue="vertical"
        :checked="menuMode"
        @change="menuModeChanged"
        size="small"
    />
</template>

<script>
import { ref, defineComponent } from "vue";
import { useAuthStore } from "../../main/store/authStore";
import common from "../../common/composable/common";

export default defineComponent({
    setup() {
        const { cssSettings } = common();
        const authStore = useAuthStore();
        const menuMode = ref(cssSettings.value.headerMenuMode);

        const menuModeChanged = (mode) => {
            authStore.updateCssSettings({
                ...cssSettings.value,
                headerMenuMode: mode,
            });
        };

        return {
            menuMode,
            menuModeChanged,
        };
    },
});
</script>

<style></style>

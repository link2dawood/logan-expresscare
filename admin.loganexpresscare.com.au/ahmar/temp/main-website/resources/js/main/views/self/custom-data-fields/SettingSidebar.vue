<template>
    <div class="setting-sidebar">
        <perfect-scrollbar
            :options="{
                wheelSpeed: 1,
                swipeEasing: true,
                suppressScrollX: true,
            }"
        >
            <a-menu v-model:selectedKeys="selectedKeys">
                <a-menu-item
                    v-for="fieldType in fieldTypesData"
                    :key="fieldType.xid"
                    :title="fieldType.name"
                    @click="handleClick(fieldType)"
                >
                    {{ fieldType.name }}
                </a-menu-item>
            </a-menu>
        </perfect-scrollbar>
    </div>
</template>

<script>
import { defineComponent, ref, watch } from "vue";
import common from "../../../../common/composable/common";

export default defineComponent({
    props: {
        fieldTypesData: {
            type: Array,
            default: () => [],
        },
    },

    setup(props, { emit }) {
        const { permsArray, appType } = common();
        const selectedKeys = ref([]);

        const handleClick = (fieldType) => {
            selectedKeys.value = [fieldType.xid];
            emit("sectionClicked", fieldType.xid);
        };

        watch(
            () => props.fieldTypesData,
            (newVal) => {
                if (newVal.length > 0 && selectedKeys.value.length === 0) {
                    const firstKey = newVal[0].xid;
                    selectedKeys.value = [firstKey];
                    emit("sectionClicked", firstKey);
                }
            },
            { immediate: true }
        );

        return {
            permsArray,
            selectedKeys,
            appType,
            handleClick,
        };
    },
});
</script>

<style lang="less">
.setting-sidebar .ps {
    height: calc(100vh - 145px);
}
</style>

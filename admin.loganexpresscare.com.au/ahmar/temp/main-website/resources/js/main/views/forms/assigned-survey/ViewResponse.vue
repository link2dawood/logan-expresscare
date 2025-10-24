<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
    >
        <div class="bg-white">
            <a-row type="flex">
                <a-col :span="20">
                    <a-list
                        item-layout="horizontal"
                        :data-source="surveyOfEmployee.employee_survey"
                    >
                        <template #renderItem="{ item }">
                            <a-list-item>
                                <a-list-item-meta :description="item.employee_answer">
                                    <template #title>
                                        <h3>{{ item.question }}</h3>
                                    </template>
                                </a-list-item-meta>
                            </a-list-item>
                        </template>
                        <a-divider />
                    </a-list>
                </a-col>
            </a-row>
        </div>
        <template #footer>
            <a-space>
                <a-button key="back" type="primary" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch } from "vue";

import apiAdmin from "../../../../common/composable/apiAdmin";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    components: {},
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const surveyOfEmployee = ref({});

        const onClose = () => {
            emit("close");
        };

        watch(
            () => props.data,

            (newVal, oldVal) => {
                surveyOfEmployee.value = newVal;
            }
        );

        return {
            loading,
            rules,
            onClose,
            surveyOfEmployee,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

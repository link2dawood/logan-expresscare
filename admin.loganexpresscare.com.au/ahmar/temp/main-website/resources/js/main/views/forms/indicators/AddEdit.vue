<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('indicator.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('indicator.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <FormItemHeading>
                {{ $t("indicator.fields") }}
            </FormItemHeading>

            <template v-if="indicators.length > 0">
                <a-row :gutter="16" v-for="(indi, index) in indicators" :key="indi.id">
                    <a-col :xs="24" :sm="24" :md="22" :lg="22">
                        <a-form-item :name="['fields', index, 'name']">
                            <a-input
                                v-model:value="indi.name"
                                :placeholder="$t('indicator.fields')"
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="2">
                        <MinusCircleOutlined @click="removeFormField(field)" />
                    </a-col>
                </a-row>
            </template>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item>
                        <a-button
                            type="dashed"
                            block
                            @click="addFormField"
                            :disabled="addFormButtonStatus"
                        >
                            <PlusOutlined />
                            {{ $t("indicator.fields") }}
                        </a-button>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import {
    PlusOutlined,
    MinusCircleOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import { some, filter } from "lodash-es";
import apiAdmin from "../../../../common/composable/apiAdmin";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        MinusCircleOutlined,
        LoadingOutlined,
        SaveOutlined,
        FormItemHeading,
    },
    setup(props, { emit }) {
        const indicators = ref([{ name: "", id: "" }]);
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const removeFormField = (item) => {
            let index = indicators.value.indexOf(item);
            if (index !== -1) {
                indicators.value.splice(index, 1);
            }
        };

        const addFormField = () => {
            indicators.value.push({
                name: "",
                id: "",
            });
        };

        const onSubmit = () => {
            const allFields = filter(indicators.value, (newField) => {
                return newField.name != "";
            });
            addEditRequestAdmin({
                url: props.url,
                data: {
                    ...props.formData,
                    fields: allFields,
                },
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const addFormButtonStatus = computed(() => {
            if (indicators.value.length == 0) {
                return false;
            } else {
                return some(indicators.value, { name: "" });
            }
        });

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                indicators.value = [];
                if (props.addEditType == "edit" && newVal) {
                    indicators.value = props.data.fields;
                } else {
                    indicators.value = [];
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            indicators,

            removeFormField,
            addFormField,
            addFormButtonStatus,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

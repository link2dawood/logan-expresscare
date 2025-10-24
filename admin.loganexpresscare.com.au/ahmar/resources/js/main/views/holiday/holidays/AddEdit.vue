<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('holiday.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('holiday.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('holiday.date')"
                        name="date"
                        :help="rules.date ? rules.date.message : null"
                        :validateStatus="rules.date ? 'error' : null"
                        class="required"
                    >
                        <a-date-picker
                            v-model:value="formData.date"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('holiday.is_half_day')"
                        name="is_half_day"
                        :help="rules.is_half_day ? rules.is_half_day.message : null"
                        :validateStatus="rules.is_half_day ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.is_half_day"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="formData.is_half_day == 1"
                >
                    <a-form-item
                        :label="$t('holiday.half_holiday')"
                        name="half_holiday"
                        :help="rules.half_holiday ? rules.half_holiday.message : null"
                        :validateStatus="rules.half_holiday ? 'error' : null"
                    >
                        <a-select
                            v-model:value="formData.half_holiday"
                            button-style="solid"
                            size="medium"
                        >
                            <a-select-option value="before_break">
                                {{ $t("common.before_break") }}
                            </a-select-option>
                            <a-select-option value="after_break">
                                {{ $t("common.after_break") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, onMounted, ref } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";

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
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, disabledDate, permsArray } = common();

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
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

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

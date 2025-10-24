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
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('warning.user')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.user_id"
                                :placeholder="
                                    $t('common.select_default_text', [$t('warning.user')])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                            <UserAddButton @onAddSuccess="userAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('warning.title')"
                        name="title"
                        :help="rules.title ? rules.title.message : null"
                        :validateStatus="rules.title ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('warning.title'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-form-item
                    :label="$t('warning.warning_date')"
                    name="warning_date"
                    :help="rules.warning_date ? rules.warning_date.message : null"
                    :validateStatus="rules.warning_date ? 'error' : null"
                    class="required"
                >
                    <DateTimePicker
                        :dateTime="formData.warning_date"
                        @dateTimeChanged="
                            (changedDateTime) => (formData.warning_date = changedDateTime)
                        "
                        :disabledDate="false"
                    />
                </a-form-item>
            </a-col>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('termination.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('termination.description'),
                                ])
                            "
                            :rows="4"
                        />
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
    </a-modal>
</template>

<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import common from "../../../../../common/composable/common";
import UserAddButton from "../../../../views/staff-members/users/StaffAddButton.vue";
import DateTimePicker from "../../../../../common/components/common/calendar/DateTimePicker.vue";

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
        UserAddButton,
        DateTimePicker,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting } = common();
        const users = ref([]);
        const userUrl = "self/users";

        onMounted(() => {
            const userPromise = axiosAdmin.get(userUrl);
            Promise.all([userPromise]).then(([userResponse]) => {
                users.value = userResponse.data;
            });
        });

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

        const userAdded = () => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
            });
        };

        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,
            users,
            userAdded,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

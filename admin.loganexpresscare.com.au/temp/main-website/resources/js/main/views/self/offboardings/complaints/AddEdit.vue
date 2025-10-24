<template>
    <a-modal :open="visible" :closable="false" :centered="true" :title="pageTitle">
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('complaint.title')"
                        name="title"
                        :help="rules.title ? rules.title.message : null"
                        :validateStatus="rules.title ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('complaint.title'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('complaint.date_time')"
                        name="date_time"
                        :help="rules.date_time ? rules.date_time.message : null"
                        :validateStatus="rules.date_time ? 'error' : null"
                        class="required"
                    >
                        <DateTimePicker
                            :dateTime="timeDate"
                            @dateTimeChanged="
                                (changedDateTime) => {
                                    timeDate = changedDateTime;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('complaint.to_user_id')"
                        name="to_user_id"
                        :help="rules.to_user_id ? rules.to_user_id.message : null"
                        :validateStatus="rules.to_user_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.to_user_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('complaint.to_user_id'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="user in staffMembers"
                                    :key="user.xid"
                                    :value="user.xid"
                                    :title="user.name"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('complaint.proff_of_document')"
                        name="proff_of_document"
                        :help="
                            rules.proff_of_document
                                ? rules.proff_of_document.message
                                : null
                        "
                        :validateStatus="rules.proff_of_document ? 'error' : null"
                    >
                        <Upload
                            :formData="formData"
                            folder="complaint"
                            imageField="proff_of_document"
                            @onFileUploaded="
                                (file) => {
                                    formData.proff_of_document = file.file;
                                    formData.proff_of_document_url = file.file_url;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('complaint.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('complaint.description'),
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
import DateTimePicker from "../../../../../common/components/common/calendar/DateTimePicker.vue";
import { reject } from "lodash-es";
import Upload from "../../../../../common/core/ui/file/Upload.vue";
import { useAuthStore } from "../../../../../main/store/authStore";

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
        DateTimePicker,
        Upload,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, dayjs } = common();
        const timeDate = ref();
        const authStore = useAuthStore();
        const staffMembers = ref([]);
        const users = ref([]);
        const userUrl = "self/users";
        const loggedUser = authStore.user;

        onMounted(() => {
            const userPromise = axiosAdmin.get(userUrl);
            Promise.all([userPromise]).then(([userResponse]) => {
                users.value = userResponse.data;
                reSetUsers();
            });
        });

        const reSetUsers = () => {
            staffMembers.value = reject(users.value, { xid: loggedUser.xid });
        };

        const onSubmit = () => {
            var newFormData = {
                ...props.formData,
                date_time: timeDate.value,
            };
            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    timeDate.value = undefined;
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

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (props.addEditType == "add") {
                    timeDate.value = dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ");
                }
                if (props.addEditType == "edit") {
                    timeDate.value = props.data.date_time;
                }
            }
        );

        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,

            userAdded,
            staffMembers,
            users,
            timeDate,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

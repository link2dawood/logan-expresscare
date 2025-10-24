<template>
    <a-drawer
        :open="visible"
        :width="drawerWidth"
        :closable="true"
        :centered="true"
        :title="pageTitle"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('news.title')"
                        name="title"
                        :help="rules.title ? rules.title.message : null"
                        :validateStatus="rules.title ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="newFormData.title"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('news.title')])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <a-form-item
                        :label="$t('news.visible_to_all')"
                        name="visible_to_all"
                        :help="rules.visible_to_all ? rules.visible_to_all.message : null"
                        :validateStatus="rules.visible_to_all ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="visibleToAll"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('news.visible_to_all'),
                                ])
                            "
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :key="1" :value="1">
                                {{ $t("common.all") }}
                            </a-radio-button>
                            <a-radio-button :key="0" :value="0">
                                {{ $t("common.select_employee") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="16" :lg="16" v-if="visibleToAll == 0">
                    <a-form-item
                        :label="$t('news.user_id')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="selectedUser"
                            :placeholder="
                                $t('common.select_default_text', [$t('news.user_id')])
                            "
                            :allowClear="true"
                            mode="multiple"
                            show-search
                            optionFilterProp="title"
                        >
                            <a-select-option
                                v-for="user in users"
                                :key="user.xid"
                                :value="user.xid"
                                :title="user.name"
                                ><user-list-display :user="user" whereToShow="select" />
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('news.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="newFormData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('news.description'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('news.status')"
                        name="status"
                        :help="rules.status ? rules.status.message : null"
                        :validateStatus="rules.status ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="newFormData.status"
                            :placeholder="
                                $t('common.select_default_text', [$t('news.status')])
                            "
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button value="draft">
                                {{ $t("common.draft") }}
                            </a-radio-button>
                            <a-radio-button value="published">
                                {{ $t("common.published") }}
                            </a-radio-button>
                        </a-radio-group>
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
import { defineComponent, onMounted, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import Upload from "../../../common/core/ui/file/Upload.vue";
import common from "../../../common/composable/common";
import { forEach } from "lodash-es";
import UserListDisplay from "../../../common/components/user/UserListDisplay.vue";

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
        Upload,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting } = common();
        const users = ref([]);
        const userUrl = "users";
        const visibleToAll = ref(1);
        const selectedUser = ref([]);
        const newFormData = ref({});

        onMounted(() => {
            const userPromise = axiosAdmin.get(userUrl);
            Promise.all([userPromise]).then(([userResponse]) => {
                users.value = userResponse.data;
            });
        });

        const onSubmit = () => {
            var newForm = {
                ...newFormData.value,
                user_id: selectedUser.value,
                visible_to_all: visibleToAll.value,
            };
            addEditRequestAdmin({
                url: props.url,
                data: newForm,
                successMessage: props.successMessage,
                success: (res) => {
                    selectedUser.value = [];
                    visibleToAll.value = 1;
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,

            (newVal, oldVal) => {
                if (props.addEditType == "add") {
                    newFormData.value = {
                        ...props.formData,
                    };
                    selectedUser.value = [];
                    visibleToAll.value = 1;
                } else {
                    newFormData.value = {
                        ...props.formData,
                        id: props.data.xid,
                    };

                    visibleToAll.value = props.data.visible_to_all;
                    if (props.data.news_user.length > 0) {
                        selectedUser.value = [];
                        forEach(props.data.news_user, (employee) => {
                            selectedUser.value.push(employee.user.xid);
                        });
                    }
                }
            }
        );

        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,
            users,
            visibleToAll,
            selectedUser,
            newFormData,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

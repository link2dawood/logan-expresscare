<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="$t('asset.lent_to')"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('asset.user_id')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="userId"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('asset.user_id'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                    :title="user.name"
                                >
                                    <user-list-display-vue
                                        :user="user"
                                        whereToShow="select"
                                    />
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('asset.lend_date')"
                        name="lend_date"
                        :help="rules.lend_date ? rules.lend_date.message : null"
                        :validateStatus="rules.lend_date ? 'error' : null"
                        class="required"
                    >
                        <a-date-picker
                            v-model:value="lendDate"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('asset.return_date')"
                        name="return_date"
                        :help="rules.return_date ? rules.return_date.message : null"
                        :validateStatus="rules.return_date ? 'error' : null"
                        class="required"
                    >
                        <a-date-picker
                            v-model:value="returnDate"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('asset.notes')"
                        name="notes"
                        :help="rules.notes ? rules.notes.message : null"
                        :validateStatus="rules.notes ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="notes"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('asset.notes')])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ $t("common.create") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import { useI18n } from "vue-i18n";
import UserListDisplayVue from "@/common/components/user/UserListDisplay.vue";

export default defineComponent({
    props: ["data", "visible", "pageTitle", "successMessage"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        UserListDisplayVue,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, dayjs } = common();
        const { t } = useI18n();
        const userId = ref();
        const lendDate = ref("");
        const returnDate = ref("");
        const notes = ref("");
        const users = ref([]);
        const userUrl = "users";

        onMounted(() => {
            const userPromise = axiosAdmin.get(userUrl);
            Promise.all([userPromise]).then(([userResponse]) => {
                users.value = userResponse.data;
            });
        });

        const onSubmit = () => {
            var submitData = {
                ...props.data,
                user_id: userId.value,
                lend_date: lendDate.value,
                return_date: returnDate.value,
                note: notes.value,
            };
            addEditRequestAdmin({
                url: "add-asset-to-lend",
                data: submitData,
                successMessage: t("asset.lend"),
                success: (res) => {
                    (userId.value = undefined), emit("addSuccess");
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
                lendDate.value = dayjs().format("YYYY-MM-DD");
                returnDate.value = dayjs().format("YYYY-MM-DD");
            }
        );

        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,

            userAdded,
            users,
            userId,
            lendDate,
            returnDate,
            notes,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

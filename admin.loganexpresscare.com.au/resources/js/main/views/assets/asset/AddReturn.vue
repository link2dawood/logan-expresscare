<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="$t('asset.return')"
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
                                :disabled="true"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                >
                                    <user-list-display
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
                    >
                        <a-date-picker
                            v-model:value="lendDate"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                            :disabled="true"
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('asset.return_date')"
                        name="return_date"
                        :help="rules.return_date ? rules.return_date.message : null"
                        :validateStatus="rules.return_date ? 'error' : null"
                    >
                        <a-date-picker
                            v-model:value="returnDate"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                            :disabled="true"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('asset.actual_return_date')"
                        name="actual_return_date"
                        :help="
                            rules.actual_return_date
                                ? rules.actual_return_date.message
                                : null
                        "
                        :validateStatus="rules.actual_return_date ? 'error' : null"
                    >
                        <a-date-picker
                            v-model:value="actualReturnDate"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                            :allowClear="false"
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('asset.is_broken')"
                        name="is_broken"
                        :help="rules.is_broken ? rules.is_broken.message : null"
                        :validateStatus="rules.is_broken ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="Isbroken"
                            button-style="solid"
                            size="small"
                            :placeholder="
                                $t('common.select_default_text', [$t('asset.is_broken')])
                            "
                        >
                            <a-radio-button value="1">{{
                                $t("common.yes")
                            }}</a-radio-button>
                            <a-radio-button value="0">{{
                                $t("common.no")
                            }}</a-radio-button>
                        </a-radio-group>
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
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: ["data", "asset", "visible", "pageTitle", "successMessage", "addEditType"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, dayjs } = common();
        const { t } = useI18n();
        const userId = ref();
        const assetId = ref();
        const Isbroken = ref("0");
        const lendDate = ref("");
        const returnDate = ref("");
        const actualReturnDate = ref("");
        const editOption = ref("");
        const assetUserId = ref("");
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
                lend_date: lendDate.value,
                return_date: returnDate.value,
                notes: notes.value,
                actual_return_date: actualReturnDate.value,
                asset_id: assetId.value,
                edit: editOption.value,
                is_broken: Isbroken.value,
                xid: assetUserId.value,
            };

            addEditRequestAdmin({
                url: "remove-lent-asset",
                data: submitData,
                successMessage: t("asset.returned"),
                success: (res) => {
                    emit("addSuccess");
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
                assetUserId.value = "";
                if (props.addEditType == "add") {
                    Isbroken.value = "0";
                    actualReturnDate.value = dayjs().format("YYYY-MM-DD");
                    editOption.value = "add";
                } else {
                    actualReturnDate.value = "";
                    editOption.value = "";
                }
                if (props.data.actual_return_date != null) {
                    actualReturnDate.value = props.data.actual_return_date;
                }
                userId.value = props.data.x_lent_to;
                lendDate.value = props.data.lend_date;
                returnDate.value = props.data.return_date;
                notes.value = props.data.notes;
                assetId.value = props.asset;
                assetUserId.value = props.data.xid;
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
            actualReturnDate,
            assetId,
            editOption,
            assetUserId,
            Isbroken,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

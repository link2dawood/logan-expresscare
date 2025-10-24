<template>
    <a-drawer
        :open="visible"
        :width="drawerWidth"
        :closable="true"
        :centered="true"
        :title="pageTitle"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane
                    key="complaint_details"
                    :tab="`${$t('complaint.complaint_details')}`"
                >
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
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('complaint.from_staff')"
                                name="from_user_id"
                                :help="
                                    rules.from_user_id ? rules.from_user_id.message : null
                                "
                                :validateStatus="rules.from_user_id ? 'error' : null"
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.from_user_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('complaint.from_staff'),
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
                                            ><user-list-display
                                                :user="user"
                                                whereToShow="select"
                                            />
                                        </a-select-option>
                                    </a-select>
                                    <UserAddButton @onAddSuccess="complainerAdded" />
                                </span>
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
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
                                            ><user-list-display
                                                :user="user"
                                                whereToShow="select"
                                            />
                                        </a-select-option>
                                    </a-select>
                                    <UserAddButton @onAddSuccess="userAdded" />
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
                                            formData.proff_of_document_url =
                                                file.file_url;
                                        }
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('complaint.status')"
                                name="status"
                                :help="rules.status ? rules.status.message : null"
                                :validateStatus="rules.status ? 'error' : null"
                            >
                                <a-radio-group
                                    v-model:value="formData.status"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('complaint.status'),
                                        ])
                                    "
                                    button-style="solid"
                                    size="small"
                                >
                                    <a-radio-button value="pending">
                                        {{ $t("complaint.pending") }}
                                    </a-radio-button>
                                    <a-radio-button value="approved">
                                        {{ $t("complaint.approved") }}
                                    </a-radio-button>
                                    <a-radio-button value="rejected">
                                        {{ $t("complaint.rejected") }}
                                    </a-radio-button>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('complaint.description')"
                                name="description"
                                :help="
                                    rules.description ? rules.description.message : null
                                "
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
                </a-tab-pane>
                <a-tab-pane
                    key="letterhead_template"
                    :tab="`${$t('appreciation.letterhead_template')}`"
                    force-render
                >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('appreciation.letterhead_template')"
                                name="letterhead_template_id"
                                :help="
                                    rules.letterhead_template_id
                                        ? rules.letterhead_template_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.letterhead_template_id ? 'error' : null
                                "
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.letterhead_template_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('appreciation.award'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                        @change="
                                            changeLetterHeadTitle(
                                                formData.letterhead_template_id
                                            )
                                        "
                                    >
                                        <a-select-option
                                            v-for="letterheadTemplate in letterheadTemplates"
                                            :key="letterheadTemplate.xid"
                                            :value="letterheadTemplate.xid"
                                            :title="letterheadTemplate.title"
                                        >
                                            {{ letterheadTemplate.title }}
                                        </a-select-option>
                                    </a-select>
                                    <LetterheadTemplateAddButton
                                        @onAddSuccess="letterheadTemplateAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="24"
                            :lg="24"
                            v-if="formData.letterhead_template_id != undefined"
                        >
                            <a-form-item
                                :label="$t('appreciation.letterhead_title')"
                                name="letterhead_title"
                                :help="
                                    rules.letterhead_title
                                        ? rules.letterhead_title.message
                                        : null
                                "
                                :validateStatus="rules.letterhead_title ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.letterhead_title"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('appreciation.letterhead_title'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <div v-show="formData.letterhead_template_id != undefined">
                        <LetterDescription
                            ref="textEditor"
                            descriptionLableTitle="letterhead_description"
                            :letterheadTemplateId="formData.letterhead_template_id"
                            :userId="formData.to_user_id"
                            :users="staffMembers"
                            :rules="rules"
                            :letterheadTemplates="letterheadTemplates"
                            @contentUpdated="
                                (formDescription) =>
                                    (formData.letterhead_description = formDescription)
                            "
                            :required="formData && formData.letterhead_template_id"
                            :renderListItem="['employee_keys', 'other_keys']"
                        />
                    </div>
                </a-tab-pane>
            </a-tabs>
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
import { defineComponent, onMounted, ref, watch, nextTick } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import UserAddButton from "../../../views/staff-members/users/StaffAddButton.vue";
import common from "../../../../common/composable/common";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import { reject, find } from "lodash-es";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import LetterheadTemplateAddButton from "../../../views/letter-head/template/AddButton.vue";
import LetterDescription from "@/common/components/letters/LetterDescription.vue";
import { useAuthStore } from "../../../../main/store/authStore";

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
        Upload,
        UserListDisplay,
        LetterheadTemplateAddButton,
        LetterDescription,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, dayjs } = common();
        const timeDate = ref(undefined);
        const staffMembers = ref([]);
        const users = ref([]);
        const userUrl = "users";
        const activeKey = ref("complaint_details");
        const letterheadTemplates = ref([]);
        const letterheadTemplateUrl = "letter-head-templates?limit=10000";
        const textEditor = ref(null);
        const authStore = useAuthStore();

        onMounted(() => {
            const userPromise = axiosAdmin.get(userUrl);
            const letterheadTemplatePromise = axiosAdmin.get(letterheadTemplateUrl);
            Promise.all([userPromise, letterheadTemplatePromise]).then(
                ([userResponse, letterheadTemplateResponse]) => {
                    users.value = userResponse.data;
                    letterheadTemplates.value = letterheadTemplateResponse.data;
                    reSetUsers();
                }
            );
        });

        const reSetUsers = () => {
            staffMembers.value = reject(users.value, { xid: authStore.user.xid });
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

        const userAdded = (xid) => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
                emit("addListSuccess", { type: "to_user_id", id: xid });
            });
        };

        const complainerAdded = (xid) => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
                emit("addListSuccess", { type: "from_user_id", id: xid });
            });
        };

        const changeLetterHeadTitle = (id) => {
            const letterHeadTitle = find(letterheadTemplates.value, ["xid", id]);
            props.formData.letterhead_title = letterHeadTitle.title;
        };

        const letterheadTemplateAdded = (xid) => {
            axiosAdmin.get(letterheadTemplateUrl).then((response) => {
                letterheadTemplates.value = response.data;
                emit("addListSuccess", { type: "letterhead_template_id", id: xid });
            });
        };

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                await nextTick();

                if (props.visible) {
                    activeKey.value = "complaint_details";
                    if (props.addEditType == "edit") {
                        timeDate.value = props.data.date_time;
                        if (
                            props.data &&
                            props.data.generate &&
                            props.data.generate.description
                        ) {
                            textEditor.value.reSetTemplate();
                            textEditor.value.setHTML(props.data.generate.description);
                        } else {
                            textEditor.value.setHTML("");
                        }
                    } else {
                        timeDate.value = dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ");
                        if (textEditor.value) {
                            textEditor.value.setHTML("");
                        }
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

            userAdded,
            complainerAdded,
            letterheadTemplateAdded,
            changeLetterHeadTitle,
            letterheadTemplates,
            activeKey,
            textEditor,
            staffMembers,
            users,
            timeDate,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

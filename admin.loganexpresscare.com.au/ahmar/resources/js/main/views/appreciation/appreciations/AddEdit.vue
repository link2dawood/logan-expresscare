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
        <a-form layout="vertical" id="add_edit_appreciation_form">
            <a-tabs v-model:activeKey="addEditActiveTab">
                <a-tab-pane
                    key="appreciation_details"
                    :tab="`${$t('appreciation.appreciation_details')}`"
                >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="6" :lg="6">
                            <a-form-item
                                :label="$t('appreciation.profile_image')"
                                name="profile_image"
                                :help="
                                    rules.profile_image
                                        ? rules.profile_image.message
                                        : null
                                "
                                :validateStatus="rules.profile_image ? 'error' : null"
                            >
                                <Upload
                                    :formData="formData"
                                    folder="appreciation"
                                    imageField="profile_image"
                                    @onFileUploaded="
                                        (file) => {
                                            formData.profile_image = file.file;
                                            formData.profile_image_url = file.file_url;
                                        }
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="18" :lg="18">
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('appreciation.user')"
                                        name="user_id"
                                        :help="
                                            rules.user_id ? rules.user_id.message : null
                                        "
                                        :validateStatus="rules.user_id ? 'error' : null"
                                        class="required"
                                    >
                                        <span style="display: flex">
                                            <a-select
                                                v-model:value="formData.user_id"
                                                :placeholder="
                                                    $t('common.select_default_text', [
                                                        $t('appreciation.user'),
                                                    ])
                                                "
                                                :allowClear="true"
                                                optionFilterProp="label"
                                                show-search
                                            >
                                                <a-select-option
                                                    v-for="user in users"
                                                    :key="user.xid"
                                                    :value="user.xid"
                                                    :label="user.name"
                                                >
                                                    <user-list-display
                                                        :user="user"
                                                        whereToShow="select"
                                                    />
                                                </a-select-option>
                                            </a-select>
                                            <StaffAddButton @onAddSuccess="userAdded" />
                                        </span>
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('appreciation.date')"
                                        name="date"
                                        :help="rules.date ? rules.date.message : null"
                                        :validateStatus="rules.date ? 'error' : null"
                                        class="required"
                                    >
                                        <DateTimePicker
                                            :dateTime="formData.date"
                                            @dateTimeChanged="
                                                (changedDateTime) =>
                                                    (formData.date = changedDateTime)
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('appreciation.award')"
                                        name="award_id"
                                        :help="
                                            rules.award_id ? rules.award_id.message : null
                                        "
                                        :validateStatus="rules.award_id ? 'error' : null"
                                    >
                                        <span style="display: flex">
                                            <a-select
                                                v-model:value="formData.award_id"
                                                :placeholder="
                                                    $t('common.select_default_text', [
                                                        $t('appreciation.award'),
                                                    ])
                                                "
                                                :allowClear="true"
                                                optionFilterProp="title"
                                                show-search
                                                @change="changeAward"
                                            >
                                                <a-select-option
                                                    v-for="award in awards"
                                                    :key="award.xid"
                                                    :value="award.xid"
                                                    :award="award"
                                                    :title="award.name"
                                                >
                                                    {{ award.name }}
                                                </a-select-option>
                                            </a-select>
                                            <AwardAddButton @onAddSuccess="awardAdded" />
                                        </span>
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('appreciation.price_amount')"
                                        name="price_amount"
                                        :help="
                                            rules.price_amount
                                                ? rules.price_amount.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.price_amount ? 'error' : null
                                        "
                                    >
                                        <a-input-number
                                            v-model:value="formData.price_amount"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('appreciation.price_amount'),
                                                ])
                                            "
                                            style="width: 100%"
                                        >
                                            <template #addonBefore>
                                                {{ appSetting.currency.symbol }}
                                            </template>
                                        </a-input-number>
                                    </a-form-item>
                                </a-col>
                                <a-col
                                    v-show="parseFloat(formData.price_amount) > 0"
                                    :xs="24"
                                    :sm="24"
                                    :md="24"
                                    :lg="24"
                                >
                                    <a-form-item
                                        :label="$t('appreciation.account_number')"
                                        name="account_id"
                                        :help="
                                            rules.account_id
                                                ? rules.account_id.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.account_id ? 'error' : null
                                        "
                                        :class="{
                                            required:
                                                parseFloat(formData.price_amount) > 0,
                                        }"
                                    >
                                        <span style="display: flex">
                                            <a-select
                                                v-model:value="formData.account_id"
                                                :placeholder="
                                                    $t('common.select_default_text', [
                                                        $t('appreciation.award'),
                                                    ])
                                                "
                                                :allowClear="true"
                                                optionFilterProp="title"
                                                show-search
                                            >
                                                <a-select-option
                                                    v-for="account in accounts"
                                                    :key="account.xid"
                                                    :value="account.xid"
                                                    :title="account.name"
                                                >
                                                    {{ account.name }}
                                                </a-select-option>
                                            </a-select>
                                            <AccountAddButton
                                                @onAddSuccess="accountAdded"
                                            />
                                        </span>
                                    </a-form-item>
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>

                    <FormItemHeading>
                        {{ $t("appreciation.price_given") }}
                    </FormItemHeading>

                    <a-row
                        :gutter="16"
                        v-for="formField in formFields"
                        :key="formField.id"
                        style="display: flex; align-items: center"
                    >
                        <a-col :xs="24" :sm="24" :md="23" :lg="23">
                            <a-form-item
                                :label="$t('appreciation.price_given')"
                                name="price_given"
                            >
                                <a-input
                                    v-model:value="formField.price_given"
                                    :placeholder="
                                        $t('appreciation.price_given_placeholder')
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <p v-if="rules.value" style="color: red">
                            {{ rules.value.message }}
                        </p>
                        <a-col :span="1" style="margin-top: 6px">
                            <MinusSquareOutlined @click="removeFormField(formField)" />
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item>
                                <a-button
                                    type="dashed"
                                    block
                                    @click="addFormField()"
                                    :disabled="addFormButtonStatus"
                                >
                                    <PlusOutlined />
                                    {{ $t("appreciation.add_price_given") }}
                                </a-button>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('appreciation.description')"
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
                                            $t('appreciation.description'),
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
                            :userId="formData.user_id"
                            :users="users"
                            :appreciation="{
                                price_amount: formData.price_amount,
                                price_given: formFields,
                                date: formData.date,
                                award: formData.award_id
                                    ? find(awards, { xid: formData.award_id })
                                    : undefined,
                            }"
                            :rules="rules"
                            :letterheadTemplates="letterheadTemplates"
                            @contentUpdated="
                                (formDescription) =>
                                    (formData.letterhead_description = formDescription)
                            "
                            :required="formData && formData.letterhead_template_id"
                            :renderListItem="[
                                'employee_keys',
                                'other_keys',
                                'appreciation',
                            ]"
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
import { defineComponent, onMounted, ref, computed, watch, nextTick } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import { QuillEditor } from "@vueup/vue-quill";
import { some, forEach, filter, find } from "lodash-es";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import StaffAddButton from "../../staff-members/users/StaffAddButton.vue";
import AwardAddButton from "../awards/AddButton.vue";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import AccountAddButton from "../../../views/accountings/accounts/AddButton.vue";
import LetterheadTemplateAddButton from "../../../views/letter-head/template/AddButton.vue";
import LetterDescription from "@/common/components/letters/LetterDescription.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";

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
        MinusSquareOutlined,
        UserListDisplay,
        StaffAddButton,
        AwardAddButton,
        DateTimePicker,
        FormItemHeading,
        Upload,
        AccountAddButton,
        LetterheadTemplateAddButton,
        QuillEditor,
        LetterDescription,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules, addEditActiveTab } = apiAdmin(
            "appreciation_details"
        );
        const { appSetting, disabledDate, permsArray, dayjs } = common();
        const users = ref([]);
        const usersUrl = "users";
        const allAwards = ref([]);
        const awards = ref([]);
        const accounts = ref([]);
        const accountUrl = "accounts?limit=10000";
        const awardUrl = "awards?limit=10000";
        const formFields = ref([
            {
                price_given: "",
            },
        ]);

        const letterheadTemplates = ref([]);
        const letterheadTemplateUrl = "letter-head-templates?limit=10000";
        const textEditor = ref(null);

        const removedPriceGiven = ref([]);

        onMounted(() => {
            const userPromise = axiosAdmin.get(usersUrl);
            const awardPromise = axiosAdmin.get(awardUrl);
            const accountPromise = axiosAdmin.get(accountUrl);
            const letterheadTemplatePromise = axiosAdmin.get(letterheadTemplateUrl);

            Promise.all([
                userPromise,
                awardPromise,
                accountPromise,
                letterheadTemplatePromise,
            ]).then(
                ([
                    userResponse,
                    awardResponse,
                    accountResponse,
                    letterheadTemplateResponse,
                ]) => {
                    users.value = userResponse.data;
                    allAwards.value = awardResponse.data;
                    accounts.value = accountResponse.data;
                    letterheadTemplates.value = letterheadTemplateResponse.data;
                }
            );
        });

        const changeLetterHeadTitle = (id) => {
            const letterHeadTitle = find(letterheadTemplates.value, ["xid", id]);
            props.formData.letterhead_title = letterHeadTitle.title;
        };

        const changeAward = (value, option) => {
            props.formData.price_amount =
                option && option.award && option.award.award_price
                    ? option.award.award_price
                    : undefined;
        };

        const onSubmit = () => {
            var newFormData = {
                ...props.formData,
                price_given: formFieldFilter(),
                removed_price_given: removedPriceGiven.value,
            };

            addEditRequestAdmin({
                id: "add_edit_appreciation_form",
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const addFormField = () => {
            formFields.value.push({
                price_given: "",
            });
        };

        const formFieldFilter = () => {
            var newFormField = [];

            forEach(formFields.value, (formField) => {
                if (formField.price_given != "") {
                    newFormField.push(formField);
                }
            });

            return newFormField;
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { price_given: "" }) ||
                    some(formFields.value, { price_given: null })
                );
            }
        });

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.id != "") {
                removedPriceGiven.value.push(item.id);
            }
        };

        const userAdded = (xid) => {
            axiosAdmin.get(usersUrl).then((response) => {
                users.value = response.data;
                emit("addListSuccess", { type: "user_id", id: xid });
            });
        };

        const accountAdded = (xid) => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
                emit("addListSuccess", { type: "account_id", id: xid });
            });
        };

        const letterheadTemplateAdded = (xid) => {
            axiosAdmin.get(letterheadTemplateUrl).then((response) => {
                letterheadTemplates.value = response.data;
                emit("addListSuccess", { type: "letterhead_template_id", id: xid });
            });
        };

        const awardAdded = () => {
            axiosAdmin.get(awardUrl).then((response) => {
                allAwards.value = response.data;
                refetchAwards();
            });
            // here we can not select it newly created award id since it may be inactive
        };

        const refetchAwards = () => {
            if (props.addEditType == "edit") {
                awards.value = filter(allAwards.value, (newAward) => {
                    return newAward.active || newAward.xid == props.formData.award_id;
                });
            } else {
                awards.value = filter(allAwards.value, { active: 1 });
            }
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                await nextTick();
                if (props.visible) {
                    refetchAwards();
                    addEditActiveTab.value = "appreciation_details";
                    if (props.addEditType == "edit") {
                        formFields.value = [...props.formData.price_given];

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
                        formFields.value = [];
                        if (textEditor.value) {
                            textEditor.value.setHTML("");
                        }
                    }
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,

            userAdded,
            users,
            awardAdded,
            awards,
            changeAward,
            formFields,
            removeFormField,
            addFormField,
            addFormButtonStatus,
            accountAdded,
            accounts,
            addEditActiveTab,
            letterheadTemplateAdded,
            letterheadTemplates,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            textEditor,
            find,
            changeLetterHeadTitle,
        };
    },
});
</script>

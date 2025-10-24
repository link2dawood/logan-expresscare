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
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('company_policy.location')"
                        name="location_id"
                        :help="rules.location_id ? rules.location_id.message : null"
                        :validateStatus="rules.location_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.location_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('company_policy.location'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="location in locations"
                                    :key="location.xid"
                                    :value="location.xid"
                                    :title="location.name"
                                >
                                    {{ location.name }}
                                </a-select-option>
                            </a-select>
                            <LocationAddButton @onAddSuccess="locationAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('company_policy.title')"
                        name="title"
                        :help="rules.title ? rules.title.message : null"
                        :validateStatus="rules.title ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('company_policy.title'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('company_policy.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('company_policy.description'),
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
                        :label="$t('company_policy.method_type')"
                        name="method_type"
                        :help="rules.method_type ? rules.method_type.message : null"
                        :validateStatus="rules.method_type ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.method_type"
                            name="method_type"
                            @change="onMethodChange(formData.method_type)"
                        >
                            <a-radio value="upload">{{
                                $t("company_policy.upload")
                            }}</a-radio>
                            <a-radio value="create">{{
                                $t("company_policy.create")
                            }}</a-radio>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16" v-if="formData.method_type == 'create'">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <LetterDescription
                        ref="textEditor"
                        :labelTitle="$t('company_policy.letter_description')"
                        descriptionLableTitle="letter_description"
                        :rules="rules"
                        :required="true"
                        :isLetterHeadTemplate="false"
                        @contentUpdated="
                            (formDescription) =>
                                (formData.letter_description = formDescription)
                        "
                        editorHeight="200px"
                        :renderListItem="['other_keys']"
                    />
                </a-col>
            </a-row>
            <a-row :gutter="16" v-else>
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('company_policy.policy_document')"
                        name="policy_document"
                        :help="
                            rules.policy_document ? rules.policy_document.message : null
                        "
                        :validateStatus="rules.policy_document ? 'error' : null"
                        class="required"
                    >
                        <UploadFile
                            :acceptFormat="'image/*,.pdf'"
                            :formData="formData"
                            folder="policyDocuments"
                            uploadField="policy_document"
                            @onFileUploaded="
                                (file) => {
                                    formData.policy_document = file.file;
                                    formData.policy_document_url = file.file_url;
                                }
                            "
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
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, watch, nextTick } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import UploadFile from "../../../common/core/ui/file/UploadFile.vue";
import LocationAddButton from "../settings/location/AddButton.vue";
import LetterDescription from "@/common/components/letters/LetterDescription.vue";

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
        LocationAddButton,
        UploadFile,
        LetterDescription,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const locations = ref({});
        const textEditor = ref(null);
        const locationUrl = "locations?limit=10000";

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

        onMounted(() => {
            const locationPromise = axiosAdmin.get(locationUrl);
            Promise.all([locationPromise]).then(([locationResponse]) => {
                locations.value = locationResponse.data;
            });
        });

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const locationAdded = (xid) => {
            axiosAdmin.get(locationUrl).then((response) => {
                locations.value = response.data;
                emit("addListSuccess", { type: "location_id", id: xid });
            });
        };

        const onMethodChange = async (method) => {
            await nextTick();
            if (method === "create" && props.visible) {
                textEditor.value.reSetTemplate();
                if (
                    props.addEditType == "edit" &&
                    props.data &&
                    props.data.letter_description
                ) {
                    textEditor.value.setHTML(props.data.letter_description);
                }
            }
        };

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                await nextTick();
                if (props.visible) {
                    if (textEditor.value) {
                        textEditor.value.reSetTemplate();
                        if (props.addEditType == "edit") {
                            if (props.formData.method_type == "create") {
                                textEditor.value.setHTML(
                                    props.formData.letter_description
                                );
                            }
                        } else {
                            if (props.formData.method_type == "upload") {
                                textEditor.value.setHTML("");
                            }
                        }
                    }
                }
            }
        );

        return {
            loading,
            rules,
            locations,
            onClose,
            onSubmit,
            locationAdded,
            textEditor,
            onMethodChange,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

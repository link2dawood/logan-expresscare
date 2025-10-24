<template>
    <a-drawer
        :open="visible"
        :closable="true"
        :centered="true"
        :title="pageTitle"
        :width="drawerWidth"
        @close="onClose"
    >
        <a-form layout="vertical" id="add_edit_asset_form">
            <a-tabs v-model:activeKey="addEditActiveTab">
                <a-tab-pane key="asset" :tab="$t('asset.asset_details')">
                    <a-row>
                        <a-col :xs="24" :sm="24" :md="6" :lg="6">
                            <a-form-item
                                :label="$t('asset.image')"
                                name="image"
                                :help="rules.image ? rules.image.message : null"
                                :validateStatus="rules.image ? 'error' : null"
                            >
                                <Upload
                                    :formData="formData"
                                    folder="assets"
                                    imageField="image"
                                    @onFileUploaded="
                                        (file) => {
                                            formData.image = file.file;
                                            formData.image_url = file.file_url;
                                        }
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="18" :lg="18">
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                    <a-form-item
                                        :label="$t('asset.name')"
                                        name="name"
                                        :help="rules.name ? rules.name.message : null"
                                        :validateStatus="rules.name ? 'error' : null"
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.name"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('asset.name'),
                                                ])
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                    <a-form-item
                                        :label="$t('asset.asset_type_id')"
                                        name="asset_type_id"
                                        :help="
                                            rules.asset_type_id
                                                ? rules.asset_type_id.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.asset_type_id ? 'error' : null
                                        "
                                        class="required"
                                    >
                                        <span style="display: flex">
                                            <a-select
                                                v-model:value="formData.asset_type_id"
                                                :placeholder="
                                                    $t('common.select_default_text', [
                                                        $t('asset.asset_type_id'),
                                                    ])
                                                "
                                                :allowClear="true"
                                                optionFilterProp="title"
                                                show-search
                                            >
                                                <a-select-option
                                                    v-for="assetType in assetTypes"
                                                    :key="assetType.xid"
                                                    :value="assetType.xid"
                                                    :title="assetType.name"
                                                >
                                                    {{ assetType.name }}
                                                </a-select-option>
                                            </a-select>
                                            <AssetTypeAddButton
                                                @onAddSuccess="assetTypeAdded"
                                            />
                                        </span>
                                    </a-form-item>
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('asset.location_id')"
                                name="location_id"
                                :help="
                                    rules.location_id ? rules.location_id.message : null
                                "
                                :validateStatus="rules.location_id ? 'error' : null"
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.location_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('asset.location_id'),
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
                                :label="$t('asset.serial_number')"
                                name="serial_number"
                                :help="
                                    rules.serial_number
                                        ? rules.serial_number.message
                                        : null
                                "
                                :validateStatus="rules.serial_number ? 'error' : null"
                            >
                                <a-input
                                    v-model:value="formData.serial_number"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('asset.serial_number'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('asset.description')"
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
                                            $t('asset.description'),
                                        ])
                                    "
                                    :rows="4"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('asset.status')"
                                name="status"
                                :help="rules.status ? rules.status.message : null"
                                :validateStatus="rules.status ? 'error' : null"
                            >
                                <a-radio-group
                                    v-model:value="formData.status"
                                    button-style="solid"
                                    size="small"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('asset.status'),
                                        ])
                                    "
                                    :disabled="data.user != null"
                                >
                                    <a-radio-button value="working">{{
                                        $t("common.working")
                                    }}</a-radio-button>
                                    <a-radio-button value="not_working">{{
                                        $t("common.not_working")
                                    }}</a-radio-button>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane
                    key="account"
                    :tab="$t('asset.transaction_details')"
                    force-render
                >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('asset.account_number')"
                                name="account_id"
                                :help="rules.account_id ? rules.account_id.message : null"
                                :validateStatus="rules.account_id ? 'error' : null"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.account_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('asset.account_number'),
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
                                    <AccountAddButton @onAddSuccess="accountAdded" />
                                </span>
                            </a-form-item>
                        </a-col>

                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('asset.purchase_date')"
                                name="purchase_date"
                                :help="
                                    rules.purchase_date
                                        ? rules.purchase_date.message
                                        : null
                                "
                                :validateStatus="rules.purchase_date ? 'error' : null"
                            >
                                <DateTimePicker
                                    :dateTime="formData.purchase_date"
                                    @dateTimeChanged="
                                        (changedDateTime) =>
                                            (formData.purchase_date = changedDateTime)
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('asset.price')"
                                name="price"
                                :help="rules.price ? rules.price.message : null"
                                :validateStatus="rules.price ? 'error' : null"
                            >
                                <a-input-number
                                    v-model:value="formData.price"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('asset.price'),
                                        ])
                                    "
                                    min="0"
                                    style="width: 100%"
                                >
                                    <template #addonBefore>
                                        {{ appSetting.currency.symbol }}
                                    </template>
                                </a-input-number>
                            </a-form-item>
                        </a-col>
                    </a-row>
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
import { defineComponent, onMounted, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import LocationAddButton from "../../../views/settings/location/AddButton.vue";
import AssetTypeAddButton from "../asset-type/AddButton.vue";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import common from "../../../../common/composable/common";
import DateTimePicker from "@/common/components/common/calendar/DateTimePicker.vue";
import AccountAddButton from "../../accountings/accounts/AddButton.vue";

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
        Upload,
        AssetTypeAddButton,
        DateTimePicker,
        AccountAddButton,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules, addEditActiveTab } = apiAdmin(
            "asset"
        );
        const { appSetting } = common();
        const locations = ref([]);
        const locationUrl = "locations";
        const assetTypes = ref([]);
        const assetTypeUrl = "asset-types";
        const accounts = ref([]);
        const accountUrl = "accounts";

        onMounted(() => {
            const locationPromise = axiosAdmin.get(locationUrl);
            const assetTypePromise = axiosAdmin.get(assetTypeUrl);
            const accountPromise = axiosAdmin.get(accountUrl);
            Promise.all([locationPromise, assetTypePromise, accountPromise]).then(
                ([locationResponse, assetTypeResponse, accountResponse]) => {
                    locations.value = locationResponse.data;
                    assetTypes.value = assetTypeResponse.data;
                    accounts.value = accountResponse.data;
                }
            );
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                id: "add_edit_asset_form",
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

        const locationAdded = (xid) => {
            axiosAdmin.get(locationUrl).then((response) => {
                locations.value = response.data;
                emit("addListSuccess", { type: "location_id", id: xid });
            });
        };

        const assetTypeAdded = (xid) => {
            axiosAdmin.get(assetTypeUrl).then((response) => {
                assetTypes.value = response.data;
                emit("addListSuccess", { type: "asset_type_id", id: xid });
            });
        };

        const accountAdded = (xid) => {
            axiosAdmin.get(accountUrl).then((response) => {
                accounts.value = response.data;
                emit("addListSuccess", { type: "account_id", id: xid });
            });
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    addEditActiveTab.value = "asset";
                }
            }
        );

        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,

            locationAdded,
            assetTypeAdded,
            accountAdded,
            locations,
            assetTypes,
            accounts,
            addEditActiveTab,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

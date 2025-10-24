import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const url =
        "assets?fields=id,xid,name,serial_number,purchase_date,price,image,image_url,description,status,asset_user_id,x_asset_user_id,location_id,x_location_id,location{id,xid,name}user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},asset_type_id,x_asset_type_id,assetType{id,xid,name},return{id,xid,lent_to,x_lent_to,lend_date,return_date,notes,lent_by,x_lent_by,return_by,x_return_by,actual_return_date},assetUser{id,xid,lent_to,x_lent_to,lend_date,return_date,notes,lent_by,x_lent_by,return_by,x_return_by,actual_return_date},assetUser:user{id,xid,name,profile_image,profile_image_url},assetUser:returnBy{id,xid,name,profile_image,profile_image_url},assetUser:lentBy{id,xid,name,profile_image,profile_image_url},account_id,x_account_id,account{id,xid,account_number},brokenBy{id,xid,name},user:designation{id,xid,name},user:location{id,xid,name}";
    const addEditUrl = "assets";
    const hashableColumns = [
        "location_id",
        "user_id",
        "asset_type_id",
        "asset_user_id",
        "account_id",
    ];
    const { t } = useI18n();
    const { dayjs } = common();

    const initData = {
        name: "",
        serial_number: "",
        location_id: undefined,
        image: undefined,
        image_url: undefined,
        description: "",
        status: "working",
        asset_type_id: undefined,
        account_id: undefined,
        purchase_date: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        price: "0",
        broken_by: undefined,
    };

    const recordViewColumns = [
        {
            title: t("user.name"),
            dataIndex: "name",
        },

        {
            title: t("asset.lend_date"),
            dataIndex: "lend_date",
        },
        {
            title: t("asset.lend_by"),
            dataIndex: "lend_by",
        },
        {
            title: t("asset.return_date"),
            dataIndex: "return_date",
        },
        {
            title: t("asset.return_by"),
            dataIndex: "return_by",
        },
        {
            title: t("asset.actual_return_date"),
            dataIndex: "actual_return_date",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const columns = [
        {
            title: t("asset.name"),
            dataIndex: "name",
        },
        {
            title: t("asset.asset_type_id"),
            dataIndex: "asset_type_id",
        },
        {
            title: t("asset.image"),
            dataIndex: "image",
        },
        {
            title: t("asset.lent_to"),
            dataIndex: "user_id",
        },
        {
            title: t("asset.location_id"),
            dataIndex: "location",
        },
        {
            title: t("asset.serial_number"),
            dataIndex: "serial_number",
        },
        {
            title: t("asset.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("asset.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        recordViewColumns,
    };
};

export default fields;

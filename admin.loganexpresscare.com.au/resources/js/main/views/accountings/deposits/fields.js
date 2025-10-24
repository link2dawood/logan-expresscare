import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const url =
        "deposits?fields=id,xid,account_id,x_account_id,amount,date_time,deposit_category_id,x_deposit_category_id,file,file_url,payer_id,x_payer_id,reference_number,notes,account{id,xid,name,balance},payer{id,xid,name},depositCategory{id,xid,name}";
    const addEditUrl = "deposits";
    const hashableColumns = ["account_id", "payer_id", "deposit_category_id"];
    const { t } = useI18n();
    const { dayjs } = common();

    const initData = {
        account_id: undefined,
        amount: "",
        date_time: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        payer_id: undefined,
        reference_number: "",
        file: undefined,
        file_url: undefined,
        notes: "",
        deposit_category_id: undefined,
    };

    const columns = [
        {
            title: t("deposit.account"),
            dataIndex: "account_id",
        },
        {
            title: t("deposit.amount"),
            dataIndex: "amount",
        },
        {
            title: t("deposit.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("deposit.deposit_category"),
            dataIndex: "deposit_category_id",
        },
        {
            title: t("deposit.payer"),
            dataIndex: "payer_id",
        },
        {
            title: t("deposit.reference_number"),
            dataIndex: "reference_number",
        },
        {
            title: t("deposit.file"),
            dataIndex: "file",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "amount",
            value: t("deposit.amount"),
        },
        {
            key: "account_id",
            value: t("deposit.account"),
        },
        {
            key: "payer_id",
            value: t("deposit.payer"),
        },
        {
            key: "deposit_category_id",
            value: t("deposit.deposit_category"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;

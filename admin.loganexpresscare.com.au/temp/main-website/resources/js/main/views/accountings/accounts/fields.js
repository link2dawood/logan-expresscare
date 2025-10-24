import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "accounts?fields=id,xid,name,initial_balance,account_number,branch_code,branch_address,balance";
    const addEditUrl = "accounts";
    const { t } = useI18n();

    const initData = {
        name: "",
        initial_balance: "",
        account_number: "",
        branch_code: "",
        branch_address: "",
        balance: "",
    };

    const columns = [
        {
            title: t("account.name"),
            dataIndex: "name",
        },
        {
            title: t("account.initial_balance"),
            dataIndex: "initial_balance",
        },
        {
            title: t("account.account_number"),
            dataIndex: "account_number",
        },
        {
            title: t("account.branch_code"),
            dataIndex: "branch_code",
        },
        {
            title: t("account.available_balance"),
            dataIndex: "balance",
        },
        {
            title: t("account.branch_address"),
            dataIndex: "branch_address",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const transactionColumns = [
        {
            title: t("account.name"),
            dataIndex: "name",
        },
        {
            title: t("account.account_number"),
            dataIndex: "account_number",
        },
        {
            title: t("account.type"),
            dataIndex: "type",
        },
        {
            title: t("account.date"),
            dataIndex: "date",
        },
        {
            title: t("account.amount"),
            dataIndex: "amount",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("account.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        transactionColumns,
    };
};

export default fields;

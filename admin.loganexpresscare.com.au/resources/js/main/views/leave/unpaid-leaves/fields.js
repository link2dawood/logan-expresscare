import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "leave-types?fields=id,xid,name,is_paid,total_leaves";
    const addEditUrl = "leave-types";
    const { t } = useI18n();
    const hashableColumns = [];

    const initData = {
        name: "",
        total_leaves: "",
        is_paid: 0,
    };

    const columns = [
        {
            title: t("common.month"),
            dataIndex: "month",
        },
        {
            title: t("menu.unpaid_leaves"),
            dataIndex: "unpaid_leaves",
        },
    ];

    const filterableColumns = [];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;

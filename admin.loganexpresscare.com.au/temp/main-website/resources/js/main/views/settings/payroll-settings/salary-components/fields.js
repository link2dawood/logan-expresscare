import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "salary-components?fields=id,xid,name,type,value_type,bi_weekly,weekly,monthly,semi_monthly";
    const addEditUrl = "salary-components";
    const { t } = useI18n();

    const initData = {
        name: "",
        type: undefined,
        value_type: undefined,
        bi_weekly: "",
        weekly: "",
        monthly: "",
        semi_monthly: "",
    };

    const columns = [
        {
            title: t("salary_component.name"),
            dataIndex: "name",
        },
        {
            title: t("salary_component.type"),
            dataIndex: "type",
        },
        {
            title: t("salary_component.value_type"),
            dataIndex: "value_type",
        },
        {
            title: t("salary_component.value"),
            dataIndex: "value",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("salary_component.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    };
};

export default fields;

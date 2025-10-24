import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "self/company-policies?fields=id,xid,location_id,x_location_id,title,method_type,letter_description,policy_document,policy_document_url,location{id,xid,name},description";
    const addEditUrl = "company-policies";
    const { t } = useI18n();
    const hashableColumns = ["location_id"];

    const initData = {};

    const columns = [
        {
            title: t("company_policy.title"),
            dataIndex: "title",
        },
        {
            title: t("company_policy.location"),
            dataIndex: "location_id",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("common.title"),
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
